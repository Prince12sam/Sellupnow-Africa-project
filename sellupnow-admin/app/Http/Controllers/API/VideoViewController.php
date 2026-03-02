<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\AdVideo;
use App\Models\VideoView;
use Illuminate\Http\Request;

class VideoViewController extends Controller
{
    public function record(Request $request)
    {
        $data = $request->validate([
            'video_id' => 'required|integer|exists:ad_videos,id',
        ]);

        $userId  = auth('api')->id();
        $videoId = $data['video_id'];

        // Increment view counter
        AdVideo::where('id', $videoId)->increment('views');

        // Store a view record (useful for analytics)
        VideoView::create([
            'ad_video_id' => $videoId,
            'user_id'     => $userId,
            'ip_address'  => $request->ip(),
        ]);

        $views = AdVideo::where('id', $videoId)->value('views');

        return $this->json('Video view recorded', [
            'video_id' => $videoId,
            'views'    => $views,
        ]);
    }
}
