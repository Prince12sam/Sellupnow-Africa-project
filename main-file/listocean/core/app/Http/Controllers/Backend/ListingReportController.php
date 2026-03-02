<?php

namespace App\Http\Controllers\Backend;

use App\Helpers\FlashMsg;
use App\Http\Controllers\Controller;
use App\Models\Common\ListingReport;
use Illuminate\Http\Request;

class ListingReportController extends Controller
{
    public function all_report(Request $request)
    {
        $all_reports = ListingReport::with('user','reason','listing')->latest()->paginate(10);
        return view('backend.pages.listings.reports.all-reports',compact('all_reports'));
    }
    public function search_report(Request $request)
    {
        $all_reports = ListingReport::where('title', 'LIKE', "%". strip_tags($request->string_search) ."%")->paginate(10);
        return $all_reports->total() >= 1 ? view('backend.pages.listings.reports.search-result', compact('all_reports'))->render() : response()->json(['status'=>__('nothing')]);
    }

    // pagination
    function pagination(Request $request)
    {
        if($request->ajax()){
            $all_reports =  $request->string_search == '' ? ListingReport::latest()->paginate(10) : ListingReport::where('title', 'LIKE', "%". strip_tags($request->string_search) ."%")->paginate(10);
            return view('backend.pages.listings.reports.search-result', compact('all_reports'))->render();
        }
    }
    public function delete_report($id)
    {
        $report= ListingReport::find($id);
        if($report){
            $report->delete();
            return back()->with(FlashMsg::error(__('Report Successfully Deleted')));
        }else{
            return back()->with(FlashMsg::error(__('Report is not deletable because it is related to other report')));
        }
    }
    public function bulk_action_report(Request $request){
        foreach($request->ids as $report_id){
            $reason = ListingReport::find($report_id);
            if($reason){
                $reason->delete();
            }
        }
        return back()->with(FlashMsg::item_new(__('Selected Report Successfully Deleted')));
    }
}
