<?php

namespace App\Models\Frontend;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UserMembership extends Model
{
    use HasFactory;

    protected $table = 'user_memberships';

    protected $fillable = [
        'user_id', 'membership_id', 'price', 'payment_gateway', 'payment_status',
        'transaction_id', 'listing_limit', 'gallery_images', 'initial_gallery_images',
        'featured_listing', 'initial_featured_listing',
        'enquiry_form', 'business_hour', 'membership_badge', 'expire_date', 'status',
    ];

    protected $casts = [
        'price'           => 'decimal:2',
        'expire_date'     => 'datetime',
        'listing_limit'   => 'integer',
        'featured_listing'=> 'integer',
        'status'          => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * The membership plan this subscription belongs to.
     * FK column in DB is 'membership_id' (not plan_id).
     */
    public function plan()
    {
        return $this->belongsTo(MembershipPlan::class, 'membership_id');
    }

    public function isActive(): bool
    {
        return (int) $this->status === 1
            && ($this->expire_date === null || $this->expire_date->isFuture());
    }

    public function isExpired(): bool
    {
        return $this->expire_date !== null && $this->expire_date->isPast();
    }

    public function canPostListing(): bool
    {
        if (! $this->isActive()) {
            return false;
        }

        $planQuota = (int) optional($this->plan)->listing_quota;
        if ($planQuota === 0) {
            return true;
        }

        return (int) $this->listing_limit > 0;
    }
}
