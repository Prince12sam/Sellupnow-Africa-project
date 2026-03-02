<?php

namespace App\Http\Controllers\Frontend\User;

use App\Actions\Media\GuestMediaHelper;
use App\Helpers\FlashMsg;
use App\Http\Controllers\Controller;
use App\Models\Backend\MediaUpload;
use Illuminate\Http\Request;

class GuestMediaUploadController extends Controller
{
    public function __construct()
    {
        $this->middleware('guest_media_upload_check');
    }

    public function uploadMediaFile(Request $request)
    {
        $this->validate($request, [
            'file' => 'nullable|mimes:jpg,jpeg,png,gif,webp|max:11000'
        ]);
        GuestMediaHelper::insert_media_image($request,'web');
    }

    public function allUploadMediaFile(Request $request)
    {
        return response()->json(GuestMediaHelper::fetch_media_image($request,'web'));
    }

    public function deleteUploadMediaFile(Request $request)
    {
        GuestMediaHelper::delete_media_image($request,'web');

        return redirect()->back()->with(FlashMsg::error('Image Deleted'));
    }

    public function altChangeUploadMediaFile(Request $request)
    {
        $this->validate($request, [
            'imgid' => 'required',
            'alt' => 'nullable',
        ]);
        MediaUpload::where(['id' => $request->imgid,'type' => 'web'])->update(['alt' => $request->alt]);
        return __('alt update done');
    }
    public function getImageForLoadmore(Request $request){
        return response()->json(GuestMediaHelper::load_more_images($request,'web'));
    }
}
