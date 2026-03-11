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
        // Flutter sends toUserId as query param
        $followingId = $request->input('toUserId') ?? $request->input('user_id');

        if (!$followingId || !User::where('id', $followingId)->exists()) {
            return response()->json(['status' => false, 'message' => 'Invalid user'], 422);
        }

        $followerId  = auth('api')->id();
        $followingId = (int) $followingId;

        if ($followerId === $followingId) {
            return response()->json(['status' => false, 'message' => 'You cannot follow yourself'], 422);
        }

        $existing = Follow::where('follower_id', $followerId)->where('following_id', $followingId)->first();

        if ($existing) {
            $existing->delete();
            $isFollowing = false;
        } else {
            Follow::create(['follower_id' => $followerId, 'following_id' => $followingId]);
            $isFollowing = true;
        }

        return response()->json([
            'status'  => true,
            'message' => 'follow status updated',
            'isFollow' => $isFollowing,
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
