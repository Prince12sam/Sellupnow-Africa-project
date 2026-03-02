<?php

namespace Modules\Brand\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Brand\Database\factories\BrandFactory;

class Brand extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $table = 'brands';
    protected $fillable = ['title','url','image'];

    protected static function newFactory(): BrandFactory
    {
        return BrandFactory::new();
    }
}
