<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuctionBid extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'bid_at' => 'datetime',
    ];

    public function listing(): BelongsTo
    {
        return $this->belongsTo(Listing::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope: only bids in a given listing, ordered highest first.
     */
    public function scopeForListing($query, int $listingId)
    {
        return $query->where('listing_id', $listingId)->orderByDesc('amount');
    }
}
