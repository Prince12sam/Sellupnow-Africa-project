<?php

namespace App\Repositories;

use Abedin\Maker\Repositories\Repository;
use App\Http\Requests\CurrencyRequest;
use App\Models\Currency;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CurrencyRepository extends Repository
{
    public static function model()
    {
        return Currency::class;
    }

    public static function storeByRequest(CurrencyRequest $request): Currency
    {
        return self::create([
            'name' => $request->name,
            'code' => $request->code,
            'symbol' => $request->symbol,
            'rate' => $request->rate,
        ]);
    }

    public static function updateByRequest(CurrencyRequest $request, Currency $currency): Currency
    {
        $currency->update([
            'name' => $request->name,
            'code' => $request->code,
            'symbol' => $request->symbol,
            'rate' => $request->rate,
        ]);

        // If the updated currency is the default, push symbol/code to listocean
        $currency->refresh();
        if ($currency->is_default) {
            self::syncToListocean($currency);
        }

        return $currency;
    }

    public static function setDefaultCurrency(Currency $currency): void
    {
        self::query()->where('is_default', true)->update(['is_default' => false]);

        $currency->update(['is_default' => true, 'rate' => 1]);

        // Push the new default currency to the listocean frontend
        self::syncToListocean($currency);
    }

    /**
     * Sync the default currency's symbol and code to the listocean frontend
     * so that amount_with_currency_symbol() displays the correct symbol.
     */
    public static function syncToListocean(Currency $currency): void
    {
        try {
            $now = now()->toDateTimeString();

            $pairs = [
                // Direct symbol — frontend helper reads this first
                'site_currency_symbol' => $currency->symbol ?: '$',
                // Currency code/label — used for payment gateway text mode
                'site_global_currency' => ($currency->code ?: $currency->name),
            ];

            foreach ($pairs as $optionName => $optionValue) {
                $exists = DB::connection('listocean')
                    ->table('static_options')
                    ->where('option_name', $optionName)
                    ->exists();

                if ($exists) {
                    DB::connection('listocean')
                        ->table('static_options')
                        ->where('option_name', $optionName)
                        ->update(['option_value' => $optionValue, 'updated_at' => $now]);
                } else {
                    DB::connection('listocean')
                        ->table('static_options')
                        ->insert([
                            'option_name'  => $optionName,
                            'option_value' => $optionValue,
                            'created_at'   => $now,
                            'updated_at'   => $now,
                        ]);
                }
            }
        } catch (\Throwable $e) {
            Log::warning('CurrencyRepository::syncToListocean failed: ' . $e->getMessage());
        }
    }
}
