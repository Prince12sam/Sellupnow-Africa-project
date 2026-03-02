<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ListingResource extends JsonResource
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
            'thumbnail' => $this->thumbnail,
            'price' => (float) number_format($price, 2, '.', ''),
            'discount_price' => 0.0,
            'discount_percentage' => 0.0,
            'rating' => 0.0,
            'total_reviews' => '0',
            'total_sold' => '0',
            'quantity' => 1,
            'is_favorite' => $isFavorite,
            'favorites_count' => (int) ($this->favorites_count ?? 0),
            'category' => $this->category?->name,
            'country_id' => $this->country_id,
            'state_id' => null,
            'city_id' => null,
            'is_negotiable' => (bool) $this->negotiable,
            'is_featured' => false,
            'view' => (int) $this->view,
            'published_at' => $this->published_at,
        ];
    }
}
