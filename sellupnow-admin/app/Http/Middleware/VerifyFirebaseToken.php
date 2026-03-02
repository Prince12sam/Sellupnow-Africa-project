<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Kreait\Firebase\Factory;
use Symfony\Component\HttpFoundation\Response;

class VerifyFirebaseToken
{
    /**
     * Verify the Firebase ID token sent by the Flutter app as
     * "Authorization: Bearer {firebase_id_token}" and resolve the
     * authenticated user from the local database.
     *
     * The token is a short-lived (~1 hour) JWT issued by Firebase Auth.
     * The Flutter SDK auto-refreshes it via FirebaseAccessToken.onGet().
     *
     * Required: storage/app/public/firebase_credentials.json (Admin SDK
     *           service-account file — uploaded via /admin/firebase).
     */
    public function handle(Request $request, Closure $next): Response
    {
        // ── 1. Extract the Bearer token ──────────────────────────────────────
        $authHeader = $request->header('Authorization', '');
        $idToken = null;

        if (str_starts_with($authHeader, 'Bearer ')) {
            $idToken = substr($authHeader, 7);
        }

        if (empty($idToken)) {
            return response()->json([
                'status'  => false,
                'message' => 'Unauthenticated. No token provided.',
            ], 401);
        }

        // ── 2. Verify the token via Firebase Admin SDK ───────────────────────
        $credPath = env('FIREBASE_CREDENTIALS', storage_path('app/public/firebase_credentials.json'));

        if (! file_exists($credPath)) {
            return response()->json([
                'status'  => false,
                'message' => 'Firebase not configured on the server.',
            ], 500);
        }

        try {
            $auth          = (new Factory)->withServiceAccount($credPath)->createAuth();
            $verifiedToken = $auth->verifyIdToken($idToken);
            $firebaseUid   = $verifiedToken->claims()->get('sub'); // Firebase UID
        } catch (\Throwable $e) {
            return response()->json([
                'status'  => false,
                'message' => 'Invalid or expired Firebase token.',
            ], 401);
        }

        // ── 3. Resolve the user from the local database ───────────────────────
        $user = User::where('firebase_uid', $firebaseUid)->first();

        if (! $user) {
            return response()->json([
                'status'  => false,
                'message' => 'User not found. Please login again.',
            ], 401);
        }

        // ── 4. Set the authenticated user so auth()->user() works everywhere ──
        Auth::setUser($user);
        $request->setUserResolver(fn () => $user);

        return $next($request);
    }
}
