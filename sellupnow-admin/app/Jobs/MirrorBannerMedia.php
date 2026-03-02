<?php
namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class MirrorBannerMedia implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $mediaPath;

    /**
     * @param string $mediaPath Relative path as stored in sellupnow media table (e.g. "logo/abc.png")
     */
    public function __construct(string $mediaPath)
    {
        $this->mediaPath = $mediaPath;
    }

    public function handle()
    {
        try {
            $srcCandidates = [];
            // candidate 1: public/ + mediaPath
            $srcCandidates[] = base_path('public') . DIRECTORY_SEPARATOR . $this->mediaPath;
            // candidate 2: storage/app/public/ + mediaPath
            $srcCandidates[] = base_path('storage') . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . $this->mediaPath;
            // candidate 3: storage/ + mediaPath
            $srcCandidates[] = base_path('storage') . DIRECTORY_SEPARATOR . $this->mediaPath;

            $found = null;
            foreach ($srcCandidates as $c) {
                if (is_file($c)) { $found = $c; break; }
            }

            if (! $found) {
                Log::warning('MirrorBannerMedia: source file not found for ' . $this->mediaPath);
                return;
            }

            $targetDir = base_path('..' . DIRECTORY_SEPARATOR . 'main-file' . DIRECTORY_SEPARATOR . 'listocean' . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'media-uploader');
            if (!is_dir($targetDir)) { @mkdir($targetDir, 0775, true); }

            $fileName = basename($this->mediaPath);
            $targetPath = $targetDir . DIRECTORY_SEPARATOR . $fileName;

            if (!@copy($found, $targetPath)) {
                Log::warning('MirrorBannerMedia: failed to copy ' . $found . ' -> ' . $targetPath);
                return;
            }

            Log::info('MirrorBannerMedia: copied ' . $this->mediaPath . ' to core uploads');
        } catch (\Throwable $e) {
            Log::error('MirrorBannerMedia error: ' . $e->getMessage());
        }
    }
}
