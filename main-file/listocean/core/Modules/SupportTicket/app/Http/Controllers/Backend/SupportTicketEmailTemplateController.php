<?php

namespace Modules\SupportTicket\app\Http\Controllers\Backend;

use App\Helpers\FlashMsg;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\Membership\app\Models\Membership;

class SupportTicketEmailTemplateController extends Controller
{
    const BASE_PATH_SupportTicket = 'supportticket::backend.email-template.';

    public function supportTicketToAdminTemplate(Request $request)
    {
        if($request->isMethod('post')){
            $request->validate([
                'support_ticket_message_email_subject'=>'required|min:5|max:1000',
                'support_ticket_message_email_message'=>'required|min:10|max:1000',
            ]);
            $fields = [
                'support_ticket_message_email_subject',
                'support_ticket_message_email_message'
            ];
            foreach ($fields as $field) {
                update_static_option($field, $request->$field);
            }
            return redirect()->back()->with(FlashMsg::item_new(__('Update Success')));
        }

        return view(self::BASE_PATH_SupportTicket.'user-support-ticket-to-admin-template');
    }

    public function supportTicketToUserTemplate(Request $request)
    {
        if($request->isMethod('post')){
            $request->validate([
                'support_ticket_subject'=>'required|min:5|max:1000',
                'support_ticket_message'=>'required|min:10|max:1000',
            ]);
            $fields = [
                'support_ticket_message',
                'support_ticket_subject'
            ];
            foreach ($fields as $field) {
                update_static_option($field, $request->$field);
            }
            return redirect()->back()->with(FlashMsg::item_new(__('Update Success')));
        }

        return view(self::BASE_PATH_SupportTicket.'user-support-ticket-to-user-template');
    }
}
