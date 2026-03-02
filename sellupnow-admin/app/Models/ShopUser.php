<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ShopUser extends Model
{
    protected $table = 'shop_user';
    protected $guarded = [];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function shop(): BelongsTo
    {
        return $this->belongsTo(Shop::class, 'shop_id');
    }

    public function shopUserChats(): HasMany
    {
        return $this->hasMany(ShopUserChats::class, 'shop_user_id');
    }

    public function latestMessage(): HasOne
    {
        return $this->hasOne(ShopUserChats::class, 'shop_user_id')->latestOfMany();
    }
}
