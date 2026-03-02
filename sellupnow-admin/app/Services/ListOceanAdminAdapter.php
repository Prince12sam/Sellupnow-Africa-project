<?php

namespace App\Services;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ListOceanAdminAdapter
{
    public function enabled(): bool
    {
        return (bool) config('listocean.enabled', false);
    }

    public function forward(Request $request, string $method, string $endpoint, ?array $payload = null, ?array $query = null): JsonResponse
    {
        $baseUrl = rtrim((string) config('listocean.base_url', ''), '/');
        $adminPrefix = '/'.trim((string) config('listocean.admin_prefix', '/api/v1/admin'), '/');
        $endpoint = '/'.ltrim($endpoint, '/');
        $url = $baseUrl.$adminPrefix.$endpoint;

        if ($baseUrl === '') {
            return response()->json([
                'message' => 'ListOcean adapter is enabled but LISTOCEAN_BASE_URL is missing.',
            ], 500);
        }

        $token = (string) config('listocean.token', '');
        if ($token === '' && (bool) config('listocean.forward_bearer', true)) {
            $token = (string) $request->bearerToken();
        }

        $client = Http::acceptJson()->timeout((int) config('listocean.timeout', 15));
        if ($token !== '') {
            $client = $client->withToken($token);
        }

        try {
            $response = match (strtoupper($method)) {
                'GET' => $client->get($url, $query ?? $request->query()),
                'POST' => $client->post($url, $payload ?? $request->all()),
                'PUT' => $client->put($url, $payload ?? $request->all()),
                'PATCH' => $client->patch($url, $payload ?? $request->all()),
                'DELETE' => $client->delete($url, $payload ?? []),
                default => $client->send(strtoupper($method), $url, [
                    'json' => $payload ?? $request->all(),
                    'query' => $query ?? $request->query(),
                ]),
            };

            $body = $response->json();
            if (!is_array($body)) {
                $body = [
                    'message' => 'ListOcean adapter response is not valid JSON.',
                    'raw' => $response->body(),
                ];
            }

            return response()->json($body, $response->status());
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'ListOcean adapter request failed.',
                'error' => $th->getMessage(),
            ], 502);
        }
    }
}
