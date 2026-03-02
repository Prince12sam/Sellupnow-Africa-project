<?php

namespace App\Models\Frontend;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class BlockedUser extends Model
{
    protected $table = 'blocked_users';

    protected $fillable = [
        'user_id',
        'blocked_user_id',
    ];

    public function blockedUser()
    {
        return $this->belongsTo(User::class, 'blocked_user_id');
    }

    public function blocker()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
