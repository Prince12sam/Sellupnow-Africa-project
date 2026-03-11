<?php

namespace App\Actions\Media;

use App\Models\Backend\MediaUpload;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;

class GuestMediaHelper
{
    private static function normalizeAbsoluteLocalUrl(?string $url): string
    {
        $url = trim((string) $url);
        if ($url === '' || !preg_match('~^https?://~i', $url)) {
            return $url;
        }

        $host = strtolower((string) parse_url($url, PHP_URL_HOST));
        if (!in_array($host, ['127.0.0.1', 'localhost'], true)) {
            return $url;
        }

        $path = (string) parse_url($url, PHP_URL_PATH);
        if ($path === '') {
            return $url;
        }

        $base = request()->getSchemeAndHttpHost();
        if (empty($base)) {
            $base = rtrim((string) config('app.url'), '/');
        }

        return rtrim($base, '/') . '/' . ltrim($path, '/');
    }

    private static function resolveMediaImageUrl($path)
    {
        $candidates = [
            'assets/uploads/media-uploader/grid/grid-' . $path,
            'assets/uploads/media-uploader/grid-' . $path,
            'assets/uploads/media-uploader/semi-large/semi-large-' . $path,
            'assets/uploads/media-uploader/semi-large-' . $path,
            'assets/uploads/media-uploader/thumb/thumb-' . $path,
            'assets/uploads/media-uploader/thumb-' . $path,
            'assets/uploads/media-uploader/' . $path,
        ];

        foreach ($candidates as $candidate) {
            if (file_exists(public_path($candidate))) {
                return self::normalizeAbsoluteLocalUrl(asset($candidate));
            }
        }

        return null;
    }

    private static function publicMediaPath($relativePath = '')
    {
        $base = public_path('assets/uploads/media-uploader');

        if ($relativePath === '') {
            return $base;
        }

        return $base . DIRECTORY_SEPARATOR . str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $relativePath);
    }

    public static function fetch_media_image($request)
    {
        $image_query = MediaUpload::query();
        //guest image IDs stored in the session
        $uploadedImageIds = Session::get('uploaded_image_ids', []);
        $type = 'web';
        $image_query->where('user_id', null);
        $all_images = $image_query->where(['type' => $type])
            ->whereIn('id', $uploadedImageIds)
            ->orderBy('id', 'DESC')
            ->take(20)
            ->get();

        $selected_image = MediaUpload::find($request->selected);
        $all_image_files = [];

        if (!empty($selected_image)){
            $image_url = self::resolveMediaImageUrl($selected_image->path);
            $all_image_files[] = [
                'image_id' => $selected_image->id,
                'title' => $selected_image->title,
                'dimensions' => $selected_image->dimensions,
                'alt' => $selected_image->alt,
                'size' => $selected_image->size,
                'path' => $selected_image->path,
                'img_url' => $image_url ?: self::normalizeAbsoluteLocalUrl(asset('assets/uploads/no-image.png')),
                'upload_at' => date_format($selected_image->created_at, 'd M y')
            ];

        }
        foreach ($all_images as $image){
            $image_url = self::resolveMediaImageUrl($image->path);
            $all_image_files[] = [
                'image_id' => $image->id,
                'title' => $image->title,
                'dimensions' => $image->dimensions,
                'alt' => $image->alt,
                'size' => $image->size,
                'path' => $image->path,
                'img_url' => $image_url ?: self::normalizeAbsoluteLocalUrl(asset('assets/uploads/no-image.png')),
                'upload_at' => date_format($image->created_at, 'd M y')
            ];
        }
        return $all_image_files;
    }


    public static function delete_user_media_image($image_id,$type ='web')
    {
        $get_image_details = MediaUpload::find($image_id);
        if (file_exists('assets/uploads/media-uploader/' . $get_image_details->path) && !is_dir('assets/uploads/media-uploader/' . $get_image_details->path)) {
            unlink('assets/uploads/media-uploader/' . $get_image_details->path);
        }
        if (file_exists('assets/uploads/media-uploader/grid-' . $get_image_details->path) && !is_dir('assets/uploads/media-uploader/' . $get_image_details->path)) {
            unlink('assets/uploads/media-uploader/grid-' . $get_image_details->path);
        }
        if (file_exists('assets/uploads/media-uploader/large-' . $get_image_details->path) && !is_dir('assets/uploads/media-uploader/' . $get_image_details->path)) {
            unlink('assets/uploads/media-uploader/large-' . $get_image_details->path);
        }
        if (file_exists('assets/uploads/media-uploader/semi-large-' . $get_image_details->path) && !is_dir('assets/uploads/media-uploader/' . $get_image_details->path)) {
            unlink('assets/uploads/media-uploader/semi-large-' . $get_image_details->path);
        }
        if (file_exists('assets/uploads/media-uploader/thumb-' . $get_image_details->path) && !is_dir('assets/uploads/media-uploader/' . $get_image_details->path)) {
            unlink('assets/uploads/media-uploader/thumb-' . $get_image_details->path);
        }

        $image_query = MediaUpload::query();

        if ($type === 'web'){
            $image_query->where(['type' => $type,'user_id' => auth($type)->id()]);
        }
        $image_query->where(['id' => $image_id])->delete();
    }

    public static function delete_media_image($request,$type ='admin')
    {
        $get_image_details = MediaUpload::find($request->img_id);
        if (file_exists('assets/uploads/media-uploader/' . $get_image_details->path) && !is_dir('assets/uploads/media-uploader/' . $get_image_details->path)) {
            unlink('assets/uploads/media-uploader/' . $get_image_details->path);
        }

        if (file_exists('assets/uploads/media-uploader/grid/grid-' . $get_image_details->path) && !is_dir('assets/uploads/media-uploader/' . $get_image_details->path)) {
            unlink('assets/uploads/media-uploader/grid/grid-' . $get_image_details->path);
        }
        if (file_exists('assets/uploads/media-uploader/large/large-' . $get_image_details->path) && !is_dir('assets/uploads/media-uploader/' . $get_image_details->path)) {
            unlink('assets/uploads/media-uploader/large/large-' . $get_image_details->path);
        }
        if (file_exists('assets/uploads/media-uploader/semi-large/semi-large-' . $get_image_details->path) && !is_dir('assets/uploads/media-uploader/' . $get_image_details->path)) {
            unlink('assets/uploads/media-uploader/semi-large/semi-large-' . $get_image_details->path);
        }
        if (file_exists('assets/uploads/media-uploader/thumb/thumb-' . $get_image_details->path) && !is_dir('assets/uploads/media-uploader/' . $get_image_details->path)) {
            unlink('assets/uploads/media-uploader/thumb/thumb-' . $get_image_details->path);
        }
        if (file_exists('assets/uploads/media-uploader/tiny/tiny-' . $get_image_details->path) && !is_dir('assets/uploads/media-uploader/' . $get_image_details->path)) {
            unlink('assets/uploads/media-uploader/tiny/tiny-' . $get_image_details->path);
        }

        $image_query = MediaUpload::query();

        if ($type === 'web'){
            $image_query->where(['type' => $type,'user_id' => auth($type)->id()]);
        }
        $image_query->where(['id' => $request->img_id])->delete();
    }

    public static function insert_media_image($request,$type='admin',$file_field_name = 'file'){

        if ($request->hasFile($file_field_name)) {
            $image = $request->$file_field_name;
            $image_dimension = getimagesize($image);
            $image_width = $image_dimension[0];
            $image_height = $image_dimension[1];
            $image_dimension_for_db = $image_width . ' x ' . $image_height . ' pixels';
            $image_size_for_db = $image->getSize();

            $image_extenstion = $image->getClientOriginalExtension();
            $image_name_with_ext = $image->getClientOriginalName();

            $image_name = pathinfo($image_name_with_ext, PATHINFO_FILENAME);
            $image_name = strtolower(Str::slug($image_name));

            $image_db = $image_name . time() . '.' . $image_extenstion;
            $image_grid = 'grid-' . $image_db;
            $image_large = 'large-' . $image_db;
            $image_thumb = 'thumb-' . $image_db;
            $image_semi_large = 'semi-large-' . $image_db;
            $image_tiny = 'tiny-' . $image_db;

            $folder_path = self::publicMediaPath();
            if (!is_dir($folder_path)) {
                @mkdir($folder_path, 0755, true);
            }
            $resize_grid_image = Image::make($image)->resize(350, null, function ($constraint) {
                $constraint->aspectRatio();
            });
            $resize_large_image = Image::make($image)->resize(740, null, function ($constraint) {
                $constraint->aspectRatio();
            });


            $resize_semi_large_image = Image::make($image)->resize(540, 350, function ($constraint) {
                $constraint->aspectRatio();
            });

            $resize_tiny_image = Image::make($image)->resize(15, 15)->blur(50);
            $resize_thumb_image = Image::make($image)->resize(150, 150);

            $resize_full_image = Image::make($request->$file_field_name)->resize($image_width, $image_height,function ($constraint) {
                $constraint->aspectRatio();
            });
            $resize_full_image->save($folder_path . DIRECTORY_SEPARATOR . $image_db);

            // get session store old id get
            $uploadedImageIds = Session::get('uploaded_image_ids', []);

            $uploadedImagesInfo = MediaUpload::create([
                'title' => $image_name_with_ext,
                'size' => formatBytes($image_size_for_db),
                'path' => $image_db,
                'dimensions' => $image_dimension_for_db,
                'type' => $type,
                'user_id' => null,
            ]);


            // Store the image ID
            if ($uploadedImagesInfo) {
                // Add the newly uploaded image ID to the array
                $uploadedImageIds[] = $uploadedImagesInfo->id;
                // Store the updated array back in the session
                Session::put('uploaded_image_ids', $uploadedImageIds);
            }

            if ($image_width > 150) {
                $thumb_image_folder = $folder_path . DIRECTORY_SEPARATOR . 'thumb';
                $grid_image_folder = $folder_path . DIRECTORY_SEPARATOR . 'grid';
                $large_image_folder = $folder_path . DIRECTORY_SEPARATOR . 'large';
                $semi_large_image_folder = $folder_path . DIRECTORY_SEPARATOR . 'semi-large';
                $tiny_image_folder = $folder_path . DIRECTORY_SEPARATOR . 'tiny';

                // image folder create
                foreach ([$thumb_image_folder, $grid_image_folder, $large_image_folder, $semi_large_image_folder, $tiny_image_folder] as $item){
                     if(!is_dir($item)){
                        @mkdir($item, 0755, true);
                     }
                }

                $resize_thumb_image->save($thumb_image_folder . DIRECTORY_SEPARATOR . $image_thumb);
                $resize_grid_image->save($grid_image_folder . DIRECTORY_SEPARATOR . $image_grid);
                $resize_large_image->save($large_image_folder . DIRECTORY_SEPARATOR . $image_large);
                $resize_semi_large_image->save($semi_large_image_folder . DIRECTORY_SEPARATOR . $image_semi_large);
                $resize_tiny_image->save($tiny_image_folder . DIRECTORY_SEPARATOR . $image_tiny);
            }
        }

    }


    public static function load_more_images($request,$type = 'admin'){

        $image_query = MediaUpload::query();
        $type = 'web';
        $image_query->where(['type' => $type,'user_id' => null]);

        //guest image IDs stored in the session
        $uploadedImageIds = Session::get('uploaded_image_ids', []);

        $all_images = $image_query->whereIn('id', $uploadedImageIds)
            ->orderBy('id', 'DESC')
            ->skip($request->skip)
            ->take(20)
            ->get();

        $all_image_files = [];
        foreach ($all_images as $image){
            $image_url = self::resolveMediaImageUrl($image->path);
            $all_image_files[] = [
                'image_id' => $image->id,
                'title' => $image->title,
                'dimensions' => $image->dimensions,
                'alt' => $image->alt,
                'size' => $image->size,
                'path' => $image->path,
                'img_url' => $image_url ?: self::normalizeAbsoluteLocalUrl(asset('assets/uploads/no-image.png')),
                'upload_at' => date_format($image->created_at, 'd M y')
            ];
        }
        return $all_image_files;
    }
}
