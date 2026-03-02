<?php

namespace App\Models;

use App\Models\Backend\IdentityVerification;
use App\Models\Backend\Listing;
use App\Models\Frontend\AccountDeactivate;
use App\Models\Frontend\Review;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Modules\Chat\app\Models\LiveChat;
use Modules\Chat\app\Models\LiveChatMessage;
use Modules\CountryManage\app\Models\City;
use Modules\CountryManage\app\Models\Country;
use Modules\CountryManage\app\Models\State;
use Modules\Membership\app\Models\UserMembership;
use Modules\Wallet\app\Models\Wallet;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, softDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'username',
        'phone',
        'email',
        'password',
        'image',
        'profile_background',
        'country_id',
        'state_id',
        'city_id',
        'post_code',
        'latitude',
        'longitude',
        'address',
        'about',
        'is_notifications_allowed',
        'is_contact_info_visible',
        'terms_condition',
        'google_id',
        'facebook_id',
        'apple_id',
        'email_verify_token',
        'email_verified',
        'otp_verified',
        'check_online_status',
        'verified_status',
        'is_suspend',
        'status',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'check_online_status'=>'datetime',
    ];

    //get user full name
    public function getFullnameAttribute()
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    public function user_country()
    {
        return $this->belongsTo(Country::class,'country_id')->select('id','country','status');
    }
    public function user_state()
    {
        return $this->belongsTo(State::class,'state_id');
    }
    public function user_city()
    {
        return $this->belongsTo(City::class,'city_id');
    }

    public function identity_verify()
    {
        return $this->hasOne(IdentityVerification::class,'user_id','id');
    }
    public function user_wallet()
    {
        return $this->hasOne(Wallet::class,'user_id','id');
    }

    public function membershipUser()
    {
        if(!moduleExists('Membership')){
            return null;
        }
        return $this->hasOne(UserMembership::class,'user_id','id');
    }

    public function listings(){
        return $this->hasMany(Listing::class,'user_id','id');
    }

    public function reviews(){
        return $this->hasMany(Review::class,'user_id','id');
    }
    public function account_deactivates(){
        return $this->hasMany(AccountDeactivate::class,'user_id','id');
    }

    public function member_unseen_message()
    {
        if (moduleExists('Chat')) {
            return $this->hasManyThrough(LiveChatMessage::class, LiveChat::class, 'member_id', 'live_chat_id');
        }
        return null;
    }

    public function user_unseen_message()
    {
        if (moduleExists('Chat')) {
            return $this->hasManyThrough(LiveChatMessage::class, LiveChat::class, 'user_id', 'live_chat_id');
        }
        return null;
    }

}
