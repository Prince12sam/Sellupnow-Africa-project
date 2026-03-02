<?php

namespace App\Models\Backend;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SocialIcon extends Model
{
    use HasFactory;

    protected $table = 'social_icons';
    protected $fillable = ['icon','url'];
}
