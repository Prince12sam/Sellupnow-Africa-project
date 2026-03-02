<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use OpenAI\Laravel\Facades\OpenAI;

class AiRecommendationService
{
    public function rankListingIdsForUser(array $listingRows, Request $request, string $surface): array
    {
        if (empty($listingRows)) {
            return [];
        }

        if (! $this->isEnabled()) {
            return array_values(array_map(fn ($row) => (int) $row['id'], $listingRows));
        }

        if (! config('openai.api_key')) {
            return array_values(array_map(fn ($row) => (int) $row['id'], $listingRows));
        }

        $userId = $request->user()?->id;
        if (! $userId) {
            return array_values(array_map(fn ($row) => (int) $row['id'], $listingRows));
        }

        $dailyLimit = $this->dailyLimit();
        if ($dailyLimit > 0) {
            $countKey = $this->dailyCountKey($userId);
            $count = (int) Cache::get($countKey, 0);
            if ($count >= $dailyLimit) {
                return array_values(array_map(fn ($row) => (int) $row['id'], $listingRows));
            }
        }

        $cacheKey = $this->rankCacheKey($userId, $request, $surface, $listingRows);
        $cached = Cache::get($cacheKey);
        if (is_array($cached) && ! empty($cached)) {
            return $cached;
        }

        $model = $this->model();

        $context = [
            'surface' => $surface,
            'search' => (string) $request->query('search', ''),
            'category_id' => (string) $request->query('category_id', ''),
            'sub_category_id' => (string) $request->query('sub_category_id', ''),
            'child_category_id' => (string) $request->query('child_category_id', ''),
        ];

        $candidates = array_map(function (array $row) {
            return [
                'id' => (int) $row['id'],
                'title' => $this->redactPII((string) ($row['title'] ?? '')),
                'subtitle' => $this->redactPII((string) ($row['sub_title'] ?? $row['subTitle'] ?? '')),
                'price' => isset($row['price']) ? (float) $row['price'] : null,
                'city' => (string) ($row['city'] ?? ''),
                'state' => (string) ($row['state'] ?? ''),
                'country' => (string) ($row['country'] ?? ''),
            ];
        }, $listingRows);

        $system = 'You recommend listings to buyers for a classifieds marketplace. '
            . 'Return ONLY valid JSON (no markdown). '
            . 'Only rank the provided candidate ids. Do not invent ids or listings.';

        $user = [
            'task' => 'Rank candidate listings for a buyer. Higher rank = more relevant and attractive, but never misleading.',
            'constraints' => [
                'must_only_use_candidate_ids' => true,
                'output_format' => 'json',
                'return_key' => 'ranked_ids',
            ],
            'context' => $context,
            'candidates' => $candidates,
            'output_schema' => [
                'ranked_ids' => 'array<int>',
            ],
        ];

        $requestId = (string) Str::uuid();
        $startedAt = microtime(true);

        try {
            $response = OpenAI::chat()->create([
                'model' => $model,
                'temperature' => 0.2,
                'max_tokens' => 400,
                'messages' => [
                    ['role' => 'system', 'content' => $system],
                    ['role' => 'user', 'content' => json_encode($user, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)],
                ],
            ]);

            $content = (string) ($response->choices[0]->message->content ?? '');
            $json = $this->extractJson($content);
            $decoded = json_decode($json, true);

            $ranked = $decoded['ranked_ids'] ?? null;
            if (! is_array($ranked)) {
                throw new \RuntimeException('Invalid ranked_ids');
            }

            $candidateIds = array_values(array_map(fn ($c) => (int) $c['id'], $candidates));
            $rankedIds = [];
            foreach ($ranked as $id) {
                $id = (int) $id;
                if (in_array($id, $candidateIds, true) && ! in_array($id, $rankedIds, true)) {
                    $rankedIds[] = $id;
                }
            }

            foreach ($candidateIds as $id) {
                if (! in_array($id, $rankedIds, true)) {
                    $rankedIds[] = $id;
                }
            }

            Cache::put($cacheKey, $rankedIds, now()->addMinutes(10));
            if ($dailyLimit > 0) {
                $countKey = $this->dailyCountKey($userId);
                Cache::put($countKey, ((int) Cache::get($countKey, 0)) + 1, now()->endOfDay());
            }

            $this->log([
                'status' => 'success',
                'request_id' => $requestId,
                'model' => $model,
                'surface' => $surface,
                'latency_ms' => (int) round((microtime(true) - $startedAt) * 1000),
                'user_id' => $userId,
                'candidate_count' => count($candidateIds),
            ]);

            return $rankedIds;
        } catch (\Throwable $e) {
            $this->log([
                'status' => 'error',
                'request_id' => $requestId,
                'model' => $model,
                'surface' => $surface,
                'latency_ms' => (int) round((microtime(true) - $startedAt) * 1000),
                'user_id' => $userId,
                'candidate_count' => count($listingRows),
                'error_message' => $e->getMessage(),
            ]);

            return array_values(array_map(fn ($row) => (int) $row['id'], $listingRows));
        }
    }

    public function isEnabled(): bool
    {
        $value = (string) ($this->listocean()->table('static_options')->where('option_name', 'ai_recommendations_enabled')->value('option_value') ?? '');
        return in_array(strtolower(trim($value)), ['1', 'true', 'yes', 'on', 'enabled'], true);
    }

    public function dailyLimit(): int
    {
        $value = (string) ($this->listocean()->table('static_options')->where('option_name', 'ai_recommendations_daily_limit')->value('option_value') ?? '');
        $int = (int) $value;
        return $int > 0 ? $int : 20;
    }

    public function model(): string
    {
        $value = (string) ($this->listocean()->table('static_options')->where('option_name', 'ai_recommendations_model')->value('option_value') ?? '');
        $value = trim($value);
        return $value !== '' ? $value : 'gpt-4o-mini';
    }

    private function redactPII(string $text): string
    {
        $text = preg_replace('/[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,}/i', '[redacted-email]', $text) ?? $text;
        $text = preg_replace('/\+?\d[\d\s().-]{8,}\d/', '[redacted-phone]', $text) ?? $text;
        return $text;
    }

    private function extractJson(string $content): string
    {
        $content = trim($content);
        if ($content === '') {
            return '';
        }

        if (str_starts_with($content, '{') && str_ends_with($content, '}')) {
            return $content;
        }

        $first = strpos($content, '{');
        $last = strrpos($content, '}');

        if ($first === false || $last === false || $last <= $first) {
            return $content;
        }

        return substr($content, $first, $last - $first + 1);
    }

    private function dailyCountKey(int $userId): string
    {
        return 'ai_reco_daily:' . now()->format('Y-m-d') . ':u:' . $userId;
    }

    private function rankCacheKey(int $userId, Request $request, string $surface, array $listingRows): string
    {
        $ids = array_map(fn ($r) => (int) $r['id'], $listingRows);
        $idsHash = sha1(implode(',', $ids));

        $q = (string) $request->query('search', '');
        $filters = implode('|', [
            (string) $request->query('category_id', ''),
            (string) $request->query('sub_category_id', ''),
            (string) $request->query('child_category_id', ''),
        ]);

        return 'ai_reco_rank:' . $surface . ':u:' . $userId . ':' . sha1($q . '|' . $filters) . ':' . $idsHash;
    }

    private function log(array $data): void
    {
        try {
            $conn = $this->listocean();
            if (! $conn->getSchemaBuilder()->hasTable('ai_recommendation_logs')) {
                return;
            }

            $now = now();
            $conn->table('ai_recommendation_logs')->insert([
                'source' => 'sellupnow_api',
                'user_id' => $data['user_id'] ?? null,
                'status' => (string) ($data['status'] ?? 'unknown'),
                'surface' => (string) ($data['surface'] ?? ''),
                'model' => (string) ($data['model'] ?? null),
                'request_id' => (string) ($data['request_id'] ?? null),
                'candidate_count' => isset($data['candidate_count']) ? (int) $data['candidate_count'] : null,
                'latency_ms' => isset($data['latency_ms']) ? (int) $data['latency_ms'] : null,
                'error_message' => isset($data['error_message']) ? Str::limit((string) $data['error_message'], 1000, '') : null,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        } catch (\Throwable $e) {
            report($e);
        }
    }

    private function listocean()
    {
        return DB::connection('listocean');
    }
}
