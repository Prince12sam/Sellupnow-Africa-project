<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class ListoceanMediaSync extends Command
{
    /**
     * The name and signature of the console command.
     *
     * --dry-run: do not write anything.
     * --fast: skip image dimension detection (much faster on large libraries).
     * --limit: only process N files (for testing).
     */
    protected $signature = 'listocean:media-sync
        {--dry-run : Show what would change, but do not write to DB}
        {--fast : Skip getimagesize() dimension detection}
        {--limit=0 : Only process the first N files (0 = no limit)}';

    protected $description = 'Sync Listocean media-uploader files into the listocean.media_uploads table';

    public function handle(): int
    {
        $mediaDir = base_path('../main-file/listocean/assets/uploads/media-uploader');
        if (! File::exists($mediaDir)) {
            $this->error("Listocean media directory not found: {$mediaDir}");
            return self::FAILURE;
        }

        $db = DB::connection('listocean');

        $existingPaths = $db->table('media_uploads')
            ->whereNotNull('path')
            ->pluck('path')
            ->filter()
            ->map(fn ($p) => (string) $p)
            ->all();

        $existingSet = [];
        foreach ($existingPaths as $p) {
            $existingSet[$p] = true;
        }

        $files = File::allFiles($mediaDir);
        $limit = (int) $this->option('limit');
        if ($limit > 0) {
            $files = array_slice($files, 0, $limit);
        }

        $this->info('Listocean media-uploader files: ' . count($files));
        $this->info('Existing media_uploads rows: ' . count($existingSet));

        $dryRun = (bool) $this->option('dry-run');
        $fast = (bool) $this->option('fast');

        $toInsert = [];
        $inserted = 0;
        $skippedExisting = 0;

        $bar = $this->output->createProgressBar(count($files));
        $bar->start();

        foreach ($files as $file) {
            $bar->advance();

            $fileName = $file->getFilename();
            if ($fileName === '' || isset($existingSet[$fileName])) {
                $skippedExisting++;
                continue;
            }

            $fullPath = $file->getRealPath();
            if (! $fullPath || ! File::exists($fullPath)) {
                continue;
            }

            $bytes = (int) File::size($fullPath);
            $size = $this->formatSize($bytes);

            $dimensions = null;
            if (! $fast) {
                try {
                    $info = @getimagesize($fullPath);
                    if (is_array($info) && isset($info[0], $info[1])) {
                        $dimensions = $info[0] . ' x ' . $info[1] . ' pixels';
                    }
                } catch (\Throwable $e) {
                    $dimensions = null;
                }
            }

            $now = now();
            $toInsert[] = [
                'title' => $fileName,
                'path' => $fileName,
                'alt' => null,
                'size' => $size,
                'dimensions' => $dimensions,
                'user_id' => null,
                'type' => 'admin',
                'created_at' => $now,
                'updated_at' => $now,
            ];

            $existingSet[$fileName] = true;

            if (count($toInsert) >= 500) {
                $inserted += $this->flush($db, $toInsert, $dryRun);
                $toInsert = [];
            }
        }

        $bar->finish();
        $this->newLine();

        if (count($toInsert)) {
            $inserted += $this->flush($db, $toInsert, $dryRun);
        }

        $this->info('Skipped existing: ' . $skippedExisting);
        $this->info(($dryRun ? 'Would insert: ' : 'Inserted: ') . $inserted);

        return self::SUCCESS;
    }

    private function flush($db, array $rows, bool $dryRun): int
    {
        if (! count($rows)) {
            return 0;
        }

        if ($dryRun) {
            return count($rows);
        }

        $db->table('media_uploads')->insert($rows);
        return count($rows);
    }

    private function formatSize(int $bytes): string
    {
        if ($bytes < 1024 * 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        }

        return number_format($bytes / (1024 * 1024), 2) . ' MB';
    }
}
