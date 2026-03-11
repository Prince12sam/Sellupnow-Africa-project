<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class VerificationController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'idProof' => 'nullable|string|max:100',
            'id_proof_type' => 'nullable|string|max:100',
            'identification_type' => 'nullable|string|max:100',
            'id_proof_number' => 'nullable|string|max:100',
            'identification_number' => 'nullable|string|max:100',
            'idProofFront' => 'nullable|file|mimes:jpg,png,jpeg,webp,pdf|max:10240',
            'front_image' => 'nullable|file|mimes:jpg,png,jpeg,webp,pdf|max:10240',
            'front_document' => 'nullable|file|mimes:jpg,png,jpeg,webp,pdf|max:10240',
            'idProofBack' => 'nullable|file|mimes:jpg,png,jpeg,webp,pdf|max:10240',
            'back_image' => 'nullable|file|mimes:jpg,png,jpeg,webp,pdf|max:10240',
            'back_document' => 'nullable|file|mimes:jpg,png,jpeg,webp,pdf|max:10240',
            'selfie' => 'nullable|file|mimes:jpg,png,jpeg,webp|max:10240',
            'selfie_image' => 'nullable|file|mimes:jpg,png,jpeg,webp|max:10240',
        ]);

        $userId = auth('api')->id();
        $idProofType = trim((string) (
            $request->input('idProof')
            ?? $request->input('id_proof_type')
            ?? $request->input('identification_type')
            ?? ''
        ));

        if ($idProofType === '') {
            return response()->json([
                'status' => false,
                'message' => 'The id proof field is required.',
                'errors' => [
                    'idProof' => ['The id proof field is required.'],
                ],
            ], 422);
        }

        $idProofNumber = trim((string) (
            $request->input('id_proof_number')
            ?? $request->input('identification_number')
            ?? ''
        ));

        $frontImage = $request->file('idProofFront')
            ?? $request->file('front_image')
            ?? $request->file('front_document');
        $backImage = $request->file('idProofBack')
            ?? $request->file('back_image')
            ?? $request->file('back_document');
        $selfieImage = $request->file('selfie')
            ?? $request->file('selfie_image');

        $db = DB::connection();
        $existingVerification = $db->table('identity_verifications')
            ->where('user_id', $userId)
            ->orderByDesc('id')
            ->first();

        if (! $frontImage && ! $existingVerification?->front_document) {
            return response()->json([
                'status' => false,
                'message' => 'The front verification document is required.',
                'errors' => [
                    'idProofFront' => ['The front verification document is required.'],
                ],
            ], 422);
        }

        $uploadDirectory = public_path('assets/uploads/verification');
        if (! File::exists($uploadDirectory)) {
            File::makeDirectory($uploadDirectory, 0755, true);
        }

        $frontDocument = $frontImage
            ? $this->storeVerificationFile($frontImage, $uploadDirectory, $existingVerification?->front_document)
            : $existingVerification?->front_document;
        $backDocument = $backImage
            ? $this->storeVerificationFile($backImage, $uploadDirectory, $existingVerification?->back_document)
            : $existingVerification?->back_document;
        $selfiePhoto = $selfieImage
            ? $this->storeVerificationFile($selfieImage, $uploadDirectory, $existingVerification?->selfie_photo)
            : $existingVerification?->selfie_photo;

        $timestamp = now();
        $payload = [
            'user_id' => $userId,
            'identification_type' => $idProofType,
            'identification_number' => $idProofNumber !== '' ? $idProofNumber : null,
            'front_document' => $frontDocument,
            'back_document' => $backDocument,
            'selfie_photo' => $selfiePhoto,
            'verify_by' => null,
            'decline_reason' => null,
            'status' => 0,
            'updated_at' => $timestamp,
        ];

        if ($existingVerification) {
            $db->table('identity_verifications')
                ->where('id', $existingVerification->id)
                ->update($payload);

            $verificationId = (int) $existingVerification->id;
        } else {
            $verificationId = (int) $db->table('identity_verifications')->insertGetId([
                ...$payload,
                'created_at' => $timestamp,
            ]);
        }

        $verification = $db->table('identity_verifications')->where('id', $verificationId)->first();
        $customerWebUrl = rtrim((string) env('CUSTOMER_WEB_URL', config('app.url')), '/');
    $createdAt = Carbon::parse($verification->created_at ?? $timestamp);
    $updatedAt = Carbon::parse($verification->updated_at ?? $timestamp);

        return response()->json([
            'status' => true,
            'message' => 'Verification submitted successfully',
            'data' => [
                'uniqueId' => (string) $verificationId,
                'user' => (string) $userId,
                'idProof' => $verification->identification_type,
                'idProofFrontUrl' => $verification->front_document
                    ? $customerWebUrl.'/assets/uploads/verification/'.ltrim((string) $verification->front_document, '/')
                    : '',
                'idProofBackUrl' => $verification->back_document
                    ? $customerWebUrl.'/assets/uploads/verification/'.ltrim((string) $verification->back_document, '/')
                    : '',
                'reason' => (string) ($verification->decline_reason ?? ''),
                'status' => (int) ($verification->status ?? 0),
                'submittedAt' => $createdAt.toIso8601String(),
                '_id' => (string) $verificationId,
                'createdAt' => $createdAt.toIso8601String(),
                'updatedAt' => $updatedAt.toIso8601String(),
            ],
        ]);
    }

    private function storeVerificationFile($file, string $uploadDirectory, ?string $existingFileName = null): string
    {
        if ($existingFileName) {
            $existingPath = $uploadDirectory.DIRECTORY_SEPARATOR.$existingFileName;
            if (File::exists($existingPath)) {
                File::delete($existingPath);
            }
        }

        $fileName = time().'-'.uniqid().'.'.$file->getClientOriginalExtension();
        $file->move($uploadDirectory, $fileName);

        return $fileName;
    }
}
