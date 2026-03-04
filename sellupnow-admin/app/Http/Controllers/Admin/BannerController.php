<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\BannerRequest;
use App\Models\Banner;
use App\Repositories\BannerRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class BannerController extends Controller
{
    /**
     * Display a listing of the banners.
     */
    public function index()
    {
        $rootShop = generaleSetting('rootShop');

        $banners = Banner::query()
            ->whereNull('shop_id')
            ->when($rootShop?->id, function ($query, $rootShopId) {
                $query->orWhere('shop_id', $rootShopId);
            })
            ->paginate(20);

        return view('admin.banner.index', compact('banners'));
    }

    /**
     * create new banner
     */
    public function create()
    {
        return view('admin.banner.create');
    }

    /**
     * store a new banner
     */
    public function store(BannerRequest $request)
    {
        $banner = BannerRepository::storeByRequest($request);
        // When creating a new Banner, insert a new advertisement row (allow multiple)
        $this->syncListoceanHomepageBannerAdvertisement($banner, $request->input('position', 'after_hero'), true);

        return to_route('admin.banner.index')->withSuccess(__('Banner created successfully'));
    }

    /**
     * edit a banner
     */
    public function edit(Banner $banner)
    {
        $currentPosition = $this->detectListoceanBannerPosition();
        return view('admin.banner.edit', compact('banner', 'currentPosition'));
    }

    /**
     * update a banner
     */
    public function update(BannerRequest $request, Banner $banner)
    {
        BannerRepository::updateByRequest($request, $banner);

        $this->syncListoceanHomepageBannerAdvertisement($banner->refresh(), $request->input('position', 'after_hero'));

        return to_route('admin.banner.index')->withSuccess(__('Banner updated successfully'));
    }

    /**
     * status toggle a banner
     */
    public function statusToggle(Banner $banner)
    {
        $banner->update([
            'status' => ! $banner->status,
        ]);

        $this->syncListoceanHomepageBannerAdvertisement($banner->refresh(), $this->detectListoceanBannerPosition());

        return to_route('admin.banner.index')->withSuccess(__('Banner status updated'));
    }

    /**
     * destroy a banner
     */
    public function destroy(Banner $banner)
    {
        $this->deleteListoceanHomepageBannerAdvertisement($banner);
        $banner->delete();

        return to_route('admin.banner.index')->withSuccess(__('Banner deleted successfully'));
    }

    /**
     * Detect whether the current banner is placed before or after the hero
     * by checking whether a frontend_ad_slots row exists for homepage_before_hero.
     */
    private function detectListoceanBannerPosition(): string
    {
        try {
            return DB::connection('listocean')
                ->table('frontend_ad_slots')
                ->where('slot_key', 'homepage_before_hero')
                ->exists() ? 'before_hero' : 'after_hero';
        } catch (\Throwable $e) {
            return 'after_hero';
        }
    }

    private function syncListoceanHomepageBannerAdvertisement(Banner $banner, string $position = 'after_hero', bool $forceInsert = false): void
    {
        // Wrap operations in transactions where possible and make mirroring
        // idempotent to avoid duplicate rows.
        try {
            $listoceanDb = DB::connection('listocean');
            $coreDb = DB::connection();

            $slot     = 'sellupnow:homepage_after_hero';
            $title    = $banner->title ?: 'Homepage Banner';
            $redirect = rtrim((string) config('listocean.base_url', ''), '/');
            if ($redirect === '') {
                $redirect = 'http://127.0.0.1:8090';
            }

            $attachmentId = $this->resolveListoceanAttachmentId($banner->banner);
            if (!$attachmentId) {
                return;
            }

            $isActive = $banner->status ? 1 : 0;
            $adStatus = ($position === 'before_hero') ? 0 : $isActive;

            // Prepare payload for both DBs (image may be replaced for core)
            $payload = [
                'title'        => $title,
                'type'         => 'image',
                'size'         => '1400*200',
                'image'        => $attachmentId,
                'slot'         => $slot,
                'redirect_url' => $redirect,
                'click'        => 0,
                'impression'   => 0,
                'status'       => $adStatus,
                'updated_at'   => now(),
            ];

            // Use a transaction on the listocean connection for the primary write.
            $listoceanDb->beginTransaction();
            try {
                if ($listoceanDb->table('advertisements')->where('slot', $slot)->exists() && ! $forceInsert) {
                    $listoceanDb->table('advertisements')->where('slot', $slot)->update($payload);
                } else {
                    $listoceanDb->table('advertisements')->insert($payload + ['created_at' => now()]);
                }

                $adId = (int) $listoceanDb->table('advertisements')->where('slot', $slot)->value('id');

                if ($position === 'before_hero') {
                    if ($adId) {
                        $listoceanDb->table('frontend_ad_slots')->updateOrInsert(
                            ['slot_key' => 'homepage_before_hero'],
                            [
                                'advertisement_id' => $adId,
                                'status'           => $isActive,
                                'start_at'         => null,
                                'end_at'           => null,
                                'updated_at'       => now(),
                                'created_at'       => now(),
                            ]
                        );
                    }
                } else {
                    $listoceanDb->table('frontend_ad_slots')->where('slot_key', 'homepage_before_hero')->delete();
                }

                $listoceanDb->commit();
            } catch (\Throwable $e) {
                $listoceanDb->rollBack();
                throw $e;
            }

            // Mirror into core DB. Make this idempotent by updating existing row
            // or inserting if missing. Also ensure media_uploads entry exists.
            $coreDb->beginTransaction();
            try {
                $coreMediaId = null;
                $listoceanMediaPath = $listoceanDb->table('media_uploads')->where('id', $attachmentId)->value('path');
                if ($listoceanMediaPath) {
                    $existingCoreMedia = $coreDb->table('media_uploads')->where('path', $listoceanMediaPath)->value('id');
                    if ($existingCoreMedia) {
                        $coreMediaId = (int) $existingCoreMedia;
                    } else {
                        $type = in_array(pathinfo($listoceanMediaPath, PATHINFO_EXTENSION), ['png','jpg','jpeg','gif','webp','svg'], true) ? 'image' : 'file';
                        $coreMediaId = (int) $coreDb->table('media_uploads')->insertGetId([
                            'title' => pathinfo($listoceanMediaPath, PATHINFO_FILENAME),
                            'path' => $listoceanMediaPath,
                            'alt' => pathinfo($listoceanMediaPath, PATHINFO_FILENAME),
                            'size' => null,
                            'dimensions' => null,
                            'user_id' => null,
                            'type' => $type,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }
                    // If file missing in core public, dispatch a job to copy it.
                    try {
                        $corePublicFile = base_path('..' . DIRECTORY_SEPARATOR . 'main-file' . DIRECTORY_SEPARATOR . 'listocean' . DIRECTORY_SEPARATOR . 'core' . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'media-uploader' . DIRECTORY_SEPARATOR . $listoceanMediaPath);
                        if (! file_exists($corePublicFile)) {
                            // Try synchronous copy from likely sellupnow locations first
                            $srcCandidates = [
                                base_path('public') . DIRECTORY_SEPARATOR . $listoceanMediaPath,
                                base_path('storage') . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . $listoceanMediaPath,
                                base_path('storage') . DIRECTORY_SEPARATOR . $listoceanMediaPath,
                            ];
                            $copied = false;
                            foreach ($srcCandidates as $src) {
                                if (is_file($src)) {
                                    @mkdir(dirname($corePublicFile), 0775, true);
                                    if (@copy($src, $corePublicFile)) {
                                        Log::info('Mirrored banner media synchronously: ' . $listoceanMediaPath);
                                        $copied = true;
                                        break;
                                    }
                                }
                            }
                            // If sync copy didn't work, dispatch job to attempt later
                            if (! $copied) {
                                \App\Jobs\MirrorBannerMedia::dispatch($listoceanMediaPath);
                            }
                        }
                    } catch (\Throwable $e) {
                        Log::warning('Failed to mirror or dispatch MirrorBannerMedia job: ' . $e->getMessage());
                    }
                }

                $corePayload = $payload;
                if ($coreMediaId) {
                    $corePayload['image'] = $coreMediaId;
                }

                // Use updateOrInsert for idempotency
                $coreDb->table('advertisements')->updateOrInsert(
                    ['slot' => $slot],
                    $corePayload + ['created_at' => now()]
                );

                // Mirror frontend_ad_slots into core as well
                if ($position === 'before_hero') {
                    if (isset($adId) && $adId) {
                        $coreDb->table('frontend_ad_slots')->updateOrInsert(
                            ['slot_key' => 'homepage_before_hero'],
                            [
                                'advertisement_id' => $adId,
                                'status'           => $isActive,
                                'start_at'         => null,
                                'end_at'           => null,
                                'updated_at'       => now(),
                                'created_at'       => now(),
                            ]
                        );
                    }
                } else {
                    $coreDb->table('frontend_ad_slots')->where('slot_key', 'homepage_before_hero')->delete();
                }

                $coreDb->commit();
            } catch (\Throwable $e) {
                $coreDb->rollBack();
                Log::warning('Failed to mirror advertisement to core DB: ' . $e->getMessage());
            }
        } catch (\Throwable $e) {
            Log::error('Failed to sync Listocean advertisement from banner: ' . $e->getMessage());
        }
    }

    private function deleteListoceanHomepageBannerAdvertisement(Banner $banner): void
    {
        try {
            $db = DB::connection('listocean');
            $slot = 'sellupnow:homepage_after_hero';
            $db->table('advertisements')->where('slot', $slot)->delete();
            $db->table('frontend_ad_slots')->where('slot_key', 'homepage_hero_banner')->delete();
        } catch (\Throwable $e) {
            Log::error('Failed to delete Listocean advertisement for banner: ' . $e->getMessage());
        }
    }

    private function resolveListoceanAttachmentId(?string $value): ?int
    {
        if (!$value) {
            return null;
        }

        // If already numeric (attachment id)
        if (ctype_digit((string) $value)) {
            return (int) $value;
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

        // Expect a Sellupnow storage path on either the default disk or the public disk.
        $disk = null;
        if (Storage::exists($value)) {
            $disk = Storage::getFacadeRoot();
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
        $targetDir = env('LISTOCEAN_PUBLIC_PATH')
            ? rtrim(str_replace('\\', '/', env('LISTOCEAN_PUBLIC_PATH')), '/') . '/assets/uploads/media-uploader'
            : listocean_core_path('public/assets/uploads/media-uploader');
        if (!is_dir($targetDir)) {
            @mkdir($targetDir, 0775, true);
        }

        if (!is_file($sourcePath) || !is_dir($targetDir)) {
            return null;
        }

        $ext = pathinfo($originalName, PATHINFO_EXTENSION);
        $ext = $ext ? strtolower($ext) : 'jpg';

        $safeBase = pathinfo($originalName, PATHINFO_FILENAME);
        $safeBase = preg_replace('/[^a-zA-Z0-9_\-]/', '-', $safeBase) ?: 'banner';

        $fileName = $safeBase . '-' . time() . '.' . $ext;
        $targetPath = $targetDir . DIRECTORY_SEPARATOR . $fileName;

        // For the homepage after-hero banner, resize to full-width strip (1400x200).
        $didResize = false;
        if (function_exists('imagecreatetruecolor')) {
            $didResize = $this->resizeImageCoverIfPossible($sourcePath, $targetPath, $ext, 1400, 200);
        }
        if (!$didResize && !@copy($sourcePath, $targetPath)) {
            return null;
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

    private function resizeImageCoverIfPossible(string $sourcePath, string $targetPath, string $ext, int $targetW, int $targetH): bool
    {
        $ext = strtolower($ext);
        $create = null;
        $save = null;

        if (in_array($ext, ['jpg', 'jpeg'], true) && function_exists('imagecreatefromjpeg')) {
            $create = 'imagecreatefromjpeg';
            $save = function ($img, $path) {
                return imagejpeg($img, $path, 90);
            };
        } elseif ($ext === 'png' && function_exists('imagecreatefrompng')) {
            $create = 'imagecreatefrompng';
            $save = function ($img, $path) {
                imagesavealpha($img, true);
                return imagepng($img, $path, 6);
            };
        } elseif ($ext === 'webp' && function_exists('imagecreatefromwebp') && function_exists('imagewebp')) {
            $create = 'imagecreatefromwebp';
            $save = function ($img, $path) {
                return imagewebp($img, $path, 90);
            };
        }

        if (!$create || !$save) {
            return false;
        }

        $src = @$create($sourcePath);
        if (!$src) {
            return false;
        }

        $srcW = imagesx($src);
        $srcH = imagesy($src);
        if ($srcW <= 0 || $srcH <= 0) {
            imagedestroy($src);
            return false;
        }

        $dst = imagecreatetruecolor($targetW, $targetH);
        if (!$dst) {
            imagedestroy($src);
            return false;
        }

        // Preserve transparency for PNG/WEBP
        if (in_array($ext, ['png', 'webp'], true)) {
            imagealphablending($dst, false);
            imagesavealpha($dst, true);
            $transparent = imagecolorallocatealpha($dst, 0, 0, 0, 127);
            imagefilledrectangle($dst, 0, 0, $targetW, $targetH, $transparent);
        }

        // "Cover" resize: scale to fill and crop center.
        $scale = max($targetW / $srcW, $targetH / $srcH);
        $cropW = (int) round($targetW / $scale);
        $cropH = (int) round($targetH / $scale);
        $srcX = (int) max(0, floor(($srcW - $cropW) / 2));
        $srcY = (int) max(0, floor(($srcH - $cropH) / 2));

        $ok = imagecopyresampled(
            $dst,
            $src,
            0,
            0,
            $srcX,
            $srcY,
            $targetW,
            $targetH,
            $cropW,
            $cropH
        );

        if ($ok) {
            $ok = (bool) $save($dst, $targetPath);
        }

        imagedestroy($dst);
        imagedestroy($src);
        return $ok;
    }
}
