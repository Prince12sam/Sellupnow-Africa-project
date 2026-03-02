<?php

namespace App\Models\Frontend;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccountDeactivate extends Model
{
    use HasFactory;

    protected $fillable = ['user_id','reason','description','status','account_status'];

    public function user(){
        return $this->belongsTo(User::class,'user_id');
    }
}
