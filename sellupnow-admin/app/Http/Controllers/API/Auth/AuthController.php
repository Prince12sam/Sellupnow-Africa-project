<?php

namespace App\Http\Controllers\API\Auth;

use App\Enums\Roles;
use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegistrationRequest;
use App\Http\Resources\UserResource;
use App\Models\Customer;
use App\Models\DeviceKey;
use App\Models\User;
use App\Models\Wallet;
use App\Repositories\CustomerRepository;
use App\Repositories\DeviceKeyRepository;
use App\Repositories\UserRepository;
use App\Repositories\WalletRepository;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

class AuthController extends Controller
{
    /**
     * Register a new user and return the registration result.
     *
     * @param  RegistrationRequest  $request  The registration request data
     * @return Some_Return_Value The registration result data
     */
    public function register(RegistrationRequest $request)
    {
        // Create a new user
        $oldUser = UserRepository::query()->where('phone', $request->phone)->orWhere('email', $request->email)->first();
        if ($oldUser) {
            $user = UserRepository::registerGuestUser($request, $oldUser);

            if (! $user->customer) {
                Customer::query()->firstOrCreate(['user_id' => $user->id]);
            }

            if (! $user->wallet) {
                Wallet::query()->firstOrCreate(
                    ['user_id' => $user->id],
                    ['balance' => 0]
                );
            }

            $this->assignCustomerRole($user);

            return $this->json('Registration successfully complete', [
                'user' => new UserResource($user),
                'access' => UserRepository::getAccessToken($user),
            ]);
        }
        $user = UserRepository::registerNewUser($request);

        if ($request->device_key) {
            DeviceKeyRepository::storeByRequest($user, $request);
        }

        // Create a new customer
        CustomerRepository::storeByRequest($user);

        // create wallet
        WalletRepository::storeByRequest($user);

        $this->assignCustomerRole($user);

        return $this->json('Registration successfully complete', [
            'user' => new UserResource($user),
            'access' => UserRepository::getAccessToken($user),
        ]);
    }

    /**
     * Login a user — handles Flutter mobile login types AND legacy web path.
     *
     * loginType 1  → phone (Firebase phone OTP). Firebase token in header is
     *                verified; user is found/created by phone number.
     * loginType 2  → email + password. Traditional credential check.
     * (no loginType) → legacy path used by the web panel (phone + password).
     */
    public function login(LoginRequest $request)
    {
        $loginType = (int) $request->input('loginType', 0);
        $fcmToken = $request->input('fcmToken');
        $authIdentity = $request->input('authIdentity');
        $signUp = false;

        // loginType 1: Phone (Firebase phone OTP)
        if ($loginType === 1) {
            $user = User::where('phone', $request->input('phoneNumber'))->first();
            if (!$user) {
                return response()->json(['status' => false, 'message' => 'User not found'], 404);
            }
            // No password check, phone verified by Firebase
        }
        // loginType 2: Google (Firebase Google login, passwordless)
        elseif ($loginType === 2) {
            // Get the real Firebase UID from the verified token in the header
            $firebaseUid = $this->verifyFirebaseIdToken($request)
                         ?? $request->header('x-meta-auth-id')
                         ?? $authIdentity;

            $user = User::where('email', $request->input('email'))
                ->orWhere('firebase_uid', $firebaseUid)
                ->first();
            if (!$user) {
                // Create user if not found (first-time Google sign-in)
                $user = User::create([
                    'email' => $request->input('email'),
                    'name' => $request->input('name', ''),
                    'firebase_uid' => $firebaseUid,
                ]);
                $signUp = true;
            } else {
                // Ensure firebase_uid is set for existing users
                if (empty($user->firebase_uid) || $user->firebase_uid !== $firebaseUid) {
                    $user->firebase_uid = $firebaseUid;
                    $user->save();
                }
            }
            // No password check, Google verified by Firebase
        }
        // loginType 4: Flutter email/password login
        elseif ($loginType === 4) {
            $user = User::where('email', $request->input('email'))->first();
            if (!$user || !Hash::check($request->input('password'), $user->password)) {
                return response()->json(['status' => false, 'message' => 'Invalid credentials'], 401);
            }

            // Ensure user has a firebase_uid for the VerifyFirebaseToken middleware
            if (empty($user->firebase_uid)) {
                $user->firebase_uid = 'email_user_' . $user->id;
                $user->save();
            }
        }
        // Legacy web admin login
        else {
            $user = User::where('phone', $request->input('phone'))->first();
            if (!$user || !Hash::check($request->input('password'), $user->password)) {
                return response()->json(['status' => false, 'message' => 'Invalid credentials'], 401);
            }
        }

        // Attach FCM token if provided
        if ($fcmToken && $user->id) {
            DeviceKey::updateOrCreate(
                ['user_id' => $user->id, 'key' => $fcmToken],
                ['device_type' => 'android']
            );
        }

        // Generate Firebase custom token for loginType 4 (email/password)
        // so Flutter can establish a Firebase session for subsequent API calls
        $firebaseCustomToken = null;
        if ($loginType === 4) {
            $firebaseCustomToken = $this->createFirebaseCustomToken($user->firebase_uid);

            if (! $firebaseCustomToken) {
                return response()->json([
                    'status' => false,
                    'message' => 'Unable to initialize secure session. Please try again.',
                ], 503);
            }
        }

        // Return in the format Flutter expects
        return response()->json([
            'status' => true,
            'message' => 'Login successful',
            'signUp' => $signUp,
            'firebaseCustomToken' => $firebaseCustomToken,
            'user' => (new UserResource($user))->toArray(request()),
        ]);
    }

    /**
     * Authenticate the user and return the user.
     *
     * @param  LoginRequest  $request  The login request
     * @return User|null
     */
    private function authenticate(LoginRequest $request)
    {
        $user = UserRepository::findByPhone($request->phone);
        if (! is_null($user) && Hash::check($request->password, $user->password)) {
            return $user;
        }

        return null;
    }

    /**
     * Verify the Firebase ID token from the Authorization header.
     * Returns the Firebase UID on success or null on failure.
     */
    private function verifyFirebaseIdToken(Request $request): ?string
    {
        $authHeader = $request->header('Authorization', '');
        if (! str_starts_with($authHeader, 'Bearer ')) {
            return null;
        }
        $idToken = substr($authHeader, 7);

        $credPath = (string) config('firebase.projects.app.credentials.file', storage_path('app/firebase_credentials.json'));
        if (! file_exists($credPath)) {
            return null; // Firebase not configured — skip verification in dev
        }

        try {
            $auth          = (new \Kreait\Firebase\Factory)->withServiceAccount($credPath)->createAuth();
            $verifiedToken = $auth->verifyIdToken($idToken);

            return $verifiedToken->claims()->get('sub'); // Firebase UID
        } catch (\Throwable) {
            return null;
        }
    }

    /**
     * Upsert the FCM token for the given user (for push notifications).
     */
    private function storeFcmToken(User $user, ?string $fcmToken): void
    {
        if (! $fcmToken) {
            return;
        }

        \App\Models\DeviceKey::updateOrCreate(
            ['user_id' => $user->id],
            ['key' => $fcmToken, 'device_type' => 'mobile']
        );
    }

    /**
     * Create a Firebase custom token for the given UID.
     * Flutter uses signInWithCustomToken() to establish a Firebase session.
     */
    private function createFirebaseCustomToken(string $firebaseUid): ?string
    {
        $credPath = (string) config('firebase.projects.app.credentials.file', storage_path('app/firebase_credentials.json'));
        if (! file_exists($credPath)) {
            return null;
        }

        try {
            $auth = (new \Kreait\Firebase\Factory)->withServiceAccount($credPath)->createAuth();
            $customToken = $auth->createCustomToken($firebaseUid);

            return $customToken->toString();
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('[AuthController] Failed to create custom token: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Logout the user and revoke the token.
     *
     * @model User $user
     *
     * @return string
     */
    public function logout()
    {
        /** @var \User $user */
        $user = auth()->user();

        if ($user) {
            $user->currentAccessToken()->delete();

            return $this->json('Logged out successfully!');
        }

        return $this->json('User not found!', [], Response::HTTP_NOT_FOUND);
    }

    public function callback(Request $request) {}

    private function assignCustomerRole(User $user): void
    {
        $guardName = config('auth.defaults.guard', 'web');
        Role::findOrCreate(Roles::CUSTOMER->value, $guardName);

        if (! $user->hasRole(Roles::CUSTOMER->value)) {
            $user->assignRole(Roles::CUSTOMER->value);
        }
    }
}
