<?php

use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

Broadcast::channel('livechat-user-channel.{member_id}.{user_id}', function ($user, $member_id){
    return (int) $user->id === (int) $member_id;
});

Broadcast::channel('livechat-member-channel.{user_id}.{member_id}', static function ($member, $user_id){
    return (int) $member->id === (int) $user_id;
});
