<?php

namespace App\Models\Backend;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Advertisement extends Model
{
    use HasFactory;

    protected $table = 'advertisements';
    protected $fillable = ['user_id','title','type','size','image','slot','requested_slot','embed_code','redirect_url','click','impression','status'];

    protected $casts = [
        'status' => 'integer'
    ];
}
