<?php

namespace App\Repositories;

use Abedin\Maker\Repositories\Repository;
use App\Models\Media;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class MediaRepository extends Repository
{
    /**
     * base method
     *
     * @method model()
     */
    public static function model()
    {
        return Media::class;
    }

    /**
     * Store a file from a request.
     *
     * @param  UploadedFile  $file  The file to store
     * @param  string  $path  The path to store the file
     * @param  string|null  $type  The type of the file
     */
    public static function storeByRequest(UploadedFile $file, string $path, ?string $type = null): Media
    {
        $storedPath = Storage::disk('public')->put('/' . trim($path, '/'), $file);

        // Mirror the file to the Listocean frontend media-uploader directory so the
        // customer-facing site always has the image available immediately after upload.
        try {
            $srcAbsolute = Storage::disk('public')->path($storedPath);
            if (file_exists($srcAbsolute)) {
                Storage::disk('listocean_media')->putFileAs(
                    trim($path, '/'),
                    new \Illuminate\Http\File($srcAbsolute),
                    basename($storedPath)
                );
            }
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('MediaRepository: failed to mirror file to listocean_media — ' . $e->getMessage());
        }

        $extension = $file->extension();

        if (! $type) {
            $type = in_array($extension, ['jpg', 'png', 'jpeg', 'gif']) ? 'image' : $extension;
        }

        $media = self::create([
            'type' => $type,
            'name' => $file->getClientOriginalName(),
            'src' => $storedPath,
            'extension' => $extension,
        ]);

        return $media;
    }

    /**
     * Update a media file based on the request.
     *
     * @param  UploadedFile  $file  The file to be uploaded
     * @param  string  $path  The path for the file
     * @param  ?string  $type  The type of the file
     * @param  Media  $media  The media object to be updated
     * @return Media The updated media object
     */

    public static function updateByRequest(UploadedFile $file, string $path, ?string $type = null, ?Media $media = null): Media
    {
        $storedPath = Storage::disk('public')->put('/' . trim($path, '/'), $file);
        $extension = $file->extension();

        if (! $type) {
            $type = in_array($extension, ['jpg', 'png', 'jpeg', 'gif']) ? 'image' : $extension;
        }

        // Mirror the new file to the Listocean frontend media-uploader directory.
        try {
            $srcAbsolute = Storage::disk('public')->path($storedPath);
            if (file_exists($srcAbsolute)) {
                Storage::disk('listocean_media')->putFileAs(
                    trim($path, '/'),
                    new \Illuminate\Http\File($srcAbsolute),
                    basename($storedPath)
                );
            }
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('MediaRepository: failed to mirror updated file to listocean_media — ' . $e->getMessage());
        }

        // If media exists, delete old file from both disks and update record.
        if ($media && Storage::disk('public')->exists($media->src)) {
            Storage::disk('public')->delete($media->src);
            // Clean up old file from frontend too.
            try {
                if (Storage::disk('listocean_media')->exists($media->src)) {
                    Storage::disk('listocean_media')->delete($media->src);
                }
            } catch (\Throwable $e) {
                // Non-fatal — frontend copy may not exist if it predates dual-write.
            }
            $media->update([
                'type' => $type,
                'name' => $file->getClientOriginalName(),
                'src' => $storedPath,
                'extension' => $extension,
            ]);
        } else {
            // Create a new media record
            $media = self::create([
                'type' => $type,
                'name' => $file->getClientOriginalName(),
                'src' => $storedPath,
                'extension' => $extension,
            ]);
        }

        return $media;
    }
}
