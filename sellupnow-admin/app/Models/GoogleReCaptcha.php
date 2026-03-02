<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GoogleReCaptcha extends Model
{
    use HasFactory;

    protected $fillable = [
        'site_key',
        'secret_key',
        'provider',
        'turnstile_site_key',
        'turnstile_secret_key',
        'is_active',
    ];
}
