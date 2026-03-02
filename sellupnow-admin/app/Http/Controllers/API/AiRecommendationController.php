<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Listing;
use App\Services\AiRecommendationService;
use Illuminate\Http\Request;

class AiRecommendationController extends Controller
{
    public function rank(Request $request, AiRecommendationService $service)
    {
        $validated = $request->validate([
            'surface' => 'required|string|max:40',
            'candidate_ids' => 'required|array|min:2|max:50',
            'candidate_ids.*' => 'integer',
        ]);

        if (! $service->isEnabled()) {
            return response()->json([
                'status' => false,
                'message' => 'AI recommendations are disabled by admin.',
            ], 403);
        }

        $ids = array_values(array_unique(array_map('intval', $validated['candidate_ids'])));

        $rows = Listing::query()
            ->select(['id', 'title', 'sub_title', 'price', 'city', 'state', 'country'])
            ->whereIn('id', $ids)
            ->isActive()
            ->get()
            ->map(fn ($l) => $l->toArray())
            ->all();

        if (count($rows) < 2) {
            return response()->json([
                'status' => true,
                'message' => 'No ranking needed',
                'data' => [
                    'ranked_ids' => $ids,
                ],
            ]);
        }

        $ranked = $service->rankListingIdsForUser($rows, $request, (string) $validated['surface']);

        return response()->json([
            'status' => true,
            'message' => 'Ranked',
            'data' => [
                'ranked_ids' => $ranked,
            ],
        ]);
    }
}
