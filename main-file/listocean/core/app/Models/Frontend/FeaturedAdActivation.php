<?php

namespace App\Models\Frontend;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Columns: id, purchase_id, listing_id, starts_at, ends_at, is_active,
 *          created_at, updated_at
 */
class FeaturedAdActivation extends Model
{
    use HasFactory;

    protected $fillable = [
        'purchase_id', 'listing_id', 'starts_at', 'ends_at', 'is_active',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at'   => 'datetime',
        'is_active' => 'boolean',
    ];

    public function purchase()
    {
        return $this->belongsTo(FeaturedAdPurchase::class, 'purchase_id');
    }

    public function isExpired(): bool
    {
        return $this->ends_at !== null && $this->ends_at->isPast();
    }

    public function isRunning(): bool
    {
        return $this->is_active && !$this->isExpired();
    }
}
