<?php

namespace App\Http\Controllers\Admin;

use Exception;
use App\Models\Currency;
use App\Models\GeneraleSetting;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\AiPromptRequest;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\GeneraleSettingRequest;
use App\Repositories\GeneraleSettingRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Smalot\PdfParser\Parser;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use GuzzleHttp\Client;

class GeneraleSettingController extends Controller
{
    /**
     * Display a listing of the generale settings.
     */
    public function index()
    {
        $currencies = Currency::all();

        $customerWebUrl = rtrim((string) config('app.customer_web_url', ''), '/');

        $listoceanGeneralKeys = [
            // SEO
            'site_meta_tags',
            'site_meta_description',
            'og_meta_title',
            'og_meta_description',
            'og_meta_site_name',
            'og_meta_url',
            'og_meta_image',

            // Third party scripts / keys
            'site_disqus_key',
            'site_google_analytics',
            'tawk_api_key',
            'site_third_party_tracking_code',
            'site_google_captcha_v3_site_key',
            'site_google_captcha_v3_secret_key',

            // Adsense / misc
            'enable_google_adsense',
            'google_adsense_publisher_id',
            'google_adsense_customer_id',
            'instagram_access_token',
        ];

        $rows = $this->listocean()->table('static_options')
            ->whereIn('option_name', $listoceanGeneralKeys)
            ->get(['option_name', 'option_value']);

        $listoceanOptions = array_fill_keys($listoceanGeneralKeys, '');
        foreach ($rows as $row) {
            $listoceanOptions[$row->option_name] = (string) ($row->option_value ?? '');
        }

        $listoceanOgImageUrl = $this->getListoceanMediaUrlByStaticOption('og_meta_image', $customerWebUrl);
        $listoceanCustomCss = $this->readListoceanAssetFile('assets/frontend/css/dynamic-style.css', '/* Write Custom Css Here */');
        $listoceanCustomJs = $this->readListoceanAssetFile('assets/frontend/js/dynamic-script.js', '/* Write Custom js Here */');

        return view('admin.generale-setting', compact(
            'currencies',
            'listoceanOptions',
            'listoceanOgImageUrl',
            'listoceanCustomCss',
            'listoceanCustomJs'
        ));
    }

    private function regenerateListoceanHomeStaticFile(string $listoceanCore): void
    {
        $homeFile = $listoceanCore . '/storage/app/home_render.html';
        if (! file_exists($homeFile)) {
            return;
        }

        $androidLink = (string) ($this->listocean()->table('static_options')->where('option_name', 'android_app_link')->value('option_value') ?? '');
        $iosLink = (string) ($this->listocean()->table('static_options')->where('option_name', 'ios_app_link')->value('option_value') ?? '');

        $customerWebUrl = rtrim((string) config('app.customer_web_url', ''), '/');

        $insertion = '';
        if ($androidLink !== '' || $iosLink !== '') {
            $insertion .= '<div class="app-downloads mt-4">' . "\n    <div class=\"d-flex flex-wrap align-items-center gap-3 justify-content-center\">\n";
            if ($androidLink !== '') {
                $insertion .= '        <a href="' . htmlspecialchars($androidLink, ENT_QUOTES) . '" class="app-badge"><img src="' . ($customerWebUrl . '/assets/frontend/img/static/google-play-badge.svg') . '" alt="" /></a>\n';
            }
            if ($iosLink !== '') {
                $insertion .= '        <a href="' . htmlspecialchars($iosLink, ENT_QUOTES) . '" class="app-badge"><img src="' . ($customerWebUrl . '/assets/frontend/img/static/app-store-badge.svg') . '" alt="" /></a>\n';
            }
            $insertion .= '    </div>\n</div>\n';
        }

        if ($insertion === '') {
            return;
        }

        $contents = file_get_contents($homeFile);
        if ($contents === false) {
            return;
        }

        $needle = '<div class="lo-hero__chips"';
        $pos = strpos($contents, $needle);
        if ($pos !== false) {
            $new = substr_replace($contents, $insertion, $pos, 0);
            @file_put_contents($homeFile, $new);
        }
    }

    /**
     * Update the generale settings.
     */
    public function update(GeneraleSettingRequest $request)
    {
        GeneraleSettingRepository::updateByRequest($request);

        // Also flush the admin helper cache here in case the repository path was skipped.
        \Illuminate\Support\Facades\Cache::forget('generale_setting');

        // Keep Listocean footer widgets in sync with the single admin panel.
        try {
            $this->syncListoceanBrandingFromRequest($request);
            $this->syncListoceanFooterWidgetsFromRequest($request);
            $this->syncListoceanAuthImageFromRequest($request);
            $this->syncListoceanAppBadgesFromRequest($request);
            // Sync mobile app links so the customer web can read them via get_static_option
            $this->upsertListoceanStaticOption('android_app_link', (string) ($request->google_playstore_url ?? ''));
            $this->upsertListoceanStaticOption('ios_app_link', (string) ($request->app_store_url ?? ''));
            $this->clearListoceanCaches();
        } catch (\Throwable $e) {
            report($e);
        }

        return back()->withSuccess(__('Generale settings updated successfully'));
    }

    private function syncListoceanBrandingFromRequest(GeneraleSettingRequest $request): void
    {
        $siteLogoId = null;
        $siteFaviconId = null;
        $siteWhiteLogoId = null;

        if ($request->hasFile('logo')) {
            $file = $request->file('logo');
            if ($file) {
                $attachmentId = $this->postFileToListoceanApi($file) ?? $this->storeListoceanMediaUpload($file);
                if ($attachmentId) {
                    $this->upsertListoceanStaticOption('site_logo', (string) $attachmentId);
                    $siteLogoId = (int) $attachmentId;
                }
            }
        }

        if ($request->hasFile('favicon')) {
            $file = $request->file('favicon');
            if ($file) {
                $attachmentId = $this->postFileToListoceanApi($file) ?? $this->storeListoceanMediaUpload($file);
                if ($attachmentId) {
                    $this->upsertListoceanStaticOption('site_favicon', (string) $attachmentId);
                    $siteFaviconId = (int) $attachmentId;
                }
            }
        }

        if ($request->hasFile('footer_logo')) {
            $file = $request->file('footer_logo');
            if (! $file) {
                return;
            }

            $attachmentId = $this->postFileToListoceanApi($file) ?? $this->storeListoceanMediaUpload($file);
            if (! $attachmentId) {
                return;
            }

            $this->upsertListoceanStaticOption('site_white_logo', (string) $attachmentId);
            $siteWhiteLogoId = (int) $attachmentId;

            $this->syncListoceanFooterContactWidgetLogo((int) $attachmentId);
        }

        $settings = GeneraleSetting::query()->with(['mediaLogo', 'mediaFavicon', 'mediaFooterLogo'])->first();

        if (! $siteLogoId && ! $request->hasFile('logo')) {
            $existing = trim((string) ($settings?->mediaLogo?->src ?? ''));
            if ($existing !== '') {
                $siteLogoId = $this->storeListoceanMediaUploadFromAdminMediaPath($existing, 'site-logo');
                if ($siteLogoId) {
                    $this->upsertListoceanStaticOption('site_logo', (string) $siteLogoId);
                }
            }
        }

        if (! $siteFaviconId && ! $request->hasFile('favicon')) {
            $existing = trim((string) ($settings?->mediaFavicon?->src ?? ''));
            if ($existing !== '') {
                $siteFaviconId = $this->storeListoceanMediaUploadFromAdminMediaPath($existing, 'site-favicon');
                if ($siteFaviconId) {
                    $this->upsertListoceanStaticOption('site_favicon', (string) $siteFaviconId);
                }
            }
        }

        if (! $siteWhiteLogoId && ! $request->hasFile('footer_logo')) {
            $existing = trim((string) ($settings?->mediaFooterLogo?->src ?? ''));
            if ($existing !== '') {
                $siteWhiteLogoId = $this->storeListoceanMediaUploadFromAdminMediaPath($existing, 'site-white-logo');
                if ($siteWhiteLogoId) {
                    $this->upsertListoceanStaticOption('site_white_logo', (string) $siteWhiteLogoId);
                    $this->syncListoceanFooterContactWidgetLogo((int) $siteWhiteLogoId);
                }
            }
        }
    }

    private function syncListoceanFooterContactWidgetLogo(int $attachmentId): void
    {
        $footerContactWidgets = $this->listocean()
            ->table('widgets')
            ->whereIn('widget_location', ['footer_one', 'footer_two'])
            ->where('widget_name', 'ContactInfoWidget')
            ->get();

        foreach ($footerContactWidgets as $widget) {
            $decoded = $this->decodeListoceanWidgetContent((string) ($widget->widget_content ?? ''));
            if (! is_array($decoded)) {
                continue;
            }

            $decoded['image'] = (string) $attachmentId;

            $this->listocean()->table('widgets')->where('id', $widget->id)->update([
                'widget_content' => json_encode($decoded, JSON_UNESCAPED_SLASHES),
                'updated_at' => now(),
            ]);
        }
    }

    private function storeListoceanMediaUploadFromAdminMediaPath(string $relativePath, string $prefix): ?int
    {
        $relativePath = trim($relativePath);
        if ($relativePath === '') {
            return null;
        }

        $sourcePath = storage_path('app/public/' . ltrim($relativePath, '/'));
        if (! File::exists($sourcePath)) {
            $fallbackPublic = public_path(ltrim($relativePath, '/'));
            if (! File::exists($fallbackPublic)) {
                return null;
            }

            $sourcePath = $fallbackPublic;
        }

        $tmpDir = storage_path('app/tmp');
        if (! File::exists($tmpDir)) {
            File::makeDirectory($tmpDir, 0775, true);
        }

        $ext = pathinfo($sourcePath, PATHINFO_EXTENSION);
        $tmpPath = $tmpDir . DIRECTORY_SEPARATOR . $prefix . '-' . Str::random(12) . ($ext ? ('.' . $ext) : '');
        File::copy($sourcePath, $tmpPath);

        $uploaded = new UploadedFile($tmpPath, basename($sourcePath), null, null, true);
        return $this->storeListoceanMediaUpload($uploaded);
    }

    private function syncListoceanAuthImageFromRequest(GeneraleSettingRequest $request): void
    {
        // Listocean customer login/register page uses `register_page_image` attachment id.
        // Reuse the "shop login background" upload as the customer auth page image.
        if ($request->hasFile('shop_login_background')) {
            $file = $request->file('shop_login_background');
            if (! $file) {
                return;
            }

            $attachmentId = $this->storeListoceanMediaUpload($file);
            $this->upsertListoceanStaticOption('register_page_image', (string) $attachmentId);
            return;
        }

        // <x-image-picker> posts a hidden string path, not a real file upload.
        $raw = trim((string) $request->input('shop_login_background', ''));
        if ($raw === '') {
            return;
        }

        $relative = $this->normalizePublicDiskPath($raw);
        if ($relative === '' || preg_match('/^https?:\/\//i', $relative)) {
            return;
        }

        $sourcePath = storage_path('app/public/' . ltrim($relative, '/'));
        if (! File::exists($sourcePath)) {
            // Best-effort fallback if the value is already a public URL path.
            $publicPath = public_path(ltrim($relative, '/'));
            if (! File::exists($publicPath)) {
                return;
            }
            $sourcePath = $publicPath;
        }

        $tmpDir = storage_path('app/tmp');
        if (! File::exists($tmpDir)) {
            File::makeDirectory($tmpDir, 0775, true);
        }

        $ext = pathinfo($sourcePath, PATHINFO_EXTENSION);
        $tmpPath = $tmpDir . DIRECTORY_SEPARATOR . 'listocean-auth-' . Str::random(12) . ($ext ? ('.' . $ext) : '');
        File::copy($sourcePath, $tmpPath);

        $uploaded = new UploadedFile($tmpPath, basename($sourcePath), null, null, true);
        $attachmentId = $this->storeListoceanMediaUpload($uploaded);
        $this->upsertListoceanStaticOption('register_page_image', (string) $attachmentId);
    }

    private function normalizePublicDiskPath(string $value): string
    {
        $value = trim($value);
        if ($value === '') {
            return $value;
        }

        // Full URL -> try to reduce to its path (e.g. https://host/storage/foo.jpg -> /storage/foo.jpg)
        if (preg_match('/^https?:\/\//i', $value)) {
            $parsed = parse_url($value);
            if (is_array($parsed) && isset($parsed['path']) && is_string($parsed['path'])) {
                $value = $parsed['path'];
            } else {
                return $value;
            }
        }

        $value = ltrim($value, '/');
        if (str_starts_with($value, 'storage/')) {
            return ltrim(substr($value, strlen('storage/')), '/');
        }

        return $value;
    }

    public function updateListoceanGeneralSettings(Request $request)
    {
        $data = $request->validate([
            // SEO
            'site_meta_tags' => 'nullable|string',
            'site_meta_description' => 'nullable|string',
            'og_meta_title' => 'nullable|string',
            'og_meta_description' => 'nullable|string',
            'og_meta_site_name' => 'nullable|string',
            'og_meta_url' => 'nullable|string',
            'og_meta_image_upload' => 'nullable|image|mimes:png,jpg,jpeg,svg,webp|max:5120',

            // Third party scripts / keys
            'site_disqus_key' => 'nullable|string',
            'site_google_analytics' => 'nullable|string',
            'tawk_api_key' => 'nullable|string',
            'site_third_party_tracking_code' => 'nullable|string',
            'site_google_captcha_v3_site_key' => 'nullable|string',
            'site_google_captcha_v3_secret_key' => 'nullable|string',

            // Adsense / misc
            'enable_google_adsense' => 'nullable',
            'google_adsense_publisher_id' => 'nullable|string',
            'google_adsense_customer_id' => 'nullable|string',
            'instagram_access_token' => 'nullable|string',

            // Custom assets
            'custom_css_area' => 'nullable|string',
            'custom_js_area' => 'nullable|string',

            // Escrow settings
            'escrow_enabled' => 'nullable',
            'escrow_fee_percent' => 'nullable|numeric|min:0',
            'escrow_currency' => 'nullable|string',
            'escrow_min_price' => 'nullable|numeric|min:0',
            'escrow_max_price' => 'nullable|numeric|min:0',
            'escrow_seller_accept_hours' => 'nullable|integer|min:0',
            'escrow_buyer_confirm_hours' => 'nullable|integer|min:0',
        ]);

        $this->upsertListoceanStaticOption('enable_google_adsense', $request->boolean('enable_google_adsense') ? 'on' : '');

        if ($request->hasFile('og_meta_image_upload')) {
            $id = $this->storeListoceanMediaUpload($request->file('og_meta_image_upload'));
            $this->upsertListoceanStaticOption('og_meta_image', (string) $id);
        }


        $staticKeys = [
            'site_meta_tags',
            'site_meta_description',
            'og_meta_title',
            'og_meta_description',
            'og_meta_site_name',
            'og_meta_url',

            'site_disqus_key',
            'site_google_analytics',
            'tawk_api_key',
            'site_third_party_tracking_code',
            'site_google_captcha_v3_site_key',
            'site_google_captcha_v3_secret_key',

            'google_adsense_publisher_id',
            'google_adsense_customer_id',
            'instagram_access_token',

            // Escrow settings
            'escrow_enabled',
            'escrow_fee_percent',
            'escrow_currency',
            'escrow_min_price',
            'escrow_max_price',
            'escrow_seller_accept_hours',
            'escrow_buyer_confirm_hours',
        ];

        foreach ($staticKeys as $key) {
            $this->upsertListoceanStaticOption($key, (string) ($data[$key] ?? ''));
        }

        // Custom CSS/JS is written directly to the customer frontend — restrict to root only.
        if (auth()->user()?->hasRole('root')) {
            $this->writeListoceanAssetFile('assets/frontend/css/dynamic-style.css', (string) ($data['custom_css_area'] ?? ''));
            $this->writeListoceanAssetFile('assets/frontend/js/dynamic-script.js', (string) ($data['custom_js_area'] ?? ''));
        }

        try {
            $this->clearListoceanCaches();
        } catch (\Throwable $e) {
        }

        return back()->withSuccess(__('Customer web settings updated successfully'));
    }

    private function readListoceanAssetFile(string $relativePath, string $default): string
    {
        $path = listocean_core_path() . DIRECTORY_SEPARATOR . str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $relativePath);
        if (File::exists($path) && File::isFile($path)) {
            $contents = @file_get_contents($path);
            if (is_string($contents) && $contents !== '') {
                return $contents;
            }
        }
        return $default;
    }

    private function writeListoceanAssetFile(string $relativePath, string $contents): void
    {
        $path = listocean_core_path() . DIRECTORY_SEPARATOR . str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $relativePath);
        $dir = dirname($path);
        if (! File::exists($dir)) {
            File::makeDirectory($dir, 0775, true);
        }
        @file_put_contents($path, $contents);
    }

    private function syncListoceanFooterWidgetsFromRequest(GeneraleSettingRequest $request): void
    {
        $address = trim((string) ($request->input('address') ?? ''));
        $phone = trim((string) ($request->input('footer_phone') ?? $request->input('mobile') ?? ''));
        $email = trim((string) ($request->input('footer_email') ?? $request->input('email') ?? ''));

        $footerText = trim((string) ($request->input('footer_text') ?? ''));
        if ($footerText !== '' && !str_contains($footerText, '{copy}') && !str_contains($footerText, '{year}')) {
            $footerText = '{copy} {year} ' . $footerText;
        }

        // Update ContactInfoWidget used in footer columns.
        $widgets = $this->listocean()
            ->table('widgets')
            ->whereIn('widget_location', ['footer_one', 'footer_two'])
            ->where('widget_name', 'ContactInfoWidget')
            ->get();

        foreach ($widgets as $widget) {
            $decoded = $this->decodeListoceanWidgetContent((string) ($widget->widget_content ?? ''));
            if (!is_array($decoded)) {
                continue;
            }

            if ($address !== '') {
                $decoded['address'] = $address;
            }
            if ($phone !== '') {
                $decoded['phone'] = $phone;
            }
            if ($email !== '') {
                $decoded['email'] = $email;
            }

            $this->listocean()->table('widgets')->where('id', $widget->id)->update([
                'widget_content' => json_encode($decoded, JSON_UNESCAPED_SLASHES),
                'updated_at' => now(),
            ]);
        }

        // Update the copyright text widget.
        if ($footerText !== '') {
            $copyrightWidgets = $this->listocean()
                ->table('widgets')
                ->where('widget_location', 'copyright')
                ->where('widget_name', 'CopyrightText')
                ->get();

            foreach ($copyrightWidgets as $widget) {
                $decoded = $this->decodeListoceanWidgetContent((string) ($widget->widget_content ?? ''));
                if (!is_array($decoded)) {
                    $decoded = [];
                }

                $decoded['title'] = $footerText;

                $this->listocean()->table('widgets')->where('id', $widget->id)->update([
                    'widget_content' => json_encode($decoded, JSON_UNESCAPED_SLASHES),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    private function clearListoceanCaches(): void
    {
        $listoceanCore = listocean_core_path();
        $artisan = $listoceanCore . DIRECTORY_SEPARATOR . 'artisan';
        if (!File::exists($artisan)) {
            return;
        }

        $cmd = 'cd ' . escapeshellarg($listoceanCore) . ' && php artisan cache:clear && php artisan view:clear';
        @shell_exec($cmd);
    }

    private function listocean()
    {
        return DB::connection('listocean');
    }

    private function getListoceanMediaUrlByStaticOption(string $optionName, string $customerWebUrl): ?string
    {
        $id = $this->listocean()->table('static_options')->where('option_name', $optionName)->value('option_value');
        if (! $id) {
            return null;
        }

        $path = $this->listocean()->table('media_uploads')->where('id', (int) $id)->value('path');
        if (! $path) {
            return null;
        }

        return $customerWebUrl . '/assets/uploads/media-uploader/' . ltrim($path, '/');
    }

    private function upsertListoceanStaticOption(string $optionName, string $optionValue): void
    {
        $now = now();
        $this->listocean()->table('static_options')->updateOrInsert(
            ['option_name' => $optionName],
            ['option_value' => $optionValue, 'updated_at' => $now, 'created_at' => $now]
        );
    }


    private function decodeListoceanWidgetContent(string $raw): ?array
    {
        // Remove invalid control chars that can break JSON parsing.
        $clean = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F]/', '', $raw);
        $clean = is_string($clean) ? $clean : $raw;

        $decoded = json_decode($clean, true);
        if (is_array($decoded)) {
            return $decoded;
        }

        // Some widget_content values are stored with escaped quotes (e.g. {\"key\":\"val\"}).
        if (str_contains($clean, '\\"') || str_contains($clean, '{\"')) {
            $decoded = json_decode(stripslashes($clean), true);
            if (is_array($decoded)) {
                return $decoded;
            }
        }

        // Some records are double-encoded JSON strings.
        $maybeString = json_decode($clean, true);
        if (is_string($maybeString)) {
            $decoded = json_decode($maybeString, true);
            if (is_array($decoded)) {
                return $decoded;
            }
        }

        return null;
    }

    private function storeListoceanMediaUpload($uploadedFile): int
    {
        $originalName = (string) $uploadedFile->getClientOriginalName();

        // Derive the extension from the real MIME type — never trust the client-supplied extension.
        $allowedMimeExtensions = [
            'image/jpeg'  => 'jpg',
            'image/png'   => 'png',
            'image/gif'   => 'gif',
            'image/webp'  => 'webp',
            'image/svg+xml' => 'svg',
            'image/bmp'   => 'bmp',
            'image/tiff'  => 'tiff',
        ];
        $realMime = $uploadedFile->getMimeType() ?? '';
        $extension = $allowedMimeExtensions[$realMime] ?? null;
        if (! $extension) {
            // Fallback: use client extension only if it's in the safe list.
            $clientExt = strtolower((string) $uploadedFile->getClientOriginalExtension());
            $extension = in_array($clientExt, ['jpg','jpeg','png','gif','webp','svg','bmp','tiff'], true)
                ? $clientExt
                : 'jpg';
        }
        $baseName = pathinfo($originalName, PATHINFO_FILENAME);

        $slug = Str::slug($baseName);
        if (! $slug) {
            $slug = 'logo';
        }

        $timestamp = time();
        $fileName = $slug . $timestamp . ($extension ? ('.' . $extension) : '');

        $targetDir = $this->resolvePrimaryListoceanMediaUploaderDirectory();
        if (! $targetDir) {
            $targetDir = listocean_core_path('public/assets/uploads/media-uploader');
        }
        if (! File::exists($targetDir)) {
            File::makeDirectory($targetDir, 0775, true);
        }

        $uploadedFile->move($targetDir, $fileName);

        $allTargetDirs = $this->resolveListoceanMediaUploaderDirectories();
        foreach ($allTargetDirs as $dir) {
            if ($dir === $targetDir) {
                continue;
            }

            if (! File::exists($dir)) {
                $parent = dirname($dir);
                if (! File::exists($parent)) {
                    continue;
                }
                File::makeDirectory($dir, 0775, true);
            }

            $sourceFile = $targetDir . DIRECTORY_SEPARATOR . $fileName;
            $destinationFile = $dir . DIRECTORY_SEPARATOR . $fileName;
            if (File::exists($sourceFile) && ! File::exists($destinationFile)) {
                @File::copy($sourceFile, $destinationFile);
            }
        }

        $fullPath = $targetDir . DIRECTORY_SEPARATOR . $fileName;
        $bytes = File::size($fullPath);
        $size = $this->formatSize($bytes);

        $dimensions = null;
        try {
            $info = @getimagesize($fullPath);
            if (is_array($info) && isset($info[0], $info[1])) {
                $dimensions = $info[0] . ' x ' . $info[1] . ' pixels';
            }
        } catch (\Throwable $e) {
            $dimensions = null;
        }

        $now = now();

        return (int) $this->listocean()->table('media_uploads')->insertGetId([
            'title' => $originalName,
            'path' => $fileName,
            'alt' => null,
            'size' => $size,
            'dimensions' => $dimensions,
            'user_id' => null,
            'type' => 'admin',
            'created_at' => $now,
            'updated_at' => $now,
        ]);
    }

    private function resolvePrimaryListoceanMediaUploaderDirectory(): ?string
    {
        $dirs = $this->resolveListoceanMediaUploaderDirectories();
        return $dirs[0] ?? null;
    }

    private function resolveListoceanMediaUploaderDirectories(): array
    {
        $candidates = [];

        $configured = trim((string) env('LISTOCEAN_MEDIA_UPLOADER_DIR', ''));
        if ($configured !== '') {
            $configured = rtrim(str_replace('\\', '/', $configured), '/');
            if (! str_ends_with($configured, 'media-uploader')) {
                $configured .= '/assets/uploads/media-uploader';
            }
            $candidates[] = $configured;
        }

        // Primary fallback: inside core/public so Laravel's public_path() finds uploaded files.
        $candidates[] = rtrim(str_replace('\\', '/', listocean_core_path('public/assets/uploads/media-uploader')), '/');

        $valid = [];
        foreach ($candidates as $candidate) {
            if ($candidate === '' || in_array($candidate, $valid, true)) {
                continue;
            }

            $parent = dirname($candidate);
            if (File::exists($candidate) || File::exists($parent)) {
                $valid[] = $candidate;
            }
        }

        return $valid;
    }

    private function formatSize(int $bytes): string
    {
        if ($bytes < 1024 * 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        }

        return number_format($bytes / (1024 * 1024), 2) . ' MB';
    }

    private function syncListoceanAppBadgesFromRequest(GeneraleSettingRequest $request): void
    {
        // Android badge: accept file upload or x-image-picker string path
        if ($request->hasFile('android_app_badge')) {
            $file = $request->file('android_app_badge');
            if ($file) {
                $attachmentId = $this->postFileToListoceanApi($file) ?? $this->storeListoceanMediaUpload($file);
                if ($attachmentId) {
                    $this->upsertListoceanStaticOption('android_app_badge', (string) $attachmentId);
                }
            }
        } else {
            $raw = trim((string) $request->input('android_app_badge', ''));
            if ($raw !== '') {
                $relative = $this->normalizePublicDiskPath($raw);
                if ($relative !== '' && ! preg_match('/^https?:\/\//i', $relative)) {
                    $sourcePath = storage_path('app/public/' . ltrim($relative, '/'));
                    if (! File::exists($sourcePath)) {
                        $publicPath = public_path(ltrim($relative, '/'));
                        if (File::exists($publicPath)) {
                            $sourcePath = $publicPath;
                        } else {
                            $sourcePath = null;
                        }
                    }
                    if ($sourcePath && File::exists($sourcePath)) {
                        $tmpDir = storage_path('app/tmp');
                        if (! File::exists($tmpDir)) {
                            File::makeDirectory($tmpDir, 0775, true);
                        }
                        $ext = pathinfo($sourcePath, PATHINFO_EXTENSION);
                        $tmpPath = $tmpDir . DIRECTORY_SEPARATOR . 'android-badge-' . Str::random(12) . ($ext ? ('.' . $ext) : '');
                        File::copy($sourcePath, $tmpPath);
                        $uploaded = new UploadedFile($tmpPath, basename($sourcePath), null, null, true);
                        $attachmentId = $this->postFileToListoceanApi($uploaded) ?? $this->storeListoceanMediaUpload($uploaded);
                        if ($attachmentId) {
                            $this->upsertListoceanStaticOption('android_app_badge', (string) $attachmentId);
                        }
                    }
                }
            }
        }

        // iOS badge: same handling
        if ($request->hasFile('ios_app_badge')) {
            $file = $request->file('ios_app_badge');
            if ($file) {
                $attachmentId = $this->postFileToListoceanApi($file) ?? $this->storeListoceanMediaUpload($file);
                if ($attachmentId) {
                    $this->upsertListoceanStaticOption('ios_app_badge', (string) $attachmentId);
                }
            }
        } else {
            $raw = trim((string) $request->input('ios_app_badge', ''));
            if ($raw !== '') {
                $relative = $this->normalizePublicDiskPath($raw);
                if ($relative !== '' && ! preg_match('/^https?:\/\//i', $relative)) {
                    $sourcePath = storage_path('app/public/' . ltrim($relative, '/'));
                    if (! File::exists($sourcePath)) {
                        $publicPath = public_path(ltrim($relative, '/'));
                        if (File::exists($publicPath)) {
                            $sourcePath = $publicPath;
                        } else {
                            $sourcePath = null;
                        }
                    }
                    if ($sourcePath && File::exists($sourcePath)) {
                        $tmpDir = storage_path('app/tmp');
                        if (! File::exists($tmpDir)) {
                            File::makeDirectory($tmpDir, 0775, true);
                        }
                        $ext = pathinfo($sourcePath, PATHINFO_EXTENSION);
                        $tmpPath = $tmpDir . DIRECTORY_SEPARATOR . 'ios-badge-' . Str::random(12) . ($ext ? ('.' . $ext) : '');
                        File::copy($sourcePath, $tmpPath);
                        $uploaded = new UploadedFile($tmpPath, basename($sourcePath), null, null, true);
                        $attachmentId = $this->postFileToListoceanApi($uploaded) ?? $this->storeListoceanMediaUpload($uploaded);
                        if ($attachmentId) {
                            $this->upsertListoceanStaticOption('ios_app_badge', (string) $attachmentId);
                        }
                    }
                }
            }
        }
    }

    private function postFileToListoceanApi(UploadedFile $file): ?int
    {
        $base = rtrim(env('LISTOCEAN_API_BASE', ''), '/');
        $apiKey = env('LISTOCEAN_ADMIN_API_KEY', '');
        if ($base === '' || $apiKey === '') {
            return null;
        }

        try {
            $client = new Client(['base_uri' => $base, 'timeout' => 10]);
            $response = $client->request('POST', '/api/admin/badge-upload', [
                'headers' => [
                    'X-Admin-Api-Key' => $apiKey,
                ],
                'multipart' => [
                    [
                        'name' => 'file',
                        'contents' => fopen($file->getPathname(), 'r'),
                        'filename' => $file->getClientOriginalName(),
                    ],
                ],
            ]);

            $body = json_decode((string) $response->getBody(), true);
            if (! empty($body['success']) && ! empty($body['id'])) {
                return (int) $body['id'];
            }
            // Log unexpected response body for diagnosis
            \Log::warning('Listocean upload returned unexpected body', ['body' => $body]);
        } catch (\Throwable $e) {
            // Report the exception so the admin logs capture why the API call failed,
            // then fall back to the local copy method.
            report($e);
        }

        return null;
    }

    /**
     * Run the latest update script.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateCommand()
    {
        // Shell execution on the server — root only.
        abort_unless(auth()->user()?->hasRole('root'), 403, 'Only the root administrator may run server update commands.');

        $commands = config('installer.update_commands');

        $errors = [];

        $changeToBasePath = 'cd ' . base_path();
        foreach($commands as $command){
            try {
                shell_exec($changeToBasePath . ' && ' . $command);
            } catch (\Throwable $th) {
                $errors[] = $th->getMessage();
            }
        }

        if(!empty($errors)){
            return back()->with('runUpdateScriptError', $errors);
        }

        return back()->withSuccess(__('Latest Script Run Successfully'));
    }


    public function aiPromptIndex()
    {
        $enabledRaw = (string) ($this->listocean()->table('static_options')->where('option_name', 'ai_listing_assistant_enabled')->value('option_value') ?? '');
        $enabled = in_array(strtolower(trim($enabledRaw)), ['1', 'true', 'yes', 'on', 'enabled'], true);

        $dailyLimitRaw = (string) ($this->listocean()->table('static_options')->where('option_name', 'ai_listing_assistant_daily_limit')->value('option_value') ?? '');
        $dailyLimit = (int) $dailyLimitRaw;
        if ($dailyLimit <= 0) {
            $dailyLimit = 20;
        }

        $model = (string) ($this->listocean()->table('static_options')->where('option_name', 'ai_listing_assistant_model')->value('option_value') ?? 'gpt-4o-mini');
        $model = trim($model) !== '' ? trim($model) : 'gpt-4o-mini';

        $aiListingAssistant = [
            'enabled' => $enabled,
            'daily_limit' => $dailyLimit,
            'model' => $model,
        ];

        $aiListingAssistantLogs = collect();
        try {
            $conn = $this->listocean();
            if ($conn->getSchemaBuilder()->hasTable('ai_listing_assistant_logs')) {
                $aiListingAssistantLogs = $conn->table('ai_listing_assistant_logs')
                    ->orderByDesc('id')
                    ->limit(50)
                    ->get();
            }
        } catch (\Throwable $e) {
            report($e);
        }

        $recoEnabledRaw = (string) ($this->listocean()->table('static_options')->where('option_name', 'ai_recommendations_enabled')->value('option_value') ?? '');
        $recoEnabled = in_array(strtolower(trim($recoEnabledRaw)), ['1', 'true', 'yes', 'on', 'enabled'], true);

        $recoDailyLimitRaw = (string) ($this->listocean()->table('static_options')->where('option_name', 'ai_recommendations_daily_limit')->value('option_value') ?? '');
        $recoDailyLimit = (int) $recoDailyLimitRaw;
        if ($recoDailyLimit <= 0) {
            $recoDailyLimit = 20;
        }

        $recoModel = (string) ($this->listocean()->table('static_options')->where('option_name', 'ai_recommendations_model')->value('option_value') ?? 'gpt-4o-mini');
        $recoModel = trim($recoModel) !== '' ? trim($recoModel) : 'gpt-4o-mini';

        $aiRecommendations = [
            'enabled' => $recoEnabled,
            'daily_limit' => $recoDailyLimit,
            'model' => $recoModel,
        ];

        $aiRecommendationLogs = collect();
        try {
            $conn = $this->listocean();
            if ($conn->getSchemaBuilder()->hasTable('ai_recommendation_logs')) {
                $aiRecommendationLogs = $conn->table('ai_recommendation_logs')
                    ->orderByDesc('id')
                    ->limit(50)
                    ->get();
            }
        } catch (\Throwable $e) {
            report($e);
        }

        $chatEnabledRaw = (string) ($this->listocean()->table('static_options')->where('option_name', 'ai_frontend_chat_enabled')->value('option_value') ?? 'on');
        $chatEnabled = ! in_array(strtolower(trim($chatEnabledRaw)), ['0', 'false', 'no', 'off', 'disabled'], true);

        $chatDailyLimitRaw = (string) ($this->listocean()->table('static_options')->where('option_name', 'ai_frontend_chat_daily_limit')->value('option_value') ?? '40');
        $chatDailyLimit = (int) $chatDailyLimitRaw;
        if ($chatDailyLimit <= 0) {
            $chatDailyLimit = 40;
        }

        $chatModel = (string) ($this->listocean()->table('static_options')->where('option_name', 'ai_frontend_chat_model')->value('option_value') ?? 'gpt-4o-mini');
        $chatModel = trim($chatModel) !== '' ? trim($chatModel) : 'gpt-4o-mini';

        $aiFrontendChat = [
            'enabled' => $chatEnabled,
            'daily_limit' => $chatDailyLimit,
            'model' => $chatModel,
        ];

        $aiKnowledgeBaseDocs = collect();
        try {
            $conn = $this->listocean();
            if ($conn->getSchemaBuilder()->hasTable('ai_knowledge_base_documents')) {
                $kbQ = trim((string) request()->get('kb_q', ''));
                $q = $conn->table('ai_knowledge_base_documents')->select(['id', 'original_filename', 'mime', 'is_active', 'created_at']);
                if ($kbQ !== '') {
                    $q->where('original_filename', 'like', '%' . $kbQ . '%');
                }
                $aiKnowledgeBaseDocs = $q->orderByDesc('id')->limit(20)->get();
            }
        } catch (\Throwable $e) {
            report($e);
        }

        $aiFrontendChatLogs = collect();
        try {
            $conn = $this->listocean();
            if ($conn->getSchemaBuilder()->hasTable('ai_frontend_chat_logs')) {
                $aiFrontendChatLogs = $conn->table('ai_frontend_chat_logs')
                    ->orderByDesc('id')
                    ->limit(50)
                    ->get();
            }
        } catch (\Throwable $e) {
            report($e);
        }

        $escrowEnabledRaw = (string) ($this->listocean()->table('static_options')->where('option_name', 'escrow_enabled')->value('option_value') ?? '');
        $escrowEnabled = in_array(strtolower(trim($escrowEnabledRaw)), ['1', 'true', 'yes', 'on', 'enabled'], true);

        $escrowFeePercent = (float) ((string) ($this->listocean()->table('static_options')->where('option_name', 'escrow_fee_percent')->value('option_value') ?? '2.5'));
        if ($escrowFeePercent < 0) {
            $escrowFeePercent = 0;
        }

        $escrowMinPrice = (float) ((string) ($this->listocean()->table('static_options')->where('option_name', 'escrow_min_price')->value('option_value') ?? '0'));
        $escrowMaxPrice = (float) ((string) ($this->listocean()->table('static_options')->where('option_name', 'escrow_max_price')->value('option_value') ?? '999999999'));

        $sellerAcceptHours = (int) ((string) ($this->listocean()->table('static_options')->where('option_name', 'escrow_seller_accept_hours')->value('option_value') ?? '24'));
        if ($sellerAcceptHours <= 0) {
            $sellerAcceptHours = 24;
        }

        $buyerConfirmHours = (int) ((string) ($this->listocean()->table('static_options')->where('option_name', 'escrow_buyer_confirm_hours')->value('option_value') ?? '72'));
        if ($buyerConfirmHours <= 0) {
            $buyerConfirmHours = 72;
        }

        $escrowCurrency = trim((string) ($this->listocean()->table('static_options')->where('option_name', 'escrow_currency')->value('option_value') ?? 'GHS'));
        if ($escrowCurrency === '') {
            $escrowCurrency = 'GHS';
        }

        $includedRaw = (string) ($this->listocean()->table('static_options')->where('option_name', 'escrow_included_category_ids')->value('option_value') ?? '[]');
        $included = json_decode($includedRaw, true);
        if (! is_array($included)) {
            $included = [];
        }
        $included = array_values(array_unique(array_map('intval', $included)));

        $excludedRaw = (string) ($this->listocean()->table('static_options')->where('option_name', 'escrow_excluded_category_ids')->value('option_value') ?? '[]');
        $excluded = json_decode($excludedRaw, true);
        if (! is_array($excluded)) {
            $excluded = [];
        }
        $excluded = array_values(array_unique(array_map('intval', $excluded)));

        $escrowSettings = [
            'enabled' => $escrowEnabled,
            'fee_percent' => $escrowFeePercent,
            'min_price' => $escrowMinPrice,
            'max_price' => $escrowMaxPrice,
            'seller_accept_hours' => $sellerAcceptHours,
            'buyer_confirm_hours' => $buyerConfirmHours,
            'currency' => $escrowCurrency,
            'included_category_ids' => $included,
            'excluded_category_ids' => $excluded,
        ];

        $categories = collect();
        try {
            $categories = $this->listocean()->table('categories')->select(['id', 'name'])->where('status', 1)->orderBy('name')->get();
        } catch (\Throwable $e) {
            report($e);
        }

        return view('admin.aiPrompt.index', compact('aiListingAssistant', 'aiListingAssistantLogs', 'aiRecommendations', 'aiRecommendationLogs', 'aiFrontendChat', 'aiKnowledgeBaseDocs', 'aiFrontendChatLogs', 'escrowSettings', 'categories'));
    }

    public function aiPromptUpdate(AiPromptRequest $request)
    {
        GeneraleSettingRepository::updateByAiPromptRequest($request);
        return back()->withSuccess(__('AI Prompt updated successfully'));
    }

    public function aiPromptConfigure()
    {
        return view('admin.aiPrompt.configure');
    }
    public function aiPromptConfigureUpdate(Request $request)
    {
        $request->validate([
            'api_key'      => 'required',
            'organization' => 'nullable|string',
            'base_url'     => 'nullable|url',
        ]);

        try {
            $apiKey  = (string) $request->api_key;
            $org     = (string) ($request->organization ?? '');
            $baseUrl = (string) ($request->base_url ?? '');

            // Write to this admin app's .env
            $this->setEnv('OPENAI_API_KEY', $apiKey);
            $this->setEnv('OPENAI_ORGANIZATION', $org);
            $this->setEnv('OPENAI_BASE_URL', $baseUrl);

            // Also sync to the ListOcean frontend .env so it can call OpenAI directly
            $listoceanEnv = listocean_core_path('.env');
            if (is_file($listoceanEnv)) {
                $this->setEnvInFile($listoceanEnv, 'OPENAI_API_KEY', $apiKey);
                $this->setEnvInFile($listoceanEnv, 'OPENAI_ORGANIZATION', $org);
                $this->setEnvInFile($listoceanEnv, 'OPENAI_BASE_URL', $baseUrl);

                // Clear the ListOcean config cache so the new key takes effect immediately
                $listoceanCore = listocean_core_path();
                @shell_exec(PHP_BINARY . ' ' . escapeshellarg("{$listoceanCore}/artisan") . ' config:clear 2>&1');
            }

            Artisan::call('config:clear');
            Artisan::call('cache:clear');

            return back()->withSuccess(__('AI configuration updated successfully'));

        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function aiListingAssistantUpdate(Request $request)
    {
        $validated = $request->validate([
            'enabled' => 'nullable|boolean',
            'daily_limit' => 'nullable|integer|min:1|max:500',
            'model' => 'nullable|string|max:100',
        ]);

        try {
            $enabled = (bool) ($validated['enabled'] ?? false);
            $dailyLimit = (int) ($validated['daily_limit'] ?? 20);
            $model = trim((string) ($validated['model'] ?? 'gpt-4o-mini'));
            if ($model === '') {
                $model = 'gpt-4o-mini';
            }

            $this->upsertListoceanStaticOption('ai_listing_assistant_enabled', $enabled ? 'on' : '');
            $this->upsertListoceanStaticOption('ai_listing_assistant_daily_limit', (string) $dailyLimit);
            $this->upsertListoceanStaticOption('ai_listing_assistant_model', $model);

            return back()->withSuccess(__('Listing AI Assistant settings updated successfully'));
        } catch (\Throwable $e) {
            report($e);
            return back()->with('error', $e->getMessage());
        }
    }

    public function aiRecommendationsUpdate(Request $request)
    {
        $validated = $request->validate([
            'enabled' => 'nullable|boolean',
            'daily_limit' => 'nullable|integer|min:1|max:500',
            'model' => 'nullable|string|max:100',
        ]);

        try {
            $enabled = (bool) ($validated['enabled'] ?? false);
            $dailyLimit = (int) ($validated['daily_limit'] ?? 20);
            $model = trim((string) ($validated['model'] ?? 'gpt-4o-mini'));
            if ($model === '') {
                $model = 'gpt-4o-mini';
            }

            $this->upsertListoceanStaticOption('ai_recommendations_enabled', $enabled ? 'on' : '');
            $this->upsertListoceanStaticOption('ai_recommendations_daily_limit', (string) $dailyLimit);
            $this->upsertListoceanStaticOption('ai_recommendations_model', $model);

            return back()->withSuccess(__('AI recommendations settings updated successfully'));
        } catch (\Throwable $e) {
            report($e);
            return back()->with('error', $e->getMessage());
        }
    }

    public function aiFrontendChatUpdate(Request $request)
    {
        $validated = $request->validate([
            'enabled' => 'nullable|boolean',
            'daily_limit' => 'nullable|integer|min:1|max:500',
            'model' => 'nullable|string|max:100',
        ]);

        try {
            $enabled = (bool) ($validated['enabled'] ?? false);
            $dailyLimit = (int) ($validated['daily_limit'] ?? 40);
            $model = trim((string) ($validated['model'] ?? 'gpt-4o-mini'));
            if ($model === '') {
                $model = 'gpt-4o-mini';
            }

            $this->upsertListoceanStaticOption('ai_frontend_chat_enabled', $enabled ? 'on' : 'off');
            $this->upsertListoceanStaticOption('ai_frontend_chat_daily_limit', (string) $dailyLimit);
            $this->upsertListoceanStaticOption('ai_frontend_chat_model', $model);

            return back()->withSuccess(__('Frontend AI chat widget settings updated successfully'));
        } catch (\Throwable $e) {
            report($e);
            return back()->with('error', $e->getMessage());
        }
    }

    public function aiKnowledgeBaseUpload(Request $request)
    {
        $validated = $request->validate([
            'pdf' => 'required|file|mimetypes:application/pdf|max:10240',
        ]);

        try {
            $file = $request->file('pdf');
            if (! $file) {
                return back()->with('error', __('No file uploaded'));
            }

            $path = $file->getRealPath();
            if (! $path || ! is_file($path)) {
                return back()->with('error', __('Upload failed'));
            }

            $parser = new Parser();
            $pdf = $parser->parseFile($path);
            $text = (string) $pdf->getText();

            $text = preg_replace('/\s+/', ' ', $text) ?? $text;
            $text = trim($text);

            if ($text === '') {
                return back()->with('error', __('Could not extract text from PDF'));
            }

            // Keep DB size bounded
            if (strlen($text) > 500000) {
                $text = substr($text, 0, 500000);
            }

            $this->listocean()->table('ai_knowledge_base_documents')->insert([
                'source' => 'admin_upload_pdf',
                'original_filename' => (string) $file->getClientOriginalName(),
                'mime' => (string) ($file->getClientMimeType() ?? 'application/pdf'),
                'uploaded_by_admin_id' => (int) (auth('admin')->id() ?? 0) ?: null,
                'is_active' => 1,
                'content_text' => $text,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return back()->withSuccess(__('Knowledge base PDF uploaded successfully'));
        } catch (\Throwable $e) {
            report($e);
            return back()->with('error', $e->getMessage());
        }
    }

    public function aiKnowledgeBaseDelete(Request $request, int $id)
    {
        try {
            $conn = $this->listocean();
            if (! $conn->getSchemaBuilder()->hasTable('ai_knowledge_base_documents')) {
                return back()->with('error', __('Knowledge base table not found'));
            }

            $conn->table('ai_knowledge_base_documents')->where('id', $id)->delete();
            return back()->withSuccess(__('Knowledge base document deleted'));
        } catch (\Throwable $e) {
            report($e);
            return back()->with('error', $e->getMessage());
        }
    }

    public function aiKnowledgeBaseToggle(Request $request, int $id)
    {
        try {
            $conn = $this->listocean();
            if (! $conn->getSchemaBuilder()->hasTable('ai_knowledge_base_documents')) {
                return back()->with('error', __('Knowledge base table not found'));
            }

            $row = $conn->table('ai_knowledge_base_documents')->where('id', $id)->first();
            if (! $row) {
                return back()->with('error', __('Document not found'));
            }

            $new = empty($row->is_active) ? 1 : 0;
            $conn->table('ai_knowledge_base_documents')->where('id', $id)->update([
                'is_active' => $new,
                'updated_at' => now(),
            ]);

            return back()->withSuccess(__('Knowledge base document updated'));
        } catch (\Throwable $e) {
            report($e);
            return back()->with('error', $e->getMessage());
        }
    }

    public function aiKnowledgeBasePreview(Request $request, int $id)
    {
        try {
            $conn = $this->listocean();
            if (! $conn->getSchemaBuilder()->hasTable('ai_knowledge_base_documents')) {
                return response()->json(['message' => __('Knowledge base table not found')], 404);
            }

            $row = $conn->table('ai_knowledge_base_documents')
                ->select(['id', 'original_filename', 'content_text'])
                ->where('id', $id)
                ->first();

            if (! $row) {
                return response()->json(['message' => __('Document not found')], 404);
            }

            $text = (string) ($row->content_text ?? '');
            $text = trim($text);
            if (strlen($text) > 5000) {
                $text = substr($text, 0, 5000) . '...';
            }

            return response()->json([
                'message' => 'OK',
                'data' => [
                    'id' => (int) $row->id,
                    'filename' => (string) ($row->original_filename ?? ''),
                    'text' => $text,
                ],
            ]);
        } catch (\Throwable $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function escrowSettingsUpdate(Request $request)
    {
        $validated = $request->validate([
            'enabled' => 'nullable|boolean',
            'fee_percent' => 'required|numeric|min:0|max:50',
            'min_price' => 'required|numeric|min:0',
            'max_price' => 'required|numeric|min:0',
            'seller_accept_hours' => 'required|integer|min:1|max:720',
            'buyer_confirm_hours' => 'required|integer|min:1|max:720',
            'currency' => 'required|string|max:10',
            'included_category_ids' => 'nullable|array',
            'included_category_ids.*' => 'integer|min:1',
            'excluded_category_ids' => 'nullable|array',
            'excluded_category_ids.*' => 'integer|min:1',
        ]);

        try {
            $enabled = (bool) ($validated['enabled'] ?? false);
            $included = array_values(array_unique(array_map('intval', $validated['included_category_ids'] ?? [])));
            $excluded = array_values(array_unique(array_map('intval', $validated['excluded_category_ids'] ?? [])));

            $this->upsertListoceanStaticOption('escrow_enabled', $enabled ? 'on' : 'off');
            $this->upsertListoceanStaticOption('escrow_fee_percent', (string) (float) $validated['fee_percent']);
            $this->upsertListoceanStaticOption('escrow_min_price', (string) (float) $validated['min_price']);
            $this->upsertListoceanStaticOption('escrow_max_price', (string) (float) $validated['max_price']);
            $this->upsertListoceanStaticOption('escrow_seller_accept_hours', (string) (int) $validated['seller_accept_hours']);
            $this->upsertListoceanStaticOption('escrow_buyer_confirm_hours', (string) (int) $validated['buyer_confirm_hours']);
            $this->upsertListoceanStaticOption('escrow_currency', strtoupper(trim((string) $validated['currency'])));
            $this->upsertListoceanStaticOption('escrow_included_category_ids', json_encode($included));
            $this->upsertListoceanStaticOption('escrow_excluded_category_ids', json_encode($excluded));

            return back()->withSuccess(__('Escrow settings updated successfully'));
        } catch (\Throwable $e) {
            report($e);
            return back()->with('error', $e->getMessage());
        }
    }

    public function aiKnowledgeBaseClear(Request $request)
    {
        try {
            $conn = $this->listocean();
            if (! $conn->getSchemaBuilder()->hasTable('ai_knowledge_base_documents')) {
                return back()->with('error', __('Knowledge base table not found'));
            }

            $conn->table('ai_knowledge_base_documents')->delete();
            return back()->withSuccess(__('Knowledge base cleared'));
        } catch (\Throwable $e) {
            report($e);
            return back()->with('error', $e->getMessage());
        }
    }

}

