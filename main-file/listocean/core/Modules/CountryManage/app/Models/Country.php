<?php

namespace Modules\CountryManage\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Country extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = ['country','country_code','dial_code','latitude','longitude','status'];
    protected $casts = ['status'=>'integer'];

    protected static function newFactory()
    {
        return \Modules\CountryManage\Database\factories\CountryFactoryFactory::new();
    }

    public static function all_countries()
    {
        return self::where('status',1)->get();
    }

    public function states(){
        return $this->hasMany(State::class,'country_id','id');
    }
}
