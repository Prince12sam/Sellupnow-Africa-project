<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MarketplaceBanner extends Model
{
    protected $table = 'marketplace_banners';

    protected $fillable = [
        'title',
        'image_path',
        'redirect_url',
        'status',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];
}
