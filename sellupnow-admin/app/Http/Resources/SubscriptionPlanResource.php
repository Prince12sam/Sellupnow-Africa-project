<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SubscriptionPlanResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            '_id'            => (string) $this->id,
            'name'           => $this->name,
            'price'          => (float) $this->price,
            'discount'       => 0,
            'finalPrice'     => (float) $this->price,
            'image'          => null,
            'description'    => $this->description,
            // 'days.value' is displayed as "X Ads Listing Free" in the Flutter UI
            'days'           => [
                'isLimited' => (int) $this->listing_quota > 0,
                'value'     => (int) $this->listing_quota,
            ],
            // 'advertisements.value' is displayed as "X Days Free Off Cost Service"
            'advertisements' => [
                'isLimited' => true,
                'value'     => (int) $this->duration_days,
            ],
            'isActive'       => (bool) $this->is_active,
            'isPopular'      => (int) $this->sort_order === 1,
            'createdAt'      => optional($this->created_at)->toISOString(),
            'updatedAt'      => optional($this->updated_at)->toISOString(),
        ];
    }
}
