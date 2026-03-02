<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\Block;
use App\Models\User;
use Illuminate\Http\Request;

class BlockController extends Controller
{
    /** Toggle block status for a user. */
    public function toggle(Request $request)
    {
        $data = $request->validate([
            'user_id' => 'required|integer|exists:users,id',
        ]);

        $blockerId  = auth('api')->id();
        $blockedId  = $data['user_id'];

        if ($blockerId === $blockedId) {
            return $this->json('You cannot block yourself', [], 422);
        }

        $existing = Block::where('blocker_id', $blockerId)->where('blocked_id', $blockedId)->first();

        if ($existing) {
            $existing->delete();
            $isBlocked = false;
        } else {
            Block::create(['blocker_id' => $blockerId, 'blocked_id' => $blockedId]);
            $isBlocked = true;
        }

        return $this->json('block status updated', [
            'user_id'    => $blockedId,
            'is_blocked' => $isBlocked,
        ]);
    }

    /** List all users blocked by the authenticated user. */
    public function index(Request $request)
    {
        $blockerId = auth('api')->id();

        $blockedIds = Block::where('blocker_id', $blockerId)->pluck('blocked_id');
        $users = User::whereIn('id', $blockedIds)->get(['id', 'name', 'username', 'image']);

        return $this->json('blocked users', [
            'total' => $users->count(),
            'users' => $users,
        ]);
    }
}
