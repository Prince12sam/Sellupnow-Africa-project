<?php

namespace Modules\SupportTicket\app\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Mail\BasicMail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Modules\SupportTicket\app\Models\ChatMessage;
use Modules\SupportTicket\app\Models\Department;
use Modules\SupportTicket\app\Models\Ticket;

class UserSupportTicketController extends Controller
{
    //all tickets
    public function ticket(Request $request)
    {
        $user_id = Auth::guard('web')->user()->id;
        if($request->isMethod('post')){
            $request->validate([
                'title'=> 'required|max:191',
                'department'=> 'required|max:191',
                'priority'=> 'required|max:191',
                'description'=> 'required',
            ]);

            // create ticket for specific user
            $ticket = Ticket::create([
                'department_id'=>$request->department,
                'user_id'=> $user_id,
                'title'=>$request->title,
                'priority'=>$request->priority,
                'description'=>$request->description,
            ]);

            // send notification to admin
            notificationToAdmin($ticket->id, $user_id,'Ticket',__('New Support Ticket'));
            //Email to admin
            try {
                $subject = get_static_option('support_ticket_subject') ?? __('Support Ticket');
                $message = get_static_option('support_ticket_message') ?? __('Support Ticket Message');
                $message = str_replace(["@name","@ticket_id"],[__('Admin'),$ticket->id], $message);
                Mail::to(get_static_option('site_global_email'))->send(new BasicMail([
                    'subject' => $subject,
                    'message' => $message
                ]));
            } catch (\Exception $e) {}

            return back()->with(toastr_success(__('New Ticket Successfully Added')));
        }

        $departments = Department::where('status',1)->get();
        $tickets = Ticket::where('user_id', $user_id)->latest()->paginate(10);
        return view('supportticket::user.tickets',compact('tickets','departments'));
    }

    //paginate
    public function paginate(Request $request)
    {
        if($request->ajax()){
            if(empty($request->string_search)){
                $tickets = Ticket::where('user_id',auth()->user()->id)->latest()->paginate(10);
            }else{
                $tickets = Ticket::where('user_id',auth()->user()->id)->where(function($q) use ($request){
                    $q->orWhere('id', $request->string_search)
                        ->orWhere('priority',$request->string_search)
                        ->orWhere('status',$request->string_search);
                })->latest()->paginate(10);
            }
            return $tickets->total() >= 1
                ? view('supportticket::user.search-result', compact('tickets'))->render()
                : response()->json(['status'=>__('nothing')]);
        }
    }

    //search
    public function search_ticket(Request $request)
    {
        $tickets = Ticket::where('user_id',auth()->user()->id)->where(function($q) use ($request){
            $q->orWhere('id','LIKE', "%".strip_tags($request->string_search)."%")
                ->orWhere('priority','LIKE',"%".strip_tags($request->string_search)."%")
                ->orWhere('status','LIKE',"%".strip_tags($request->string_search)."%");
        })->latest()->paginate(10);

        return $tickets->total() >= 1
            ? view('supportticket::user.search-result', compact('tickets'))->render()
            : response()->json(['status'=>__('nothing')]);
    }

    //ticket details and chat
    public function ticket_details(Request $request, $id){
        $ticket_details = Ticket::with('user','message')->where('id',$id)->where('user_id',auth()->user()->id)->first();
        if($request->isMethod('post')){

            // user to admin ticket chat
            if(empty($request->attachment) && empty($request->message)){
                $request->validate([
                    'message'=> 'required|max:10000',
                ]);
            }

            if(!empty($request->attachment) || empty($request->message)){
                $request->validate([
                    'attachment'=> 'nullable|mimes:jpg,jpeg,png,webp,gif,pdf,svg,xlsx,xls,txt',
                ]);
            }

            if($attachment = $request->file('attachment')){
                $imageName = time().'-'.uniqid().'.'.$attachment->getClientOriginalExtension();
                $attachment->move('assets/uploads/ticket/chat-messages',$imageName);
            }
            ChatMessage::create([
                'ticket_id'=>$id,
                'message'=>$request->message,
                'attachment'=>$imageName ?? '',
                'notify'=>$request->email_notify,
                'type'=>'user',
            ]);

            // send notification to user
            notificationToAdmin($id,$ticket_details?->user?->id,'Ticket',__('Ticket New Message'));

            if($request->email_notify == 'on'){
                //Email to user
                try {
                    $message = get_static_option('support_ticket_message_email_message') ?? __('Support Ticket Message Email Notify');
                    $message = str_replace(["@name","@ticket_id"],[__('Admin') ,$id], $message);
                    Mail::to(get_static_option('site_global_email'))->send(new BasicMail([
                        'subject' => get_static_option('support_ticket_message_email_subject') ?? __('Support Ticket Message Email'),
                        'message' => $message
                    ]));
                } catch (\Exception $e) {}
            }

            return back();
        }
        return !empty($ticket_details) ? view('supportticket::user.details',compact('ticket_details')) : back();
    }
}
