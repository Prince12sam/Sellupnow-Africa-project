<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Backend\Listing;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use OpenAI\Laravel\Facades\OpenAI;

class AiRecommendationController extends Controller
{
    /**
     * Return AI-powered listing recommendations for a given listing.
     * Results are cached per listing for 4 hours to minimise API cost.
     *
     * POST /ai/recommendations
     */
    public function recommend(Request $request): JsonResponse
    {
        // --- Feature guard ---
        $enabled = get_static_option('ai_recommendations_enabled');
        $isEnabled = in_array(strtolower(trim((string) $enabled)), ['1', 'true', 'yes', 'on', 'enabled'], true);
        if (! $isEnabled) {
            return response()->json(['html' => '', 'count' => 0]);
        }

        $request->validate([
            'listing_id' => 'required|integer',
            'title'      => 'required|string|max:200',
            'category'   => 'nullable|string|max:100',
        ]);

        $listingId   = (int) $request->input('listing_id');
        $title       = trim($request->input('title'));
        $category    = trim($request->input('category', ''));

        $modelName = (string) (get_static_option('ai_recommendations_model') ?? 'gpt-4o-mini');
        if ($modelName === '') {
            $modelName = 'gpt-4o-mini';
        }

        $cacheKey = 'ai_reco_' . $listingId . '_v1';

        try {
            $listingIds = Cache::remember($cacheKey, now()->addHours(4), function () use (
                $listingId, $title, $category, $modelName
            ) {
                return $this->fetchRecommendedIds($listingId, $title, $category, $modelName);
            });

            if (empty($listingIds)) {
                return response()->json(['html' => '', 'count' => 0]);
            }

            $listings = Listing::with('user')
                ->whereIn('id', $listingIds)
                ->where('id', '!=', $listingId)
                ->where('status', 1)
                ->where('is_published', 1)
                ->take(6)
                ->get();

            if ($listings->isEmpty()) {
                return response()->json(['html' => '', 'count' => 0]);
            }

            $html = view('frontend.pages.listings.ai-recommendations-markup', [
                'recommendations' => $listings,
            ])->render();

            return response()->json(['html' => $html, 'count' => $listings->count()]);

        } catch (\Throwable $e) {
            report($e);
            return response()->json(['html' => '', 'count' => 0]);
        }
    }

    /**
     * Call OpenAI to get keyword suggestions, then query the DB.
     */
    private function fetchRecommendedIds(int $listingId, string $title, string $category, string $model): array
    {
        // Log attempt
        $logData = [
            'listing_id'        => $listingId,
            'model'             => $model,
            'prompt'            => '',
            'response'          => null,
            'prompt_tokens'     => 0,
            'completion_tokens' => 0,
            'success'           => false,
            'error_message'     => null,
            'created_at'        => now(),
            'updated_at'        => now(),
        ];

        try {
            $contextLine = "Listing title: \"{$title}\"";
            if ($category !== '') {
                $contextLine .= " | Category: {$category}";
            }

            $systemPrompt = 'You are a smart classifieds marketplace assistant. '
                . 'Given a listing, suggest 5 short keyword phrases (1-3 words each) '
                . 'that describe items a buyer of this listing might also want to find. '
                . 'Focus on complementary or similar products. '
                . 'Respond with a valid JSON array of strings — no extra text, no markdown.';

            $userPrompt = "Suggest related search keywords for: {$contextLine}";

            $logData['prompt'] = $userPrompt;

            $response = OpenAI::chat()->create([
                'model'           => $model,
                'messages'        => [
                    ['role' => 'system', 'content' => $systemPrompt],
                    ['role' => 'user',   'content' => $userPrompt],
                ],
                'temperature'     => 0.6,
                'max_tokens'      => 120,
                'response_format' => ['type' => 'json_object'],
            ]);

            $raw     = $response->choices[0]->message->content ?? '{}';
            $decoded = json_decode($raw, true);

            // The model might return {"keywords": [...]} or a plain array
            $keywords = [];
            if (is_array($decoded)) {
                $first = reset($decoded);
                if (is_array($first)) {
                    $keywords = $first;
                } elseif (is_string($first)) {
                    $keywords = array_values($decoded);
                }
            }

            $logData['response']          = $raw;
            $logData['prompt_tokens']     = $response->usage->promptTokens ?? 0;
            $logData['completion_tokens'] = $response->usage->completionTokens ?? 0;
            $logData['success']           = true;

        } catch (\Throwable $e) {
            $logData['error_message'] = $e->getMessage();
            // Fallback: use title words as keywords
            $keywords = collect(explode(' ', $title))
                ->filter(fn($w) => strlen($w) > 3)
                ->take(4)
                ->map(fn($w) => trim($w, '.,!?-'))
                ->values()
                ->all();
        } finally {
            // Store log if table exists
            try {
                if (DB::getSchemaBuilder()->hasTable('ai_recommendation_logs')) {
                    DB::table('ai_recommendation_logs')->insert($logData);
                }
            } catch (\Throwable) {}
        }

        if (empty($keywords)) {
            return [];
        }

        // Build a LIKE query for each keyword
        $query = Listing::where('status', 1)
            ->where('is_published', 1)
            ->where('id', '!=', $listingId);

        $query->where(function ($q) use ($keywords) {
            foreach ($keywords as $kw) {
                $kw = trim((string) $kw);
                if ($kw === '') continue;
                $q->orWhere('title', 'like', '%' . $kw . '%');
            }
        });

        return $query->inRandomOrder()->take(6)->pluck('id')->all();
    }
}
