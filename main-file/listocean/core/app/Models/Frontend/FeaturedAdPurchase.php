<?php

namespace App\Models\Frontend;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Columns: id, user_id, package_id, listing_id, amount_paid,
 *          duration_days_at_purchase, payment_method, payment_reference,
 *          purchased_at, created_at, updated_at
 */
class FeaturedAdPurchase extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'package_id', 'listing_id', 'amount_paid',
        'duration_days_at_purchase',
        'payment_method', 'payment_reference', 'purchased_at',
    ];

    protected $casts = [
        'amount_paid'               => 'decimal:2',
        'duration_days_at_purchase' => 'integer',
        'purchased_at'              => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function package()
    {
        return $this->belongsTo(FeaturedAdPackage::class, 'package_id');
    }

    public function activations()
    {
        return $this->hasMany(FeaturedAdActivation::class, 'purchase_id');
    }

    public function activeActivation()
    {
        return $this->hasOne(FeaturedAdActivation::class, 'purchase_id')
            ->where('is_active', true)
            ->where('ends_at', '>=', now());
    }
}
