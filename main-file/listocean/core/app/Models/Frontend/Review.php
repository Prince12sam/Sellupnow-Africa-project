<?php

namespace App\Models\Frontend;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;

    protected $table = 'reviews';
    protected $fillable = ['user_id','reviewer_id','rating','message','status'];

    public function ratingMax($max)
    {
        return $this->avg('rating') <= $max;
    }

    public function user(){
        return $this->belongsTo(User::class,'user_id','id');
    }

    public function reviewer(){
        return $this->belongsTo(User::class,'reviewer_id','id');
    }

}
