<?php

namespace App\Http\Controllers\Backend;

use App\Helpers\FlashMsg;
use App\Http\Controllers\Controller;
use App\Models\Backend\Notice;
use Illuminate\Http\Request;

class NoticeController extends Controller
{
    public function allNotice(){
        $notices = Notice::latest()->paginate(10);
        return view('backend.pages.notice.all-notice', compact('notices'));
    }
    public function addNoticePage(){
        return view('backend.pages.notice.add-notice');
    }

    public function addNotice(Request $request) {

        $this->validate($request, [
            'title' => 'required',
            'notice_type' => 'required',
            'notice_for' => 'required',
            'expire_date' => 'required',
        ]);

        Notice::create([
            'title' => $request->title,
            'description' => $request->description,
            'notice_type' => $request->notice_type,
            'notice_for' => $request->notice_for,
            'expire_date' => $request->expire_date,
            'status' => $request->status,
        ]);

        return redirect()->route('admin.all.notice')->with(FlashMsg::item_new('Notice Created Successfully'));
    }


    public function noticeEdit($id=null){
        $notice =   Notice::find($id);
        return view('backend.pages.notice.edit-notice', compact('notice'));
    }

    public function noticeUpdate(Request $request){

        $this->validate($request, [
            'title' => 'required',
            'notice_type' => 'required',
            'notice_for' => 'required',
            'expire_date' => 'required',
        ]);

        $notice = Notice::find($request->notice_id);;
        if ($notice) {
            $notice->title = $request->title;
            $notice->description = $request->description;
            $notice->notice_type = $request->notice_type;
            $notice->notice_for = $request->notice_for;
            $notice->expire_date = $request->expire_date;
            $notice->status = $request->status;
            $notice->save();
            return redirect()->route('admin.all.notice')->with(FlashMsg::item_new('Notice Update Success'));
        } else {
            return redirect()->back()->with('error', 'Notice not found');
        }

    }


    public function changeStatus($id){
        $notice = Notice::select('status')->where('id',$id)->first();
        if($notice->status==1){
            $status = 0;
        }else{
            $status = 1;
        }
        Notice::where('id',$id)->update(['status'=>$status]);
        return redirect()->back()->with(FlashMsg::item_new('Status Change Success'));
    }

    public function newNoticeDelete($id){
        Notice::findOrFail($id)->delete();
        return redirect()->back()->with(FlashMsg::item_delete('Notice Deleted Success'));
    }

    // search notice
    public function searchNotice(Request $request)
    {
        $notices = Notice::where('title', 'LIKE', "%". strip_tags($request->string_search) ."%")->paginate(10);
        return $notices->total() >= 1 ? view('backend.pages.notice.search-notice', compact('notices'))->render() : response()->json(['status'=>__('nothing')]);
    }

    // pagination
    function paginate(Request $request)
    {
        if($request->ajax()){
            $notices = Notice::latest()->paginate(10);
            return view('backend.pages.notice.search-notice', compact('notices'))->render();
        }
    }
}
