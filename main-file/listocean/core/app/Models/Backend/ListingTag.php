<?php

namespace App\Models\Backend;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ListingTag extends Model
{
    use HasFactory;
    protected $table = 'listing_tags';
    protected $fillable = [ 'listing_id','tag_id'];
}
