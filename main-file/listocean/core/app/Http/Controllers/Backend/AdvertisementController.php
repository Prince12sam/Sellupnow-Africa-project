<?php

namespace App\Http\Controllers\Backend;

use App\Helpers\FlashMsg;
use App\Http\Controllers\Controller;
use App\Models\Backend\Advertisement;
use Illuminate\Http\Request;

class AdvertisementController extends Controller
{

    public function index()
    {
        $all_advertisements = Advertisement::latest()->paginate(10);
        return view('backend.pages.advertisement.index',compact('all_advertisements'));
    }

    public function new_advertisement()
    {
        return view('backend.pages.advertisement.new');
    }

    public function store_advertisement(Request $request)
    {
        $request->validate([
            'title'=>'required|string',
            'type'=>'required|string',
            'size'=> 'required',
            'status'=> 'required',
            'slot'=> 'nullable',
            'embed_code'=> 'nullable',
            'redirect_url'=> 'nullable',
            'image'=> 'nullable'
        ]);

        Advertisement::create([
            'title' => $request->title,
            'type' => $request->type,
            'size' => $request->size,
            'status' => $request->status,
            'slot' => $request->slot,
            'embed_code' => $request->embed_code,
            'redirect_url' => purify_html($request->redirect_url),
            'image' => $request->image,
        ]);

        return redirect()->back()->with(FlashMsg::item_new(__('New Advertisement Created Successfully')));
    }

    public function edit_advertisement($id)
    {
        $advertisement = Advertisement::find($id);
        return view('backend.pages.advertisement.edit',compact('advertisement'));
    }

    public function update_advertisement(Request $request,$id)
    {
        $request->validate([
            'title'=>'required|string',
            'type'=>'required|string',
            'size'=> 'required',
            'status'=> 'required',
            'slot'=> 'nullable',
            'embed_code'=> 'nullable',
            'redirect_url'=> 'nullable',
            'image'=> 'nullable'
        ]);

        Advertisement::where('id',$id)->update([
            'title' => purify_html( $request->title),
            'type' => purify_html($request->type),
            'size' => $request->size,
            'status' => $request->status,
            'slot' => $request->slot,
            'embed_code' => $request->embed_code,
            'redirect_url' => purify_html($request->redirect_url),
            'image' => $request->image,
        ]);

        return redirect()->back()->with(FlashMsg::item_new(__('Advertisement Updated Successfully')));
    }


    public function delete_advertisement($id){
        Advertisement::find($id)->delete();
        return redirect()->back()->with(FlashMsg::item_new(__('Advertisement Deleted Successfully')));
    }

    public function bulk_action(Request $request){
        Advertisement::whereIn('id',$request->ids)->delete();
        return response()->json(['status' => 'ok']);
    }

    public function search_advertisement(Request $request)
    {
        $all_advertisements = Advertisement::where('title', 'LIKE', "%". strip_tags($request->string_search) ."%")->paginate(10);
        return $all_advertisements->total() >= 1 ? view('backend.pages.advertisement.search-advertisement', compact('all_advertisements'))->render() : response()->json(['status'=>__('nothing')]);
    }
    function advertisement_paginate(Request $request)
    {
        if($request->ajax()){
            if($request->string_search == ''){
                $all_advertisements = Advertisement::latest()->paginate(10);
            }else{
                $all_advertisements = Advertisement::where('title', 'LIKE', "%". strip_tags($request->string_search) ."%")->paginate(10);
            }
            return view('backend.pages.advertisement.search-advertisement', compact('all_advertisements'))->render();
        }
    }

    public function changeStatus($id){
        $advertisement = Advertisement::select('status')->where('id',$id)->first();
        if($advertisement->status==1){
            $status = 0;
        }else{
            $status = 1;
        }
        Advertisement::where('id',$id)->update(['status'=>$status]);
        return redirect()->back()->with(FlashMsg::item_new('Status Change Success'));
    }
}
