<?php

namespace App\Models\Backend;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Widget extends Model
{
    use HasFactory;

    protected $table = 'widgets';
    protected $fillable = ['widget_area','widget_order','widget_name','widget_content','widget_location'];
}
