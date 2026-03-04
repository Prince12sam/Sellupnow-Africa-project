<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Media;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ListoceanHomepageHeroController extends Controller
{
    private const HERO_ADDON_NAME = 'HeaderStyleOne';
    private const HERO_ADDON_NAMESPACE = 'plugins\\PageBuilder\\Addons\\Header\\HeaderStyleOne';
    private const HERO_ADDON_PAGE_TYPE = 'dynamic_page';

    public function edit()
    {
        $homePageId = (int) (DB::connection('listocean')
            ->table('static_options')
            ->where('option_name', 'home_page')
            ->value('option_value') ?? 0);

        if ($homePageId <= 0) {
            return back()->withErrors(['hero' => 'Listocean home page is not configured (static option: home_page).']);
        }

        $heroRow = $this->getHeroRow($homePageId);
        if (!$heroRow) {
            return back()->withErrors(['hero' => 'Listocean homepage hero addon (HeaderStyleOne) was not found in page builder.']);
        }

        $settings = $this->decodeSettings($heroRow->addon_settings ?? null);
        $heroEnabled = (int) ($settings['hero_enabled'] ?? 1);
        $currentAttachmentId = (int) ($settings['background_image'] ?? 0);
        $currentAttachmentId2 = (int) ($settings['background_image_2'] ?? 0);
        $currentAttachmentId3 = (int) ($settings['background_image_3'] ?? 0);
        $paddingTop = (int) ($settings['padding_top'] ?? 0);
        $paddingBottom = (int) ($settings['padding_bottom'] ?? 0);
        $backgroundPosition = (string) ($settings['background_position'] ?? '');

        $currentImageUrl = $currentAttachmentId > 0
            ? $this->listoceanMediaUrlById($currentAttachmentId)
            : null;

        $currentImageUrl2 = $currentAttachmentId2 > 0
            ? $this->listoceanMediaUrlById($currentAttachmentId2)
            : null;

        $currentImageUrl3 = $currentAttachmentId3 > 0
            ? $this->listoceanMediaUrlById($currentAttachmentId3)
            : null;

        return view('admin.listocean.homepage-hero', [
            'homePageId' => $homePageId,
            'heroRowId' => (int) $heroRow->id,
            'heroEnabled' => $heroEnabled,
            'currentAttachmentId' => $currentAttachmentId,
            'currentImageUrl' => $currentImageUrl,
            'currentAttachmentId2' => $currentAttachmentId2,
            'currentImageUrl2' => $currentImageUrl2,
            'currentAttachmentId3' => $currentAttachmentId3,
            'currentImageUrl3' => $currentImageUrl3,
            'paddingTop' => $paddingTop,
            'paddingBottom' => $paddingBottom,
            'backgroundPosition' => $backgroundPosition,
        ]);
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'background_image' => ['nullable', 'string'],
            'background_image_2' => ['nullable', 'string'],
            'background_image_3' => ['nullable', 'string'],
            'background_position' => ['nullable', 'string', 'max:64'],
            'padding_top' => ['nullable', 'integer', 'min:0', 'max:500'],
            'padding_bottom' => ['nullable', 'integer', 'min:0', 'max:500'],
            'hero_enabled' => ['nullable'],
        ]);

        $homePageId = (int) (DB::connection('listocean')
            ->table('static_options')
            ->where('option_name', 'home_page')
            ->value('option_value') ?? 0);

        if ($homePageId <= 0) {
            return back()->withErrors(['background_image' => 'Listocean home page is not configured (static option: home_page).']);
        }

        $heroRow = $this->getHeroRow($homePageId);
        if (!$heroRow) {
            return back()->withErrors(['background_image' => 'Listocean homepage hero addon (HeaderStyleOne) was not found in page builder.']);
        }

        try {
            $settings = $this->decodeSettings($heroRow->addon_settings ?? null);

            // Checkbox: when unchecked, the key will be missing. Default to disabled in that case.
            $settings['hero_enabled'] = $request->boolean('hero_enabled') ? '1' : '0';

            if (!empty($validated['background_image'])) {
                $attachmentId = $this->resolveListoceanAttachmentId($validated['background_image']);
                if (!$attachmentId) {
                    return back()->withErrors(['background_image' => 'Could not sync the selected image into Listocean media library. Please pick an image that exists in SellUpNow storage.']);
                }

                $settings['background_image'] = (int) $attachmentId;
            }

            if (!empty($validated['background_image_2'] ?? null)) {
                $attachmentId2 = $this->resolveListoceanAttachmentId($validated['background_image_2']);
                if (!$attachmentId2) {
                    return back()->withErrors(['background_image_2' => 'Could not sync the selected image into Listocean media library. Please pick an image that exists in SellUpNow storage.']);
                }
                $settings['background_image_2'] = (int) $attachmentId2;
            }

            if (!empty($validated['background_image_3'] ?? null)) {
                $attachmentId3 = $this->resolveListoceanAttachmentId($validated['background_image_3']);
                if (!$attachmentId3) {
                    return back()->withErrors(['background_image_3' => 'Could not sync the selected image into Listocean media library. Please pick an image that exists in SellUpNow storage.']);
                }
                $settings['background_image_3'] = (int) $attachmentId3;
            }

            if (!is_null($validated['padding_top'] ?? null)) {
                $settings['padding_top'] = (string) (int) $validated['padding_top'];
            }

            if (!is_null($validated['padding_bottom'] ?? null)) {
                $settings['padding_bottom'] = (string) (int) $validated['padding_bottom'];
            }

            if (!is_null($validated['background_position'] ?? null)) {
                $settings['background_position'] = (string) $validated['background_position'];
            }

            DB::connection('listocean')
                ->table('page_builders')
                ->where('id', (int) $heroRow->id)
                ->update([
                    'addon_settings' => json_encode($settings),
                    'updated_at' => now(),
                ]);

        } catch (\Throwable $e) {
            Log::error('Failed updating Listocean homepage hero background image: ' . $e->getMessage());
            return back()->withErrors(['background_image' => 'Failed to update Listocean hero settings. Check logs for details.']);
        }

        return back()->withSuccess(__('Homepage hero updated successfully'));
    }

    private function getHeroRow(int $homePageId): ?object
    {
        return DB::connection('listocean')
            ->table('page_builders')
            ->where('addon_page_id', $homePageId)
            ->where('addon_page_type', self::HERO_ADDON_PAGE_TYPE)
            ->where(function ($q) {
                $q->where('addon_name', self::HERO_ADDON_NAME)
                    ->orWhere('addon_namespace', self::HERO_ADDON_NAMESPACE);
            })
            ->orderBy('addon_order')
            ->first();
    }

    private function decodeSettings(?string $json): array
    {
        if (!$json) {
            return [];
        }

        $decoded = json_decode($json, true);
        return is_array($decoded) ? $decoded : [];
    }

    private function listoceanBaseUrl(): string
    {
        $url = rtrim((string) config('listocean.base_url', ''), '/');
        return $url !== '' ? $url : 'http://127.0.0.1:8090';
    }

    private function listoceanMediaUrlById(int $id): ?string
    {
        $path = DB::connection('listocean')->table('media_uploads')->where('id', $id)->value('path');
        if (!$path) {
            return null;
        }

        return $this->listoceanBaseUrl() . '/assets/uploads/media-uploader/' . ltrim((string) $path, '/');
    }

    private function resolveListoceanAttachmentId(?string $value): ?int
    {
        if (!$value) {
            return null;
        }

        // If already numeric (attachment id)
        if (ctype_digit((string) $value)) {
            $id = (int) $value;

            // If it already exists in Listocean media library, reuse it.
            $existsInListocean = DB::connection('listocean')
                ->table('media_uploads')
                ->where('id', $id)
                ->exists();

            if ($existsInListocean) {
                return $id;
            }

            // Otherwise treat it as SellUpNow admin media ID and mirror into Listocean.
            $adminMedia = Media::query()->find($id);
            $adminSrc = trim((string) ($adminMedia?->src ?? ''));
            if ($adminSrc !== '') {
                $sourcePath = Storage::disk('public')->path($adminSrc);
                if (is_file($sourcePath)) {
                    return $this->storeListoceanMediaFromLocalPath($sourcePath, basename($adminSrc));
                }
            }

            return null;
        }

        // If stored as a URL, try to map it back to a local storage path.
        if (filter_var($value, FILTER_VALIDATE_URL)) {
            $parts = parse_url($value);
            $path = $parts['path'] ?? '';

            // Common: /storage/xxx -> storage/app/public/xxx
            if (str_starts_with($path, '/storage/')) {
                $candidate = 'public/' . ltrim(substr($path, strlen('/storage/')), '/');
                if (Storage::exists($candidate)) {
                    $value = $candidate;
                }
            }
        }

        // Common normalization: public disk paths are stored as 'public/xxx' or just 'xxx'.
        if (str_starts_with($value, 'public/')) {
            $value = ltrim(substr($value, strlen('public/')), '/');
        }

        // Expect a SellUpNow storage path on either the default disk or the public disk.
        if (Storage::exists($value)) {
            $sourcePath = Storage::path($value);
        } elseif (Storage::disk('public')->exists($value)) {
            $sourcePath = Storage::disk('public')->path($value);
        } else {
            return null;
        }

        $originalName = basename($value);
        return $this->storeListoceanMediaFromLocalPath($sourcePath, $originalName);
    }

    private function storeListoceanMediaFromLocalPath(string $sourcePath, string $originalName): ?int
    {
        $targetDir = $this->resolvePrimaryListoceanMediaUploaderDirectory();
        if (! $targetDir) {
            $targetDir = listocean_core_path('public/assets/uploads/media-uploader');
        }
        if (!is_dir($targetDir)) {
            @mkdir($targetDir, 0775, true);
        }

        if (!is_file($sourcePath) || !is_dir($targetDir)) {
            return null;
        }

        $ext = pathinfo($originalName, PATHINFO_EXTENSION);
        $ext = $ext ? strtolower($ext) : 'jpg';

        $safeBase = pathinfo($originalName, PATHINFO_FILENAME);
        $safeBase = preg_replace('/[^a-zA-Z0-9_\-]/', '-', $safeBase) ?: 'hero';

        $fileName = $safeBase . '-' . time() . '.' . $ext;
        $targetPath = $targetDir . DIRECTORY_SEPARATOR . $fileName;

        if (!@copy($sourcePath, $targetPath)) {
            return null;
        }

        // Best effort mirror to any additional candidate frontend media directories.
        foreach ($this->resolveListoceanMediaUploaderDirectories() as $dir) {
            if ($dir === $targetDir) {
                continue;
            }

            if (! is_dir($dir)) {
                $parent = dirname($dir);
                if (! is_dir($parent)) {
                    continue;
                }
                @mkdir($dir, 0775, true);
            }

            $mirrorTarget = $dir . DIRECTORY_SEPARATOR . $fileName;
            if (! is_file($mirrorTarget)) {
                @copy($targetPath, $mirrorTarget);
            }
        }

        $db = DB::connection('listocean');
        $existing = $db->table('media_uploads')->where('path', $fileName)->value('id');
        if ($existing) {
            return (int) $existing;
        }

        $type = in_array($ext, ['png', 'jpg', 'jpeg', 'gif', 'webp', 'svg'], true) ? 'image' : 'file';

        return (int) $db->table('media_uploads')->insertGetId([
            'title' => $safeBase,
            'path' => $fileName,
            'alt' => $safeBase,
            'size' => null,
            'dimensions' => null,
            'user_id' => null,
            'type' => $type,
            'created_at' => now(),
            'updated_at' => now(),
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

        // 1. Explicit override env var
        $configured = trim((string) env('LISTOCEAN_MEDIA_UPLOADER_DIR', ''));
        if ($configured !== '') {
            $configured = rtrim(str_replace('\\', '/', $configured), '/');
            if (! str_ends_with($configured, 'media-uploader')) {
                $configured .= '/assets/uploads/media-uploader';
            }
            $candidates[] = $configured;
        }

        // 2. Derive from LISTOCEAN_PUBLIC_PATH (same env var used by the listocean_media
        //    filesystem disk in filesystems.php and MediaRepository — use this on VPS)
        $publicPath = trim((string) env('LISTOCEAN_PUBLIC_PATH', ''));
        if ($publicPath !== '') {
            $candidates[] = rtrim(str_replace('\\', '/', $publicPath), '/') . '/assets/uploads/media-uploader';
        }

        // 3. Relative fallback — correct path after listocean/assets/ was removed
        $candidates[] = rtrim(str_replace('\\', '/', base_path('../main-file/listocean/core/public/assets/uploads/media-uploader')), '/');

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
}
