<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;
use App\Models\MediaUpload;

class Listing extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = ['id'];

    protected $casts = [
        'auction_start_date' => 'datetime',
        'auction_end_date'   => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function subCategory(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'sub_category_id');
    }

    public function childCategory(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'child_category_id');
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function favorites(): HasMany
    {
        return $this->hasMany(ListingFavorite::class);
    }

    public function reports(): HasMany
    {
        return $this->hasMany(ListingReport::class);
    }

    public function auctionBids(): HasMany
    {
        return $this->hasMany(AuctionBid::class);
    }

    public function adVideos(): HasMany
    {
        return $this->hasMany(AdVideo::class);
    }

    public function mediaUpload(): BelongsTo
    {
        return $this->belongsTo(MediaUpload::class, 'image');
    }

    public function scopeIsActive($query)
    {
        return $query->where('status', true)->where('is_published', true);
    }

    public function thumbnail(): Attribute
    {
        $thumbnail = asset('default/default.jpg');

        if ($this->image) {
            if (filter_var($this->image, FILTER_VALIDATE_URL)) {
                $thumbnail = $this->image;
            } elseif (str_starts_with((string) $this->image, 'assets/uploads/')) {
                $thumbnail = rtrim(config('app.url'), '/') . '/' . ltrim((string) $this->image, '/');
            }

            if (is_numeric($this->image)) {
                // image is a media_uploads.id reference (from Listocean)
                if ($this->relationLoaded('mediaUpload') && $this->mediaUpload) {
                    $thumbnail = rtrim(config('app.url'), '/') . '/assets/uploads/media-uploader/' . $this->mediaUpload->path;
                }
            } elseif (Storage::disk('public')->exists($this->image)) {
                // image is a storage path (from sellupnow-admin file uploads)
                $thumbnail = Storage::disk('public')->url($this->image);
            }
        }

        return Attribute::make(
            get: fn () => $thumbnail,
        );
    }
}
