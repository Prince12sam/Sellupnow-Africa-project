<?php

namespace App\Models\Frontend;

use App\Models\Backend\Listing;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ListingFavorite extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'listing_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function listing()
    {
        return $this->belongsTo(Listing::class);
    }
}
