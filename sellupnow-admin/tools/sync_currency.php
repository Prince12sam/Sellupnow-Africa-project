<?php
/**
 * One-time script: sync the admin's default currency to listocean static_options.
 * Run from the sellupnow-admin directory:
 *   php artisan tinker --execute="require 'tools/sync_currency.php';"
 * Or simply: php tools/sync_currency.php  (bootstraps Laravel manually)
 */

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Currency;
use App\Repositories\CurrencyRepository;
use Illuminate\Support\Facades\DB;

$currency = Currency::where('is_default', 1)->first();

if (!$currency) {
    echo "ERROR: No default currency found in admin currencies table.\n";
    exit(1);
}

echo "Default currency: symbol={$currency->symbol}, code={$currency->code}, name={$currency->name}\n";

CurrencyRepository::syncToListocean($currency);

// Verify
$symbol = DB::connection('listocean')->table('static_options')->where('option_name', 'site_currency_symbol')->value('option_value');
$code   = DB::connection('listocean')->table('static_options')->where('option_name', 'site_global_currency')->value('option_value');

echo "Listocean static_options after sync:\n";
echo "  site_currency_symbol  = {$symbol}\n";
echo "  site_global_currency  = {$code}\n";
echo "Done.\n";
