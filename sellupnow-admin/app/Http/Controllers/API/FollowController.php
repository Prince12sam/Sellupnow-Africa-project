<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Follow;
use App\Models\User;
use Illuminate\Http\Request;

class FollowController extends Controller
{
    /** Toggle follow status for a user. */
    public function toggle(Request $request)
    {
        $data = $request->validate([
            'user_id' => 'required|integer|exists:users,id',
        ]);

        $followerId  = auth('api')->id();
        $followingId = $data['user_id'];

        if ($followerId === $followingId) {
            return $this->json('You cannot follow yourself', [], 422);
        }

        $existing = Follow::where('follower_id', $followerId)->where('following_id', $followingId)->first();

        if ($existing) {
            $existing->delete();
            $isFollowing = false;
        } else {
            Follow::create(['follower_id' => $followerId, 'following_id' => $followingId]);
            $isFollowing = true;
        }

        return $this->json('follow status updated', [
            'user_id'      => $followingId,
            'is_following' => $isFollowing,
        ]);
    }

    /** Return followers and following lists for the authenticated user. */
    public function connections(Request $request)
    {
        $userId = auth('api')->id();

        // Who is the auth user following?
        $followingIds = Follow::where('follower_id', $userId)->pluck('following_id');
        $following    = User::whereIn('id', $followingIds)->get(['id', 'name', 'username', 'image']);

        // Who follows the auth user?
        $followerIds = Follow::where('following_id', $userId)->pluck('follower_id');
        $followers   = User::whereIn('id', $followerIds)->get(['id', 'name', 'username', 'image']);

        return $this->json('social connections', [
            'following'        => $following,
            'followers'        => $followers,
            'following_count'  => $following->count(),
            'followers_count'  => $followers->count(),
        ]);
    }
}
