<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use OpenAI\Laravel\Facades\OpenAI;

class AiListingAssistantController extends Controller
{
    public function suggest(Request $request)
    {
        $validated = $request->validate([
            'title' => 'nullable|string|max:191',
            'subtitle' => 'nullable|string|max:191',
            'description' => 'nullable|string|max:5000',
            'category' => 'nullable|string|max:191',
            'language' => 'nullable|string|max:60',
        ]);

        $rawTitle = (string) ($validated['title'] ?? '');
        $rawSubtitle = (string) ($validated['subtitle'] ?? '');
        $rawDescription = (string) ($validated['description'] ?? '');

        if (trim($rawTitle . $rawSubtitle . $rawDescription) === '') {
            return response()->json([
                'status' => false,
                'message' => 'Please provide title or description.',
            ], 422);
        }

        if (! $this->isAssistantEnabled()) {
            $this->logAttempt([
                'status' => 'disabled',
                'input_title' => $rawTitle,
                'input_description' => $rawDescription,
            ]);

            return response()->json([
                'status' => false,
                'message' => 'AI Listing Assistant is disabled by admin.',
            ], 403);
        }

        if (! config('openai.api_key')) {
            return response()->json([
                'status' => false,
                'message' => 'OpenAI is not configured.',
            ], 503);
        }

        $dailyLimit = $this->dailyLimit();
        if ($dailyLimit > 0) {
            $dailyKey = $this->dailyLimitCacheKey($request);
            $count = (int) Cache::get($dailyKey, 0);
            if ($count >= $dailyLimit) {
                $this->logAttempt([
                    'status' => 'rate_limited',
                    'input_title' => $rawTitle,
                    'input_description' => $rawDescription,
                ]);

                return response()->json([
                    'status' => false,
                    'message' => 'Daily AI limit reached. Try again tomorrow.',
                ], 429);
            }
        }

        $inputTitle = $this->redactPII($rawTitle);
        $inputSubtitle = $this->redactPII($rawSubtitle);
        $inputDescription = $this->redactPII($rawDescription);

        $model = $this->model();
        $language = trim((string) ($validated['language'] ?? ''));
        $category = trim((string) ($validated['category'] ?? ''));

        $system = 'You are an AI listing assistant for a classifieds marketplace. '
            . 'Return ONLY valid JSON (no markdown, no explanations). '
            . 'Do not include phone numbers, emails, addresses, or any personal data.';

        $user = [
            'task' => 'Improve listing title, subtitle, and description for clarity and conversion while staying truthful and non-misleading.',
            'constraints' => [
                'title_max_chars' => 191,
                'subtitle_max_chars' => 191,
                'description_min_chars' => 150,
                'description_max_chars' => 2000,
                'no_personal_data' => true,
                'no_contact_info' => true,
            ],
            'context' => [
                'category' => $category !== '' ? $category : null,
                'language' => $language !== '' ? $language : null,
            ],
            'input' => [
                'title' => $inputTitle,
                'subtitle' => $inputSubtitle,
                'description' => $inputDescription,
            ],
            'output_schema' => [
                'title' => 'string',
                'subtitle' => 'string',
                'description' => 'string',
            ],
        ];

        $requestId = (string) Str::uuid();
        $startedAt = microtime(true);

        try {
            $response = OpenAI::chat()->create([
                'model' => $model,
                'temperature' => 0.4,
                'max_tokens' => 700,
                'messages' => [
                    ['role' => 'system', 'content' => $system],
                    ['role' => 'user', 'content' => json_encode($user, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)],
                ],
            ]);

            $content = (string) ($response->choices[0]->message->content ?? '');
            $json = $this->extractJson($content);
            $decoded = json_decode($json, true);

            if (! is_array($decoded)) {
                throw new \RuntimeException('Invalid JSON response from model.');
            }

            $outTitle = $this->sanitizeOutput((string) ($decoded['title'] ?? ''));
            $outSubtitle = $this->sanitizeOutput((string) ($decoded['subtitle'] ?? ''));
            $outDescription = $this->sanitizeOutput((string) ($decoded['description'] ?? ''));

            if ($dailyLimit > 0) {
                $dailyKey = $this->dailyLimitCacheKey($request);
                Cache::put($dailyKey, ((int) Cache::get($dailyKey, 0)) + 1, now()->endOfDay());
            }

            $this->logAttempt([
                'status' => 'success',
                'model' => $model,
                'request_id' => $requestId,
                'latency_ms' => (int) round((microtime(true) - $startedAt) * 1000),
                'input_title' => $inputTitle,
                'input_description' => $inputDescription,
                'output_title' => $outTitle,
                'output_subtitle' => $outSubtitle,
                'output_description' => $outDescription,
            ]);

            return response()->json([
                'status' => true,
                'message' => 'AI suggestion generated',
                'data' => [
                    'request_id' => $requestId,
                    'title' => $outTitle,
                    'subtitle' => $outSubtitle,
                    'description' => $outDescription,
                ],
            ]);
        } catch (\Throwable $e) {
            $this->logAttempt([
                'status' => 'error',
                'model' => $model,
                'request_id' => $requestId,
                'latency_ms' => (int) round((microtime(true) - $startedAt) * 1000),
                'input_title' => $inputTitle,
                'input_description' => $inputDescription,
                'error_message' => $e->getMessage(),
            ]);

            report($e);

            return response()->json([
                'status' => false,
                'message' => 'Failed to generate AI suggestion.',
            ], 500);
        }
    }

    private function isAssistantEnabled(): bool
    {
        $value = (string) ($this->listocean()->table('static_options')->where('option_name', 'ai_listing_assistant_enabled')->value('option_value') ?? '');

        return in_array(strtolower(trim($value)), ['1', 'true', 'yes', 'on', 'enabled'], true);
    }

    private function dailyLimit(): int
    {
        $value = (string) ($this->listocean()->table('static_options')->where('option_name', 'ai_listing_assistant_daily_limit')->value('option_value') ?? '');
        $int = (int) $value;

        return $int > 0 ? $int : 20;
    }

    private function model(): string
    {
        $value = (string) ($this->listocean()->table('static_options')->where('option_name', 'ai_listing_assistant_model')->value('option_value') ?? '');
        $value = trim($value);

        return $value !== '' ? $value : 'gpt-4o-mini';
    }

    private function dailyLimitCacheKey(Request $request): string
    {
        $userId = $request->user()?->id;
        $identity = $userId ? 'u:' . $userId : 'ip:' . $request->ip();

        return 'ai_listing_assistant_daily:' . now()->format('Y-m-d') . ':' . $identity;
    }

    private function redactPII(string $text): string
    {
        $text = preg_replace('/[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,}/i', '[redacted-email]', $text) ?? $text;

        // Very loose phone matcher: +country and 10+ digits overall.
        $text = preg_replace('/\+?\d[\d\s().-]{8,}\d/', '[redacted-phone]', $text) ?? $text;

        return $text;
    }

    private function sanitizeOutput(string $text): string
    {
        $text = trim($text);
        $text = $this->redactPII($text);

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

    private function logAttempt(array $data): void
    {
        try {
            $conn = $this->listocean();
            $schema = $conn->getSchemaBuilder();
            if (! $schema->hasTable('ai_listing_assistant_logs')) {
                return;
            }

            $now = now();

            $conn->table('ai_listing_assistant_logs')->insert([
                'source' => 'sellupnow_api',
                'user_id' => request()->user()?->id,
                'ip' => request()->ip(),
                'user_agent' => (string) request()->userAgent(),
                'status' => (string) ($data['status'] ?? 'unknown'),
                'model' => (string) ($data['model'] ?? null),
                'request_id' => (string) ($data['request_id'] ?? null),
                'latency_ms' => isset($data['latency_ms']) ? (int) $data['latency_ms'] : null,
                'input_title' => isset($data['input_title']) ? Str::limit((string) $data['input_title'], 500, '') : null,
                'input_description' => isset($data['input_description']) ? Str::limit((string) $data['input_description'], 2000, '') : null,
                'output_title' => isset($data['output_title']) ? Str::limit((string) $data['output_title'], 500, '') : null,
                'output_subtitle' => isset($data['output_subtitle']) ? Str::limit((string) $data['output_subtitle'], 500, '') : null,
                'output_description' => isset($data['output_description']) ? Str::limit((string) $data['output_description'], 2000, '') : null,
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
