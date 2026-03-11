<?php

namespace App\Http\Resources;

use App\Models\Banner;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BannerResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $placement = $this->placement ?? Banner::PLACEMENT_HOMEPAGE;

        return [
            '_id' => (string) $this->id,
            'id' => $this->id,
            'title' => $this->title,
            'image' => $this->thumbnail,
            'thumbnail' => $this->thumbnail,
            'redirectUrl' => $this->redirect_url,
            'redirect_url' => $this->redirect_url,
            'isActive' => (bool) $this->status,
            'placement' => $placement,
            'createdAt' => optional($this->created_at)?->toIso8601String(),
            'updatedAt' => optional($this->updated_at)?->toIso8601String(),
        ];
    }
}
