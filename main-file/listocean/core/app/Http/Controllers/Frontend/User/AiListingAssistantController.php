<?php

namespace App\Http\Controllers\Frontend\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use OpenAI\Laravel\Facades\OpenAI;

class AiListingAssistantController extends Controller
{
    /**
     * Generate AI title and description suggestions for a listing.
     *
     * POST /user/ai/listing-suggest
     */
    public function suggest(Request $request): JsonResponse
    {
        // --- Guard: feature enabled? ---
        $enabled = get_static_option('ai_listing_assistant_enabled');
        $isEnabled = in_array(strtolower(trim((string) $enabled)), ['1', 'true', 'yes', 'on', 'enabled'], true);
        if (! $isEnabled) {
            return response()->json(['error' => __('AI Listing Assistant is currently disabled.')], 403);
        }

        // --- Guard: authenticated user ---
        $user = Auth::guard('web')->user();
        if (! $user) {
            return response()->json(['error' => __('Unauthorized.')], 401);
        }

        // --- Daily limit check ---
        $dailyLimitRaw = (string) (get_static_option('ai_listing_assistant_daily_limit') ?? '20');
        $dailyLimit = max(1, (int) $dailyLimitRaw);

        $usedToday = DB::table('ai_listing_assistant_logs')
            ->where('user_id', $user->id)
            ->where('success', true)
            ->whereDate('created_at', now()->toDateString())
            ->count();

        if ($usedToday >= $dailyLimit) {
            return response()->json([
                'error' => __('You have reached your daily AI suggestion limit of :limit. Please try again tomorrow.', ['limit' => $dailyLimit]),
            ], 429);
        }

        // --- Validate input ---
        $request->validate([
            'keywords'  => 'required|string|min:3|max:300',
            'category'  => 'nullable|string|max:100',
            'condition' => 'nullable|string|max:50',
            'price'     => 'nullable|string|max:50',
        ]);

        $keywords  = trim($request->input('keywords'));
        $category  = trim($request->input('category', ''));
        $condition = trim($request->input('condition', ''));
        $price     = trim($request->input('price', ''));

        $modelName = (string) (get_static_option('ai_listing_assistant_model') ?? 'gpt-4o-mini');
        if ($modelName === '') {
            $modelName = 'gpt-4o-mini';
        }

        // --- Build prompt ---
        $contextParts = ["Item keywords: \"{$keywords}\""];
        if ($category !== '')  { $contextParts[] = "Category: {$category}"; }
        if ($condition !== '') { $contextParts[] = "Condition: {$condition}"; }
        if ($price !== '')     { $contextParts[] = "Price: {$price}"; }
        $context = implode(' | ', $contextParts);

        $systemPrompt = 'You are a professional marketplace listing copywriter. '
            . 'Given item details, you generate concise, engaging, SEO-friendly listing copy for a classifieds marketplace. '
            . 'Respond with a JSON object containing exactly two keys: "title" (max 80 chars) and "description" (150–400 chars, plain text, no markdown, buyer-focused). '
            . 'Do not include any extra keys, code blocks, or explanation outside the JSON.';

        $userPrompt = "Write a marketplace listing for this item. {$context}.";

        // --- Call OpenAI ---
        $logData = [
            'user_id'           => $user->id,
            'model'             => $modelName,
            'prompt'            => $userPrompt,
            'response'          => null,
            'prompt_tokens'     => 0,
            'completion_tokens' => 0,
            'success'           => false,
            'error_message'     => null,
            'ip_address'        => $request->ip(),
            'created_at'        => now(),
            'updated_at'        => now(),
        ];

        try {
            $response = OpenAI::chat()->create([
                'model'    => $modelName,
                'messages' => [
                    ['role' => 'system', 'content' => $systemPrompt],
                    ['role' => 'user',   'content' => $userPrompt],
                ],
                'temperature'      => 0.7,
                'max_tokens'       => 500,
                'response_format'  => ['type' => 'json_object'],
            ]);

            $rawContent = $response->choices[0]->message->content ?? '{}';
            $parsed     = json_decode($rawContent, true);

            $title       = trim((string) ($parsed['title'] ?? ''));
            $description = trim((string) ($parsed['description'] ?? ''));

            if ($title === '' && $description === '') {
                throw new \RuntimeException('AI returned empty content.');
            }

            $logData['response']          = $rawContent;
            $logData['prompt_tokens']     = $response->usage->promptTokens ?? 0;
            $logData['completion_tokens'] = $response->usage->completionTokens ?? 0;
            $logData['success']           = true;

            DB::table('ai_listing_assistant_logs')->insert($logData);

            return response()->json([
                'success'     => true,
                'title'       => $title,
                'description' => $description,
                'used_today'  => $usedToday + 1,
                'daily_limit' => $dailyLimit,
            ]);

        } catch (\Throwable $e) {
            $logData['error_message'] = $e->getMessage();
            DB::table('ai_listing_assistant_logs')->insert($logData);

            report($e);

            return response()->json([
                'error' => __('AI suggestion failed. Please try again or fill in the fields manually.'),
            ], 500);
        }
    }
}
