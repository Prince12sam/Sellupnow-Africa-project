<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class Banner extends Model
{
    use HasFactory;

    public const PLACEMENT_HOMEPAGE = 'homepage';
    public const PLACEMENT_MOBILE_AFTER_LIVE_AUCTION = 'mobile_after_live_auction';

    protected $guarded = ['id'];

    public static function placementOptions(): array
    {
        return [
            self::PLACEMENT_HOMEPAGE => 'Homepage Banner',
            self::PLACEMENT_MOBILE_AFTER_LIVE_AUCTION => 'Mobile Home - After Live Auction',
        ];
    }

    public function isHomepagePlacement(): bool
    {
        return ($this->placement ?? self::PLACEMENT_HOMEPAGE) === self::PLACEMENT_HOMEPAGE;
    }

    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }


    public function thumbnail(): Attribute
    {
        $thumbnail = asset('default/default.jpg');
        if ($this->banner) {
            if (Storage::disk('public')->exists($this->banner)) {
                $thumbnail = Storage::disk('public')->url($this->banner);
            } elseif (Storage::exists($this->banner)) {
                $thumbnail = Storage::url($this->banner);
            }
        }

        return Attribute::make(
            get: fn () => $thumbnail
        );
    }

}
