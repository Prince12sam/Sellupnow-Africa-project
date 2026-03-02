<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\ChangePasswordRequest;
use App\Http\Requests\UserRequest;
use App\Http\Resources\UserResource;
use App\Models\DeviceKey;
use App\Repositories\UserRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Returns the user profile.
     *
     * @return mixed
     */
    public function index()
    {
        $user = auth()->user();

        return $this->json('profile details', [
            'user' => UserResource::make($user),
        ]);
    }

    /**
     * Updates the user profile.
     *
     * @param  UserRequest  $request  The request object containing the updated user data.
     */
    public function update(UserRequest $request)
    {
        $user = UserRepository::updateByRequest($request, auth()->user());
        $user->refresh();

        return $this->json('Profile updated successfully', [
            'user' => UserResource::make($user),
        ]);
    }

    /**
     * Change the user's password.
     *
     * @param  ChangePasswordRequest  $request  The request object containing the new password.
     * @return string The success message.
     *
     * @throws Some_Exception_Class If the current password does not match.
     */
    public function changePassword(ChangePasswordRequest $request)
    {
        /** @var User $user */
        $user = auth()->user();

        if (! Hash::check($request->current_password, $user->password)) {
            return $this->json('Current password does not match', [], 422);
        }
        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return $this->json('Password changed successfully');
    }

    public function deactivateAccount(Request $request)
    {
        $user = auth('api')->user();
        if (! $user) {
            return $this->json('Unauthenticated', [], 401);
        }

        // Revoke all Passport tokens so the user is immediately signed out
        $user->tokens()->delete();
        $user->update(['status' => 0]);

        return $this->json('Account deactivated successfully');
    }

    public function managePermission(Request $request)
    {
        $data = $request->validate([
            'fcm_token'   => 'required|string',
            'device_type' => 'nullable|string|max:50',
        ]);

        $user = auth('api')->user();

        DeviceKey::updateOrCreate(
            ['user_id' => $user->id, 'device_type' => $data['device_type'] ?? 'mobile'],
            ['key' => $data['fcm_token']]
        );

        return $this->json('FCM token registered successfully');
    }
}
