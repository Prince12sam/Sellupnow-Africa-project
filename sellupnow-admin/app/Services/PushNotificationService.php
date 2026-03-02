<?php

namespace App\Services;

use App\Models\DeviceKey;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * PushNotificationService
 *
 * Sends FCM push notifications using the legacy HTTP API.
 *
 * Required .env key:
 *   FCM_SERVER_KEY=AAAA...  (from Firebase console → Project Settings → Cloud Messaging)
 */
class PushNotificationService
{
    private const FCM_URL = 'https://fcm.googleapis.com/fcm/send';

    /**
     * Send a push notification to one or more users by user ID.
     *
     * @param  int|array $userIds
     */
    public static function sendToUsers(
        int|array $userIds,
        string $title,
        string $body,
        array $data = []
    ): void {
        $fcmKey = config('services.fcm.server_key', env('FCM_SERVER_KEY'));
        if (! $fcmKey) {
            return; // FCM not configured — silently skip
        }

        $userIds = (array) $userIds;

        $tokens = DeviceKey::whereIn('user_id', $userIds)
            ->pluck('key')
            ->filter()
            ->unique()
            ->values()
            ->toArray();

        if (empty($tokens)) {
            return;
        }

        static::dispatch($fcmKey, $tokens, $title, $body, $data);
    }

    /**
     * Send a push notification to one or more raw FCM tokens.
     *
     * @param  string|array $tokens
     */
    public static function sendToTokens(
        string|array $tokens,
        string $title,
        string $body,
        array $data = []
    ): void {
        $fcmKey = config('services.fcm.server_key', env('FCM_SERVER_KEY'));
        if (! $fcmKey) {
            return;
        }

        $tokens = array_values(array_unique((array) $tokens));
        if (empty($tokens)) {
            return;
        }

        static::dispatch($fcmKey, $tokens, $title, $body, $data);
    }

    private static function dispatch(
        string $fcmKey,
        array $tokens,
        string $title,
        string $body,
        array $data
    ): void {
        // FCM allows up to 1000 tokens per request
        foreach (array_chunk($tokens, 1000) as $chunk) {
            $payload = [
                'registration_ids' => $chunk,
                'notification'     => [
                    'title' => $title,
                    'body'  => $body,
                    'sound' => 'default',
                ],
                'data'             => array_merge($data, ['title' => $title, 'body' => $body]),
                'priority'         => 'high',
            ];

            try {
                $response = Http::withHeaders([
                    'Authorization' => 'key=' . $fcmKey,
                    'Content-Type'  => 'application/json',
                ])->post(self::FCM_URL, $payload);

                if (! $response->successful()) {
                    Log::warning('FCM push failed', [
                        'status' => $response->status(),
                        'body'   => $response->body(),
                    ]);
                }
            } catch (\Throwable $e) {
                Log::error('FCM push exception: ' . $e->getMessage());
            }
        }
    }
}
