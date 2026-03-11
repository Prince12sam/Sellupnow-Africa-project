<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $accountVerified = false;
        if ($this->phone_verified_at || $this->email_verified_at) {
            $accountVerified = true;
        }

        $identityVerification = null;
        try {
            $identityVerification = DB::table('identity_verifications')
                ->where('user_id', $this->id)
                ->orderByDesc('id')
                ->first(['id', 'status', 'created_at', 'updated_at', 'decline_reason']);
        } catch (\Throwable $_) {
            $identityVerification = null;
        }

        $identityStatus = null;
        if ($identityVerification) {
            $rawStatus = $identityVerification->status;
            if ($rawStatus === null) {
                $identityStatus = 0;
            } elseif (is_numeric($rawStatus)) {
                $identityStatus = (int) $rawStatus;
            } elseif (is_string($rawStatus)) {
                $normalizedStatus = strtolower(trim($rawStatus));
                $identityStatus = match ($normalizedStatus) {
                    'approved' => 1,
                    'declined', 'rejected' => 2,
                    'pending' => 0,
                    default => null,
                };
            }
        }

        $isIdentityVerified = (bool) ($this->verified_status ?? false);
        if ($isIdentityVerified) {
            $identityStatus = 1;
        }

        $hasPendingIdentityVerification = $identityVerification !== null
            && $identityStatus === 0
            && ! $isIdentityVerified;

        $identityStatusLabel = match ($identityStatus) {
            0 => 'pending',
            1 => 'approved',
            2 => 'declined',
            default => null,
        };

        $identitySubmittedAt = null;
        if ($identityVerification && ! empty($identityVerification->created_at)) {
            try {
                $identitySubmittedAt = Carbon::parse($identityVerification->created_at)->toIso8601String();
            } catch (\Throwable $_) {
                $identitySubmittedAt = (string) $identityVerification->created_at;
            }
        }

        $lastOnline = $this->last_online >= now() ? true : false;

        // Resolve profile image URL for Flutter (maps to profileImage / thumbnail)
        $profileImage = null;
        if ($this->thumbnail) {
            $profileImage = is_string($this->thumbnail) ? $this->thumbnail : null;
        }

        return [
            // Core identity  (Flutter reads '_id' for the DB primary key)
            '_id'           => (string) $this->id,
            'id'            => $this->id,

            // Firebase UID — Flutter stores this as loginUserFirebaseId
            'firebaseUid'   => $this->firebase_uid,

            // Profile fields
            'name'          => $this->name,
            'phoneNumber'   => $this->phone,
            'phone'         => $this->phone,
            'email'         => $this->email,
            'profileImage'  => $profileImage,
            'profile_photo' => $this->thumbnail,
            'gender'        => $this->gender,
            'date_of_birth' => $this->date_of_birth,
            'country'       => $this->country,
            'phone_code'    => $this->phone_code,
            'address'       => $this->address ?? null,

            // Auth meta
            'loginType'     => $this->resolveLoginType(),
            'authProvider'  => $this->auth_type,
            'authIdentity'  => $this->auth_id,
            'fcmToken'      => $this->devices()->latest()->value('key'),

            // Verification & status
            'phone_verified'   => (bool) $this->phone_verified_at,
            'email_verified'   => (bool) $this->email_verified_at,
            'account_verified' => (bool) $accountVerified,
            'is_active'        => (bool) $this->is_active,
            'isVerified'       => $isIdentityVerified,
            'isBlocked'        => ! (bool) $this->is_active,
            'isOnline'         => $lastOnline,
            'last_online'      => $lastOnline,
            'verificationStatus' => $identityStatus,
            'verificationStatusLabel' => $identityStatusLabel,
            'hasPendingVerification' => $hasPendingIdentityVerification,
            'verificationId' => $identityVerification ? (string) $identityVerification->id : null,
            'verificationSubmittedAt' => $identitySubmittedAt,
            'verificationDeclineReason' => $identityVerification->decline_reason ?? null,

            // Permissions
            'isNotificationsAllowed' => (bool) $this->is_notifications_allowed,
            'isContactInfoVisible'   => (bool) $this->is_contact_info_visible,

            // Timestamps
            'createdAt'     => optional($this->created_at)->toIso8601String(),
            'updatedAt'     => optional($this->updated_at)->toIso8601String(),
        ];
    }

    /**
     * Determine the loginType value for Flutter.
     *
     * 1 = phone OTP login (phone is the auth identity, non-editable)
     * 2 = social / Google login (email is non-editable)
     * 4 = email + password login (phone is editable)
     */
    private function resolveLoginType(): int
    {
        // Social auth (Google, Facebook, Apple)
        if ($this->auth_type) {
            return 2;
        }

        // Phone-verified login (phone OTP)
        if ($this->phone_verified_at && ! $this->email_verified_at) {
            return 1;
        }

        // Email + password (default for most users)
        if ($this->password) {
            return 4;
        }

        return 0;
    }
}
