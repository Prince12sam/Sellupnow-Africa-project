<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ShopUserChats extends Model
{
    protected $table = 'shop_user_chats';
    protected $guarded = [];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function shop(): BelongsTo
    {
        return $this->belongsTo(Shop::class, 'shop_id');
    }

    public function shopUser(): BelongsTo
    {
        return $this->belongsTo(ShopUser::class, 'shop_user_id');
    }
}
