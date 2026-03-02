<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ListoceanMapSettingsController extends Controller
{
    private function listocean()
    {
        return DB::connection('listocean');
    }

    private function getOption(string $key, string $default = ''): string
    {
        return (string) ($this->listocean()->table('static_options')
            ->where('option_name', $key)
            ->value('option_value') ?? $default);
    }

    private function setOption(string $key, string $value): void
    {
        $this->listocean()->table('static_options')->updateOrInsert(
            ['option_name' => $key],
            ['option_value' => $value, 'updated_at' => now()]
        );
    }

    public function index()
    {
        $settings = [
            'google_map_settings_on_off'         => $this->getOption('google_map_settings_on_off'),
            'google_map_api_key'                 => $this->getOption('google_map_api_key'),
            'google_map_search_placeholder_title' => $this->getOption('google_map_search_placeholder_title', 'Search by location'),
            'google_map_search_button_title'      => $this->getOption('google_map_search_button_title', 'Search'),
        ];

        return view('admin.map-settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'google_map_api_key'                 => 'nullable|string|max:512',
            'google_map_search_placeholder_title' => 'nullable|string|max:255',
            'google_map_search_button_title'      => 'nullable|string|max:255',
        ]);

        $this->setOption('google_map_settings_on_off', $request->boolean('google_map_settings_on_off') ? 'on' : '');
        $this->setOption('google_map_api_key', (string) ($request->google_map_api_key ?? ''));
        $this->setOption('google_map_search_placeholder_title', (string) ($request->google_map_search_placeholder_title ?? ''));
        $this->setOption('google_map_search_button_title', (string) ($request->google_map_search_button_title ?? ''));

        return back()->withSuccess(__('Map settings updated successfully'));
    }
}
