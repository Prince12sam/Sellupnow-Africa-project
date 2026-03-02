<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Backend\AdminNotification;
use Illuminate\Http\Request;

class AdminNotificationController extends Controller
{

    public function all_notification()
    {
        $all_notifications = AdminNotification::with('listing')->latest()->orderBy('is_read','ASC')->paginate(10);
        return view('backend.pages.notification.all-notification',compact('all_notifications'));
    }

    // search history
    public function search_notification(Request $request)
    {
        $all_notifications = AdminNotification::where(function ($query) use($request) {
            $query->where('type', 'LIKE', "%". strip_tags($request->string_search) ."%")
                ->orWhere('is_read', 'LIKE', strip_tags($request->string_search));
        })
            ->latest()
            ->orderBy('is_read','ASC')
            ->paginate(10);

        return $all_notifications->total() >= 1 ? view('backend.pages.notification.search-result', compact('all_notifications'))->render() : response()->json(['status'=>__('nothing')]);
    }

    // pagination
    function pagination(Request $request)
    {

        if($request->ajax()){
            if(empty($request->string_search)){
                $all_notifications = AdminNotification::with('listing')->latest()->orderBy('is_read','ASC')->paginate(10);
                return view('backend.pages.notification.search-result', compact('all_notifications'))->render();
            }else{
                $all_notifications = AdminNotification::where(function ($query) use($request) {
                    $query->where('type', 'LIKE', "%". strip_tags($request->string_search) ."%")
                        ->orWhere('is_read', 'LIKE', strip_tags($request->string_search));
                })
                    ->latest()
                    ->orderBy('is_read','ASC')
                    ->paginate(10);

                return $all_notifications->total() >= 1 ? view('backend.pages.notification.search-result', compact('all_notifications'))->render() : response()->json(['status'=>__('nothing')]);

            }
        }
    }

    // read notification
    public function read_notification()
    {
        AdminNotification::where('is_read','unread')
            ->update(['is_read' => 'read']);
        return response()->json(['status' => 'success']);
    }
}
