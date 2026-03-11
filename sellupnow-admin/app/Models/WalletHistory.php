<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WalletHistory extends Model
{
    public $timestamps = false;

    protected $guarded = ['id'];

    protected $table = 'wallet_histories';

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
