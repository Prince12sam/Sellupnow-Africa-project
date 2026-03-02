<?php

namespace App\Http\Controllers\Backend;

use App\Actions\Media\MediaHelper;
use App\Helpers\FlashMsg;
use App\Http\Controllers\Controller;
use App\Models\Backend\MediaUpload;
use Illuminate\Http\Request;
use Intervention\Image\Facades\Image;

class MediaUploadController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    public function uploadMediaFile(Request $request)
    {
        $this->validate($request, [
            'file' => 'nullable|mimes:jpg,jpeg,png,gif,webp|max:11264'
        ]);
        MediaHelper::insert_media_image($request);
    }

    public function allUploadMediaFile(Request $request)
    {
        return response()->json(MediaHelper::fetch_media_image($request));
    }

    public function deleteUploadMediaFile(Request $request)
    {
        MediaHelper::delete_media_image($request);
        return redirect()->back()->with(FlashMsg::error('Image Deleted'));
    }

    public function regenerateMediaImages()
    {
        $all_media_file = MediaUpload::all();
        foreach ($all_media_file as $img) {

            if (!file_exists('assets/uploads/media-uploader/' . $img->path)) {
                continue;
            }
            $image = 'assets/uploads/media-uploader/' . $img->path;
            $image_dimension = getimagesize($image);;
            $image_width = $image_dimension[0];
            $image_height = $image_dimension[1];

            $image_db = $img->path;
            $image_grid = 'grid-' . $image_db;
            $image_large = 'large-' . $image_db;
            $image_thumb = 'thumb-' . $image_db;
            $image_semi_large = 'semi-large-' . $image_db;
            $image_tiny = 'tiny-' . $image_db;

            $folder_path = 'assets/uploads/media-uploader/';
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

            if ($image_width > 150) {
                $resize_thumb_image->save($folder_path . $image_thumb);
                $resize_grid_image->save($folder_path . $image_grid);
                $resize_large_image->save($folder_path . $image_large);
                $resize_semi_large_image->save($folder_path . $image_semi_large);
                $resize_tiny_image->save($folder_path . $image_tiny);
            }
        }
        return __('regenerate done');
    }

    public function altChangeUploadMediaFile(Request $request)
    {
        $this->validate($request, [
            'imgid' => 'required',
            'alt' => 'nullable',
        ]);
        MediaUpload::where('id', $request->imgid)->update(['alt' => $request->alt]);
        return __('alt update done');
    }

    public function allUploadMediaImagesForPage()
    {
        $all_media_images = MediaUpload::where(['type' => 'admin'])->orderBy('id', 'desc')->get();
        return view('backend.pages.media.images')->with(['all_media_images' => $all_media_images]);
    }

    public function getImageForLoadmore(Request $request){
        return response()->json(MediaHelper::load_more_images($request));
    }
}
