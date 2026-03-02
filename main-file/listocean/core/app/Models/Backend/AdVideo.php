<?php

namespace App\Models\Backend;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class AdVideo extends Model
{
    use HasFactory;

    protected $table = 'ad_videos';

    protected $guarded = ['id'];

    protected $casts = [
        'is_approved'  => 'boolean',
        'is_rejected'  => 'boolean',
        'is_sponsored' => 'boolean',
        'approved_at'  => 'datetime',
        'rejected_at'  => 'datetime',
        'start_at'     => 'datetime',
        'end_at'       => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    public function listing(): BelongsTo
    {
        return $this->belongsTo(Listing::class);
    }

    /** Absolute URL to the stored video file. */
    public function videoUrl(): ?string
    {
        return $this->video_url ? Storage::url($this->video_url) : null;
    }

    /** Absolute URL to the thumbnail. */
    public function thumbnailUrl(): ?string
    {
        return $this->thumbnail_url ? Storage::url($this->thumbnail_url) : null;
    }

    /** Human-readable moderation status label + colour. */
    public function moderationStatus(): array
    {
        if ($this->is_approved) {
            return ['label' => 'Approved', 'class' => 'bg-success'];
        }
        if ($this->is_rejected) {
            return ['label' => 'Rejected', 'class' => 'bg-danger'];
        }
        return ['label' => 'Pending Review', 'class' => 'bg-warning text-dark'];
    }
}
