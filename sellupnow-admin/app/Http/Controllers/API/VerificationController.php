<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\UserVerification;
use Illuminate\Http\Request;

class VerificationController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'id_proof_type'   => 'required|string|max:100',
            'id_proof_number' => 'nullable|string|max:100',
            'front_image'     => 'required|image|max:5120',
            'back_image'      => 'nullable|image|max:5120',
            'selfie_image'    => 'nullable|image|max:5120',
        ]);

        $userId = auth('api')->id();

        // Store uploaded images
        $data['front_image']  = $request->file('front_image')->store('verifications', 'public');
        if ($request->hasFile('back_image')) {
            $data['back_image'] = $request->file('back_image')->store('verifications', 'public');
        }
        if ($request->hasFile('selfie_image')) {
            $data['selfie_image'] = $request->file('selfie_image')->store('verifications', 'public');
        }

        // Cancel any previous pending submission
        UserVerification::where('user_id', $userId)->where('status', 'pending')->delete();

        $verification = UserVerification::create([
            'user_id'         => $userId,
            'id_proof_type'   => $data['id_proof_type'],
            'id_proof_number' => $data['id_proof_number'] ?? null,
            'front_image'     => $data['front_image'],
            'back_image'      => $data['back_image'] ?? null,
            'selfie_image'    => $data['selfie_image'] ?? null,
            'status'          => 'pending',
        ]);

        return $this->json('Verification submitted successfully', [
            'verification_id' => $verification->id,
            'status'          => $verification->status,
        ]);
    }
}
