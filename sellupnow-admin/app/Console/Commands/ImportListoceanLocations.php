<?php

namespace App\Console\Commands;

use App\Services\CountriesNowLocationImporter;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ImportListoceanLocations extends Command
{
    protected $signature = 'listocean:import-locations {countryId? : Listocean countries.id} {--country= : Country name to look up in listocean countries table}';

    protected $description = 'Import Listocean Cities/Towns (states/cities tables) from CountriesNow for a given country';

    public function handle(CountriesNowLocationImporter $importer): int
    {
        $db = DB::connection('listocean');

        $countryId = $this->argument('countryId');
        $countryName = (string) $this->option('country');

        if (! $countryId && $countryName === '') {
            $this->error('Provide {countryId} or --country="Ghana"');
            return self::INVALID;
        }

        if (! $countryId) {
            $row = $db->table('countries')->where('country', $countryName)->first();
            if (! $row) {
                $this->error('Country not found in listocean DB: '.$countryName);
                return self::FAILURE;
            }
            $countryId = (int) $row->id;
            $countryName = (string) $row->country;
        } else {
            $row = $db->table('countries')->where('id', (int) $countryId)->first();
            if (! $row) {
                $this->error('Country not found in listocean DB by id: '.$countryId);
                return self::FAILURE;
            }
            $countryId = (int) $row->id;
            $countryName = (string) $row->country;
        }

        $this->info('Importing for country_id='.$countryId.' country='.$countryName);

        $result = $importer->import($countryId, $countryName);

        if (! ($result['ok'] ?? false)) {
            $this->error('Import failed: '.(string) ($result['error'] ?? 'unknown error'));
            $this->line('Tip: On Windows, set COUNTRIESNOW_CA_BUNDLE or keep fallback enabled in local env.');
            return self::FAILURE;
        }

        $this->info('Done. Cities(created)='.(int) ($result['states_created'] ?? 0).' Towns(created)='.(int) ($result['cities_created'] ?? 0));

        return self::SUCCESS;
    }
}
