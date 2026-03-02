<?php

namespace App\Models\Frontend;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Visitor extends Model
{
    use HasFactory;

    protected $table = 'visitors';
    protected $fillable = [
        'advertisement_id',
        'ip_address',
        'user_agent',
        'country',
        'city',
        'country_code',
        'latitude',
        'longitude',
    ];

}
