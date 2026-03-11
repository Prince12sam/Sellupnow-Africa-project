<?php

namespace App\Http\Controllers\Admin;

use App\Models\HomeTheme;
use App\Models\CategoryThemeColor;
use App\Models\HeaderFooterThemeColor;
use App\Models\ThemeColor;
use App\Models\OfferBanner;
use Illuminate\Http\Request;
use App\Models\GeneraleSetting;
use App\Http\Controllers\Controller;
use App\Repositories\ThemeColorRepository;
use App\Repositories\OfferBannerRepository;
use App\Repositories\GeneraleSettingRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ThemeColorController extends Controller
{
    public function index()
    {
        // Bootstrap: if no theme colors exist yet, create a default so the page can save.
        if (ThemeColorRepository::query()->count() === 0) {
            ThemeColorRepository::create([
                'theme_name' => 'default',
                'primary' => '#EE456B',
                'secondary' => '#1F2937',
                'variant_500' => '#EE456B',
                'variant_100' => '#1F2937',
                'is_default' => true,
            ]);
        }

        $themeColors = ThemeColorRepository::getAll();

        $currentThemeModel = ThemeColorRepository::query()->where('is_default', true)->first()
            ?? ThemeColorRepository::query()->first();
        $currentTheme = $currentThemeModel?->toArray() ?? [
            'id' => null,
            'theme_name' => 'default',
            'primary' => '#EE456B',
            'secondary' => '#1F2937',
        ];
        $homeThemes = HomeTheme::orderBy('id', 'asc')->get();

        // Category background palettes (separate from global frontend theme)
        $categoryPalettes = Schema::hasTable('category_theme_colors')
            ? CategoryThemeColor::orderByDesc('is_default')->orderBy('id')->get()
            : collect();
        if ($categoryPalettes->count() === 0) {
            $categoryPalettes = collect([
                CategoryThemeColor::create([
                    'name' => 'default',
                    'base_hex' => '#F6915D',
                    'is_default' => true,
                ]),
            ]);
            $this->syncListoceanCategoryPalette($categoryPalettes->first());
        } else {
            $this->syncListoceanCategoryPalette($categoryPalettes->firstWhere('is_default', true) ?? $categoryPalettes->first());
        }

        $headerFooterPalettes = Schema::hasTable('header_footer_theme_colors')
            ? HeaderFooterThemeColor::orderByDesc('is_default')->orderBy('id')->get()
            : collect();
        if ($headerFooterPalettes->count() === 0) {
            $headerFooterPalettes = collect([
                HeaderFooterThemeColor::create([
                    'name' => 'default',
                    'header_hex' => '#FFFFFF',
                    'footer_hex' => '#F9FAFB',
                    'is_default' => true,
                ]),
            ]);
            $this->syncListoceanHeaderFooterPalette($headerFooterPalettes->first());
        } else {
            $this->syncListoceanHeaderFooterPalette($headerFooterPalettes->firstWhere('is_default', true) ?? $headerFooterPalettes->first());
        }

        return view('admin.theme-color', compact('themeColors', 'currentTheme', 'homeThemes', 'categoryPalettes', 'headerFooterPalettes'));
    }

    public function storeCategoryPalette(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:50'],
            'base_hex' => ['required', 'regex:/^#[A-Fa-f0-9]{6}$/'],
        ]);

        $palette = CategoryThemeColor::create([
            'name' => $request->name,
            'base_hex' => $request->base_hex,
            'is_default' => false,
        ]);

        return back()->with('success', __('Category palette created successfully'));
    }

    public function setDefaultCategoryPalette(CategoryThemeColor $categoryThemeColor)
    {
        CategoryThemeColor::query()->update(['is_default' => false]);
        $categoryThemeColor->update(['is_default' => true]);

        $this->syncListoceanCategoryPalette($categoryThemeColor);

        return back()->with('success', __('Category palette updated successfully'));
    }

    public function updateHeaderFooterPalette(Request $request)
    {
        $request->validate([
            'header_bg_color' => ['required', 'regex:/^#[A-Fa-f0-9]{6}$/'],
            'footer_bg_color' => ['required', 'regex:/^#[A-Fa-f0-9]{6}$/'],
        ]);

        try {
            $db = DB::connection('listocean');

            $this->upsertListoceanStaticOption($db, 'header_bg_color', $request->header_bg_color);
            $this->upsertListoceanStaticOption($db, 'footer_bg_color', $request->footer_bg_color);
        } catch (\Throwable $e) {
            // Fail silently; admin theme page should still work.
        }

        return back()->with('success', __('Header and footer palette updated successfully'));
    }

    public function storeHeaderFooterPalette(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:50'],
            'header_hex' => ['required', 'regex:/^#[A-Fa-f0-9]{6}$/'],
            'footer_hex' => ['required', 'regex:/^#[A-Fa-f0-9]{6}$/'],
        ]);

        HeaderFooterThemeColor::create([
            'name' => $request->name,
            'header_hex' => $request->header_hex,
            'footer_hex' => $request->footer_hex,
            'is_default' => false,
        ]);

        return back()->with('success', __('Header/footer palette created successfully'));
    }

    public function setDefaultHeaderFooterPalette(HeaderFooterThemeColor $headerFooterThemeColor)
    {
        HeaderFooterThemeColor::query()->update(['is_default' => false]);
        $headerFooterThemeColor->update(['is_default' => true]);

        $this->syncListoceanHeaderFooterPalette($headerFooterThemeColor);

        return back()->with('success', __('Header/footer palette updated successfully'));
    }

    private function syncListoceanCategoryPalette(?CategoryThemeColor $palette): void
    {
        if (! $palette) {
            return;
        }

        try {
            $db = DB::connection('listocean');

            [$r, $g, $b] = $this->hexToRgb($palette->base_hex);

            $bgFrom = sprintf('rgba(%d, %d, %d, 0.2)', $r, $g, $b);
            $border = sprintf('rgba(%d, %d, %d, 0.32)', $r, $g, $b);

            foreach ([
                'category_card_bg_from' => $bgFrom,
                'category_card_border' => $border,
            ] as $key => $val) {
                $this->upsertListoceanStaticOption($db, $key, $val);
            }
        } catch (\Throwable $e) {
            // Fail silently; admin theme page should still work.
        }
    }

    private function upsertListoceanStaticOption($db, string $key, string $value): void
    {
        $exists = $db->table('static_options')->where('option_name', $key)->exists();

        if ($exists) {
            $db->table('static_options')->where('option_name', $key)->update(['option_value' => $value]);
            return;
        }

        $db->table('static_options')->insert(['option_name' => $key, 'option_value' => $value]);
    }

    private function syncListoceanHeaderFooterPalette(?HeaderFooterThemeColor $palette): void
    {
        if (! $palette) {
            return;
        }

        try {
            $db = DB::connection('listocean');

            $this->upsertListoceanStaticOption($db, 'header_bg_color', $palette->header_hex);
            $this->upsertListoceanStaticOption($db, 'footer_bg_color', $palette->footer_hex);
        } catch (\Throwable $e) {
            // Fail silently; admin theme page should still work.
        }
    }

    private function hexToRgb(string $hex): array
    {
        $hex = ltrim(trim($hex), '#');
        if (strlen($hex) !== 6) {
            return [246, 145, 93];
        }

        return [
            hexdec(substr($hex, 0, 2)),
            hexdec(substr($hex, 2, 2)),
            hexdec(substr($hex, 4, 2)),
        ];
    }

    public function update(Request $request)
    {
        $request->validate([
            'selected_id' => 'required|exists:theme_colors,id',
        ]);


        ThemeColorRepository::DefaultColorUpdate($request);

        return back()->with('success', __('Theme color updated successfully'));
    }

    public function change(Request $request)
    {
        $request->validate([
            'primary_color' => ['required', 'regex:/^#[A-Fa-f0-9]{6}$/'],
            'generated_color_variants' => 'required|string',
        ]);

        $decodedVariants = json_decode($request->generated_color_variants, true);
        if (! is_array($decodedVariants) || count($decodedVariants) === 0) {
            return back()->with('error', __('Please generate valid color variants.'));
        }

        // if (app()->environment('local')) {
        //     return back()->with('demoMode', __('Sorry! You can not change color in demo mode'));
        // }

        ThemeColorRepository::updateColorPalette($request);

        return back()->with('success', __('Theme color updated successfully'));
    }

    public function themeStatus(HomeTheme $homeTheme)
    {
        if ($homeTheme->is_active) {
            return back()->with('success', 'Theme status updated');
        }
        HomeTheme::where('is_active', true)->update(['is_active' => false]);
        ThemeColor::query()->update(['is_default' => false]);
        $homeTheme->update(['is_active' => true]);
        $themeColor = ThemeColor::where('theme_name', $homeTheme->theme_name)->first();
        if (! $themeColor) {
            return back()->with('error', __('Theme color configuration is missing for selected theme'));
        }
        $themeColor->update(['is_default' => true]);

        GeneraleSettingRepository::updateOrCreateThemeColor($themeColor->primary, $themeColor->secondary);
        ThemeColorRepository::changeStyleCSS($themeColor->primary, $themeColor->secondary);
        
        return back()->with('success', 'Theme status updated successfully');
    }

    public function offerBannerIndex(HomeTheme $homeTheme)
    {
        $offerBanners = OfferBanner::where('home_theme_id', $homeTheme->id)->orderBy('id', 'asc')->get();
        return view('admin.offerBanner.index', compact('offerBanners'));
    }
    public function offerBannerEdit(OfferBanner $offerBanner)
    {
        return view('admin.offerBanner.edit', compact('offerBanner'));
    }

    public function offerBannerUpdate(OfferBanner $offerBanner, Request $request)
    {
        $request->validate([
            'thumbnail' => 'nullable|string|max:255',
            'link' => 'nullable|url|max:255',
        ]);

        OfferBannerRepository::updateByRequest($offerBanner, $request);
        return back()->with('success', 'Offer Banner updated successfully');
    }

    public function destroyCategoryPalette(CategoryThemeColor $categoryThemeColor)
    {
        if ($categoryThemeColor->is_default) {
            return back()->with('error', __('Cannot delete the active palette.'));
        }
        $categoryThemeColor->delete();
        return back()->with('success', __('Category palette deleted successfully'));
    }

    public function destroyHeaderFooterPalette(HeaderFooterThemeColor $headerFooterThemeColor)
    {
        if ($headerFooterThemeColor->is_default) {
            return back()->with('error', __('Cannot delete the active palette.'));
        }
        $headerFooterThemeColor->delete();
        return back()->with('success', __('Header/footer palette deleted successfully'));
    }
}
