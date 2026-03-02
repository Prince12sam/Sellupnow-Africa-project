<?php

namespace App\Models\Backend;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomFont extends Model
{
    use HasFactory;

    protected $table = 'custom_fonts';
    protected $fillable = ['file', 'path', 'status'];
}
