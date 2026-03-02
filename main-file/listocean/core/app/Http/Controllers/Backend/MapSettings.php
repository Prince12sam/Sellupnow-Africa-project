<?php

namespace App\Http\Controllers\Backend;

use App\Helpers\FlashMsg;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class MapSettings extends Controller
{
    public function addMapSettings()
    {
        return view('backend.map-settings.map-settings');
    }
    public function UpdateMapSettings(Request $request)
    {
        $request->validate([
            'google_map_settings_on_off' => 'nullable',
            'google_map_api_key' => 'nullable',
            'google_map_search_placeholder_title' => 'nullable',
            'google_map_search_button_title' => 'nullable'
        ]);

        update_static_option('google_map_settings_on_off',$request->google_map_settings_on_off);
        update_static_option('google_map_api_key',$request->google_map_api_key);
        update_static_option('google_map_search_placeholder_title',$request->google_map_search_placeholder_title);
        update_static_option('google_map_search_button_title',$request->google_map_search_button_title);
        return redirect()->back()->with(FlashMsg::settings_update());
    }
}
