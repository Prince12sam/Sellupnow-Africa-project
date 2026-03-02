<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ListoceanFlashSaleWidgetController extends Controller
{
    private const OPTION_ENABLED = 'flash_sale_widget_enabled';
    private const OPTION_PLACEMENTS = 'flash_sale_widget_placements';

    private const PAGES = [
        'home' => 'Homepage',
        'listings' => 'Listings page',
        'category' => 'Category page',
        'subcategory' => 'Subcategory page',
        'child_category' => 'Child category page',
        'listing_details' => 'Listing details page',
        'dynamic_page' => 'Dynamic page',
    ];

    private const SLOTS = [
        'before_content' => 'Before content',
        'after_content' => 'After content',
    ];

    public function edit()
    {
        $enabled = (int) ($this->getOption(self::OPTION_ENABLED) ?? 0);
        $placements = $this->decodeJsonOption(self::OPTION_PLACEMENTS);

        return view('admin.listocean.flash-sale-widget', [
            'enabled' => $enabled === 1,
            'pages' => self::PAGES,
            'slots' => self::SLOTS,
            'placements' => is_array($placements) ? $placements : [],
        ]);
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'enabled' => ['nullable'],
            'placements' => ['nullable', 'array'],
            'placements.*' => ['nullable', 'array'],
            'placements.*.*' => ['nullable', 'in:' . implode(',', array_keys(self::SLOTS))],
        ]);

        $enabled = $request->boolean('enabled') ? '1' : '0';
        $placements = (array) ($validated['placements'] ?? []);

        // Normalize: keep only known pages/slots.
        $normalized = [];
        foreach (array_keys(self::PAGES) as $pageKey) {
            $pageSlots = (array) ($placements[$pageKey] ?? []);
            $pageSlots = array_values(array_intersect($pageSlots, array_keys(self::SLOTS)));
            $normalized[$pageKey] = $pageSlots;
        }

        try {
            $this->setOption(self::OPTION_ENABLED, $enabled);
            $this->setOption(self::OPTION_PLACEMENTS, json_encode($normalized));
        } catch (\Throwable $e) {
            Log::error('Failed updating Listocean flash sale widget placements: ' . $e->getMessage());
            return back()->withErrors(['enabled' => 'Failed to update widget settings. Check logs for details.']);
        }

        return back()->withSuccess(__('Flash sale widget settings updated successfully'));
    }

    private function getOption(string $key): ?string
    {
        return DB::connection('listocean')
            ->table('static_options')
            ->where('option_name', $key)
            ->value('option_value');
    }

    private function setOption(string $key, string $value): void
    {
        $db = DB::connection('listocean');
        $exists = $db->table('static_options')->where('option_name', $key)->exists();
        if ($exists) {
            $db->table('static_options')->where('option_name', $key)->update([
                'option_value' => $value,
            ]);
            return;
        }

        $db->table('static_options')->insert([
            'option_name' => $key,
            'option_value' => $value,
        ]);
    }

    private function decodeJsonOption(string $key): array
    {
        $raw = $this->getOption($key);
        if (!$raw) {
            return [];
        }

        $decoded = json_decode($raw, true);
        return is_array($decoded) ? $decoded : [];
    }
}
