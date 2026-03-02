<?php

namespace App\Models\Frontend;

use App\Models\User;
use App\Models\Backend\Listing;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Columns: id, listing_id, user_id, amount_paid, payment_method,
 *          boosted_at, expires_at, status, created_at, updated_at
 */
class Boost extends Model
{
    use HasFactory;

    protected $fillable = [
        'listing_id', 'user_id', 'amount_paid', 'payment_method',
        'boosted_at', 'expires_at', 'status',
    ];

    protected $casts = [
        'amount_paid' => 'decimal:2',
        'boosted_at'  => 'datetime',
        'expires_at'  => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function listing()
    {
        return $this->belongsTo(Listing::class);
    }

    public function isActive(): bool
    {
        return $this->status === 'active' && $this->expires_at?->isFuture();
    }
}
