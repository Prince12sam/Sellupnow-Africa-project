<?php

namespace App\Services;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\PendingRequest;

class CountriesNowLocationImporter
{
    private const BASE_URL = 'https://countriesnow.space/api/v0.1';

    /**
     * Imports Country -> States -> Cities from CountriesNow into the Listocean SQLite tables.
     *
     * Returns:
     *  - ok (bool)
     *  - states_created (int)
     *  - states_existing (int)
     *  - cities_created (int)
     *  - cities_existing (int)
     *  - error (string|null)
     */
    public function import(int $countryId, string $countryName): array
    {
        $countryName = trim($countryName);
        if ($countryName === '') {
            return $this->fail('Country name is empty.');
        }

        $db = DB::connection('listocean');

        $client = $this->makeClient();
        $usingInsecure = false;

        try {
            $statesResponse = $client->post(self::BASE_URL.'/countries/states', [
                'country' => $countryName,
            ]);
        } catch (\Throwable $th) {
            if ($this->shouldRetryWithoutSslVerify($th)) {
                try {
                    $client = $this->makeClient(withoutVerifying: true);
                    $usingInsecure = true;
                    $statesResponse = $client->post(self::BASE_URL.'/countries/states', [
                        'country' => $countryName,
                    ]);
                } catch (\Throwable $th2) {
                    return $this->fail($th2->getMessage());
                }
            } else {
                return $this->fail($th->getMessage());
            }
        }

        if (! $statesResponse->ok()) {
            return $this->fail('CountriesNow states API failed (HTTP '.$statesResponse->status().').');
        }

        $statesJson = $statesResponse->json();
        $states = Arr::get($statesJson, 'data.states', []);
        if (! is_array($states) || empty($states)) {
            return $this->fail('No states returned by CountriesNow for '.$countryName.'.');
        }

        $existingStates = $db->table('states')
            ->where('country_id', $countryId)
            ->pluck('id', 'state')
            ->all();

        $statesCreated = 0;
        $statesExisting = 0;
        $citiesCreated = 0;
        $citiesExisting = 0;

        foreach ($states as $stateRow) {
            $stateName = trim((string) Arr::get($stateRow, 'name', ''));
            if ($stateName === '') {
                continue;
            }

            $stateId = $existingStates[$stateName] ?? null;
            if (! $stateId) {
                $stateId = (int) $db->table('states')->insertGetId([
                    'country_id' => $countryId,
                    'state' => $stateName,
                    'timezone' => null,
                    'status' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                $existingStates[$stateName] = $stateId;
                $statesCreated++;
            } else {
                $statesExisting++;
            }

            try {
                $citiesResponse = $client->post(self::BASE_URL.'/countries/state/cities', [
                    'country' => $countryName,
                    'state' => $stateName,
                ]);
            } catch (\Throwable $th) {
                if (! $usingInsecure && $this->shouldRetryWithoutSslVerify($th)) {
                    try {
                        $client = $this->makeClient(withoutVerifying: true);
                        $usingInsecure = true;
                        $citiesResponse = $client->post(self::BASE_URL.'/countries/state/cities', [
                            'country' => $countryName,
                            'state' => $stateName,
                        ]);
                    } catch (\Throwable $th2) {
                        continue;
                    }
                } else {
                    continue;
                }
            }

            if (! $citiesResponse->ok()) {
                // Skip this state on API error, keep others.
                continue;
            }

            $citiesJson = $citiesResponse->json();
            $cities = Arr::get($citiesJson, 'data', []);
            if (! is_array($cities) || empty($cities)) {
                continue;
            }

            $existingCities = $db->table('cities')
                ->where('state_id', $stateId)
                ->pluck('id', 'city')
                ->all();

            foreach ($cities as $cityNameRaw) {
                $cityName = trim((string) $cityNameRaw);
                if ($cityName === '') {
                    continue;
                }

                if (isset($existingCities[$cityName])) {
                    $citiesExisting++;
                    continue;
                }

                $cityId = (int) $db->table('cities')->insertGetId([
                    'country_id' => $countryId,
                    'state_id' => $stateId,
                    'city' => $cityName,
                    'status' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                $existingCities[$cityName] = $cityId;
                $citiesCreated++;
            }
        }

        return [
            'ok' => true,
            'states_created' => $statesCreated,
            'states_existing' => $statesExisting,
            'cities_created' => $citiesCreated,
            'cities_existing' => $citiesExisting,
            'error' => null,
        ];
    }

    private function makeClient(bool $withoutVerifying = false): PendingRequest
    {
        $client = Http::acceptJson()
            ->timeout((int) config('countriesnow.timeout', 25))
            ->retry(1, 250, throw: false);

        if ($withoutVerifying) {
            return $client->withoutVerifying();
        }

        $caBundle = (string) config('countriesnow.ca_bundle', '');
        if ($caBundle !== '') {
            return $client->withOptions([
                'verify' => $caBundle,
            ]);
        }

        return $client;
    }

    private function shouldRetryWithoutSslVerify(\Throwable $th): bool
    {
        $message = $th->getMessage();
        $isCurl60 = str_contains($message, 'cURL error 60') || str_contains($message, 'SSL certificate problem');

        if (! $isCurl60) {
            return false;
        }

        // Only allow insecure fallback in local/debug to avoid weakening production security.
        if (! app()->environment(['local', 'development', 'testing']) && ! (bool) config('app.debug', false)) {
            return false;
        }

        return (bool) config('countriesnow.allow_insecure_fallback', true);
    }

    private function fail(string $message): array
    {
        return [
            'ok' => false,
            'states_created' => 0,
            'states_existing' => 0,
            'cities_created' => 0,
            'cities_existing' => 0,
            'error' => $message,
        ];
    }
}
