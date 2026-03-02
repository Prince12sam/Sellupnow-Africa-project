<?php

namespace App\Models\Backend;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\CountryManage\app\Models\City;
use Modules\CountryManage\app\Models\Country;
use Modules\CountryManage\app\Models\State;

class IdentityVerification extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'identification_type',
        'identification_number',
        'front_document',
        'back_document',
        'country_id',
        'state_id',
        'city_id',
        'zip_code',
        'address',
        'verify_by',
        'status',
    ];

    protected $casts = ['status'=>'integer','is_read'=>'integer'];

    public function user()
    {
        return $this->belongsTo(User::class,'user_id');
    }

    public function user_country()
    {
        return $this->belongsTo(Country::class,'country_id');
    }
    public function user_state()
    {
        return $this->belongsTo(State::class,'state_id');
    }
    public function user_city()
    {
        return $this->belongsTo(City::class,'city_id');
    }
}
