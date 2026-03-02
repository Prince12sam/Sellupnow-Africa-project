<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\AdVideo;
use App\Models\AdVideoLike;
use Illuminate\Http\Request;

class AdVideoLikeController extends Controller
{
    public function toggle(Request $request)
    {
        $data = $request->validate([
            'video_id' => 'required|integer|exists:ad_videos,id',
        ]);

        $userId  = auth('api')->id();
        $videoId = $data['video_id'];

        $existing = AdVideoLike::where('user_id', $userId)->where('ad_video_id', $videoId)->first();

        if ($existing) {
            $existing->delete();
            AdVideo::where('id', $videoId)->decrement('likes_count');
            $isLiked = false;
        } else {
            AdVideoLike::create(['user_id' => $userId, 'ad_video_id' => $videoId]);
            AdVideo::where('id', $videoId)->increment('likes_count');
            $isLiked = true;
        }

        $count = AdVideo::where('id', $videoId)->value('likes_count');

        return $this->json('ad video like toggled', [
            'video_id'    => $videoId,
            'is_liked'    => $isLiked,
            'likes_count' => $count,
        ]);
    }
}
