<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReelComment extends Model
{
    protected $table = 'reel_comments';

    protected $fillable = ['listing_id', 'user_id', 'body', 'likes'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }
}
