<?php

namespace App\Models\Frontend;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MembershipPlan extends Model
{
    use HasFactory;

    protected $table = 'membership_plans';

    protected $fillable = [
        'name', 'description', 'price', 'currency', 'billing_period',
        'duration_days', 'listing_quota', 'auto_feature_count', 'video_quota', 'banner_ad_quota',
        'badge_label', 'badge_color', 'features', 'is_active', 'sort_order',
    ];

    protected $casts = [
        'features'        => 'array',
        'price'           => 'decimal:2',
        'is_active'       => 'boolean',
        'video_quota'     => 'integer',
        'banner_ad_quota' => 'integer',
    ];

    public function userMemberships()
    {
        return $this->hasMany(UserMembership::class, 'membership_id');
    }

    public function isFree(): bool
    {
        return (float) $this->price == 0.0;
    }

    public function hasFeature(string $key): bool
    {
        $features = $this->features ?? [];
        return in_array($key, $features) || isset($features[$key]);
    }
}
