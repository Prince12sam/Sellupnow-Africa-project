<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class ListoceanPageBuildersController extends Controller
{
    private function pageBuilders()
    {
        return DB::connection('listocean')->table('page_builders');
    }

    private function pages()
    {
        return DB::connection('listocean')->table('pages');
    }

    /**
     * Show all content blocks for a specific page.
     */
    public function page(int $page_id)
    {
        $page = $this->pages()->where('id', $page_id)->first();
        if (! $page) {
            abort(404);
        }

        $blocks = $this->pageBuilders()
            ->where('addon_page_id', $page_id)
            ->orderBy('addon_order')
            ->get();

        // Decode addon_settings for each block
        $blocks = $blocks->map(function ($block) {
            $settings = json_decode($block->addon_settings, true) ?? [];
            $block->settings = $settings;
            $block->has_text = ! empty($settings['text_editor'])
                || ! empty($settings['title'])
                || ! empty($settings['subtitle'])
                || ! empty($settings['address'])
                || ! empty($settings['contact_page_contact_info_01'])
                || ! empty($settings['about_page_info_01'])
                || ! empty($settings['our_team_01']);
            return $block;
        });

        return view('admin.page-builders.page', compact('page', 'blocks'));
    }

    /**
     * Show the edit form for a specific content block.
     */
    public function edit(int $pb_id)
    {
        $block = $this->pageBuilders()->where('id', $pb_id)->first();
        if (! $block) {
            abort(404);
        }

        $page = $this->pages()->where('id', $block->addon_page_id)->first();
        $settings = json_decode($block->addon_settings, true) ?? [];

        // For MarketPlaceOne: resolve current banner image so the view can preview it
        $bannerImageInfo = null;
        if ($block->addon_name === 'MarketPlaceOne') {
            $mediaId = $settings['banner_image_one'] ?? null;
            if ($mediaId) {
                $media = DB::connection('listocean')
                    ->table('media_uploads')
                    ->where('id', $mediaId)
                    ->first();
                if ($media) {
                    $bannerImageInfo = [
                        'id'   => $media->id,
                        'path' => $media->path,
                        'url'  => rtrim((string) config('listocean.base_url', ''), '/')
                               . '/assets/uploads/media-uploader/' . $media->path,
                    ];
                }
            }
        }

        return view('admin.page-builders.edit', compact('block', 'page', 'settings', 'bannerImageInfo'));
    }

    /**
     * Update a content block's addon_settings.
     */
    public function update(Request $request, int $pb_id)
    {
        $block = $this->pageBuilders()->where('id', $pb_id)->first();
        if (! $block) {
            abort(404);
        }

        $settings = json_decode($block->addon_settings, true) ?? [];
        $addonName = $settings['addon_name'] ?? '';

        // --- MarketPlaceOne: handle banner image file upload ---
        if ($addonName === 'MarketPlaceOne' && $request->hasFile('banner_image_file')) {
            $request->validate(['banner_image_file' => 'image|max:4096']);

            $file     = $request->file('banner_image_file');
            $filename = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '_', $file->getClientOriginalName());
            $destDir  = rtrim(env('LISTOCEAN_PUBLIC_PATH',
                listocean_core_path('public')), '/\\') . '/assets/uploads/media-uploader';

            if (! is_dir($destDir)) {
                mkdir($destDir, 0775, true);
            }

            $file->move($destDir, $filename);

            // Insert into ListOcean media_uploads table
            $newMediaId = DB::connection('listocean')->table('media_uploads')->insertGetId([
                'title'      => pathinfo($filename, PATHINFO_FILENAME),
                'path'       => $filename,
                'alt'        => $filename,
                'size'       => filesize($destDir . '/' . $filename),
                'dimensions' => '845x800',
                'user_id'    => 1,
                'type'       => 'image',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $settings['banner_image_one'] = $newMediaId;

            $this->pageBuilders()
                ->where('id', $pb_id)
                ->update([
                    'addon_settings' => json_encode($settings, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                ]);

            return redirect()
                ->route('admin.pageBuilders.page', $block->addon_page_id)
                ->with('success', 'Marketplace banner image updated successfully.');
        }

        // --- Raw JSON mode (used by generic fallback editor) ---
        if ($request->has('addon_settings_raw')) {
            $decoded = json_decode($request->addon_settings_raw, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                return back()->withErrors(['json' => 'Invalid JSON: ' . json_last_error_msg()])->withInput();
            }
            $settings = $decoded;
        } else {
            // --- Structured mode: update known simple scalar fields ---
            $simpleFields = [
                'title', 'subtitle', 'sub_title', 'address', 'email', 'phone',
                'contact_info', 'contact_info_title', 'contact_info_link',
                'button_title_one', 'button_link_one',
                'button_title_two', 'button_link_two',
                // MarketPlaceOne field names
                'button_one_title', 'button_one_link',
                'button_two_title', 'button_two_link',
                'padding_top', 'padding_bottom',
            ];

            foreach ($simpleFields as $field) {
                if ($request->has($field)) {
                    $settings[$field] = $request->input($field);
                }
            }

            // --- TextEditor addon: rich HTML field ---
            if (in_array($addonName, ['TextEditor']) && $request->has('text_editor')) {
                $settings['text_editor'] = $request->input('text_editor');
            }

            // --- FAQ / FaqOne: Q&A repeater ---
            if (in_array($addonName, ['Faq', 'FaqOne']) && $request->has('faq_titles')) {
                $titles = array_values(array_filter($request->input('faq_titles', []), fn($v) => $v !== null));
                $descs  = array_values($request->input('faq_descs', []));

                // Determine which nested key holds the FAQ items
                $faqKey = null;
                foreach ($settings as $k => $v) {
                    if (is_array($v) && isset($v['title_'])) {
                        $faqKey = $k;
                        break;
                    }
                }
                if ($faqKey) {
                    $settings[$faqKey]['title_']       = $titles;
                    $settings[$faqKey]['description_'] = $descs;
                }
            }

            // --- ContactInfo: social icons repeater ---
            if ($addonName === 'ContactInfo' && $request->has('icon_class')) {
                $icons  = array_values($request->input('icon_class', []));
                $links  = array_values($request->input('icon_link', []));
                $iconKey = null;
                foreach ($settings as $k => $v) {
                    if (is_array($v) && isset($v['icon_'])) {
                        $iconKey = $k;
                        break;
                    }
                }
                if ($iconKey) {
                    $settings[$iconKey]['icon_']       = $icons;
                    $settings[$iconKey]['icon_link_']  = $links;
                }
            }
        }

        $this->pageBuilders()
            ->where('id', $pb_id)
            ->update([
                'addon_settings' => json_encode($settings, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            ]);

        return redirect()
            ->route('admin.pageBuilders.page', $block->addon_page_id)
            ->with('success', '"' . $addonName . '" block updated successfully.');
    }
}
