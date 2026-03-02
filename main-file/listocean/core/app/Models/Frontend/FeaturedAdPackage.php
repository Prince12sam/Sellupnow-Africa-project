<?php

namespace App\Models\Frontend;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Columns: id, name, description, price, currency, duration_days,
 *          advertisement_limit, position, max_listings, is_active,
 *          sort_order, created_at, updated_at
 */
class FeaturedAdPackage extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'description', 'price', 'currency', 'duration_days',
        'advertisement_limit', 'position', 'max_listings', 'is_active', 'sort_order',
    ];

    protected $casts = [
        'price'               => 'decimal:2',
        'is_active'           => 'boolean',
        'duration_days'       => 'integer',
        'advertisement_limit' => 'integer',
        'max_listings'        => 'integer',
        'sort_order'          => 'integer',
    ];

    public function purchases()
    {
        return $this->hasMany(FeaturedAdPurchase::class, 'package_id');
    }

    public function isFree(): bool
    {
        return (float) $this->price <= 0;
    }
}
