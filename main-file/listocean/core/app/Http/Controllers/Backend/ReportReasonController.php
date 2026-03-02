<?php

namespace App\Http\Controllers\Backend;

use App\Helpers\FlashMsg;
use App\Http\Controllers\Controller;
use App\Models\Backend\ReportReason;
use Illuminate\Http\Request;

class ReportReasonController extends Controller
{
    public function all_reason(Request $request)
    {
        if($request->isMethod('post')){
            $request->validate([
                'title'=> 'required|max:191'
            ]);

            ReportReason::create([
                'title' => $request->title
            ]);
            FlashMsg::item_new(__('New Reason Successfully Added'));
            return back();
        }

        $all_reasons = ReportReason::latest()->paginate(10);
        return view('backend.pages.listings.reasons.all-reasons',compact('all_reasons'));
    }

    public function edit_reason(Request $request)
    {
        $request->validate([
            'title'=> 'required|max:191'
        ]);

        ReportReason::where('id',$request->reason_id)->update([
            'title' => $request->title
        ]);
        FlashMsg::item_new(__('Reason Successfully Updated'));
        return back();
    }

    // search membership
    public function search_reason(Request $request)
    {
        $all_reasons = ReportReason::where('title', 'LIKE', "%". strip_tags($request->string_search) ."%")->paginate(10);
        return $all_reasons->total() >= 1 ? view('backend.pages.listings.reasons.search-result', compact('all_reasons'))->render() : response()->json(['status'=>__('nothing')]);
    }

    // pagination
    function pagination(Request $request)
    {
        if($request->ajax()){
            $all_reasons =  $request->string_search == '' ? ReportReason::latest()->paginate(10) : ReportReason::where('title', 'LIKE', "%". strip_tags($request->string_search) ."%")->paginate(10);
            return view('backend.pages.listings.reasons.search-result', compact('all_reasons'))->render();
        }
    }

    public function delete_reason($id)
    {
        $reason = ReportReason::find($id);
        if(!empty($reason)){
            $reason->delete();
            return back()->with(FlashMsg::error(__('Reason Successfully Deleted')));
        }else{
            return back()->with(FlashMsg::error(__('Reason is not deletable because it is related to other reason')));
        }
    }

    // bulk action membership type
    public function bulk_action_reason(Request $request){
        foreach($request->ids as $reason_id){
            $reason = ReportReason::find($reason_id);
            if($reason){
                $reason->delete();
            }
        }
        return back()->with(FlashMsg::item_new(__('Selected Reason Successfully Deleted')));
    }
}
