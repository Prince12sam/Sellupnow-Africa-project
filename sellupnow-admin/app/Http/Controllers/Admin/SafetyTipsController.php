<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SafetyTipsController extends Controller
{
    public function edit()
    {
        // read from listocean connection
        // Prefer the canonical key used by the frontend: `safety_tips_info`.
        $textInfo = DB::connection('listocean')->table('static_options')->where('option_name', 'safety_tips_info')->value('option_value');
        $textLegacy = DB::connection('listocean')->table('static_options')->where('option_name', 'safety_tips_text')->value('option_value');
        $text = $textInfo ?? $textLegacy ?? '';
        $color = DB::connection('listocean')->table('static_options')->where('option_name','safety_tips_color')->value('option_value');
        return view('admin.safety_tips.edit', compact('text','color'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'safety_tips_info'  => 'nullable|string',
            'safety_tips_color' => 'nullable|string|max:50',
        ]);
        // Preserve HTML — the frontend renders with {!! !!}
        $text  = trim((string) ($request->input('safety_tips_info', '') ?? ''));
        $color = trim((string) ($request->input('safety_tips_color', '') ?? ''));

        // upsert into listocean static_options under both canonical and legacy keys
        DB::connection('listocean')->table('static_options')->updateOrInsert(
            ['option_name' => 'safety_tips_info'],
            ['option_value' => $text]
        );
        DB::connection('listocean')->table('static_options')->updateOrInsert(
            ['option_name' => 'safety_tips_text'],
            ['option_value' => $text]
        );
        DB::connection('listocean')->table('static_options')->updateOrInsert(
            ['option_name' => 'safety_tips_color'],
            ['option_value' => $color]
        );

        return redirect()->back()->with('success', 'Safety Tips updated');
    }
}
