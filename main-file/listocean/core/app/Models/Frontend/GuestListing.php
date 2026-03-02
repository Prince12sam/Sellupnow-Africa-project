<?php

namespace App\Models\Frontend;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GuestListing extends Model
{
    use HasFactory;

    protected $table = 'guest_listings';
    protected $fillable = ['listing_id','first_name','last_name','email','phone', 'terms_condition', 'status'];

    //get user full name
    public function getGuestFullnameAttribute()
    {
        return $this->first_name . ' ' . $this->last_name;
    }

}
