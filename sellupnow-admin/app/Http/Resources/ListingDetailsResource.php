<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ListingDetailsResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $currencyRate = (float) ($request->currencyData['rate'] ?? 1);
        $price = (float) $this->price * $currencyRate;
        $isFavorite = $this->relationLoaded('favorites') ? $this->favorites->isNotEmpty() : false;

        return [
            'id' => $this->id,
            'listing_id' => $this->id,
            'title' => $this->title,
            'name' => $this->title,
            'slug' => $this->slug,
            'description' => $this->description,
            'thumbnail' => $this->thumbnail,
            'image' => $this->thumbnail,
            'gallery_images' => $this->gallery_images,
            'price' => (float) number_format($price, 2, '.', ''),
            'discount_price' => 0.0,
            'discount_percentage' => 0.0,
            'rating' => 0.0,
            'total_reviews' => '0',
            'total_sold' => '0',
            'quantity' => 1,
            'is_favorite' => $isFavorite,
            'favorites_count' => (int) ($this->favorites_count ?? 0),
            'phone' => $this->phone,
            'address' => $this->address,
            'lat' => $this->lat,
            'lon' => $this->lon,
            'category' => $this->category?->name,
            'sub_category' => $this->subCategory?->name,
            'child_category' => $this->childCategory?->name,
            'country_id' => $this->country_id,
            'is_negotiable' => (bool) $this->negotiable,
            'is_featured' => false,
            'view' => (int) $this->view,
            'published_at' => $this->published_at,
            'user' => [
                'id' => $this->user?->id,
                'name' => $this->user?->name,
                'email' => $this->user?->email,
                'thumbnail' => $this->user?->thumbnail,
            ],
        ];
    }
}
