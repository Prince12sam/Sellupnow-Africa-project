<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

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
            'loginType'     => $this->auth_type ? 2 : 1,
            'authProvider'  => $this->auth_type,
            'authIdentity'  => $this->auth_id,
            'fcmToken'      => $this->devices()->latest()->value('key'),

            // Verification & status
            'phone_verified'   => (bool) $this->phone_verified_at,
            'email_verified'   => (bool) $this->email_verified_at,
            'account_verified' => (bool) $accountVerified,
            'is_active'        => (bool) $this->is_active,
            'isVerified'       => (bool) $accountVerified,
            'isBlocked'        => ! (bool) $this->is_active,
            'isOnline'         => $lastOnline,
            'last_online'      => $lastOnline,

            // Timestamps
            'createdAt'     => optional($this->created_at)->toIso8601String(),
            'updatedAt'     => optional($this->updated_at)->toIso8601String(),
        ];
    }
}
