<?php

namespace App\Http\Controllers\Admin;

use App\Events\SupportTicketMessageEvent;
use App\Http\Controllers\Controller;
use App\Http\Requests\TimeScheduleRequest;
use App\Http\Resources\SupportTicketMessageResource;
use App\Models\SupportTicket;
use App\Models\SupportTicketMessage;
use App\Repositories\SupportTicketRepository;
use Illuminate\Http\Request;

class SupportTicketController extends Controller
{
    private const ALLOWED_STATUS = ['pending', 'confirm', 'completed', 'cancel'];

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $request = request();
        $status = $request->query('status');

        if ($status && ! in_array($status, self::ALLOWED_STATUS, true)) {
            $status = null;
        }

        $supportTickets = SupportTicketRepository::query()->when($status, function ($query) use ($status) {
            return $query->where('status', $status);
        })->latest('id')->paginate(10);

        return view('admin.supportTicket.index', compact('supportTickets'));
    }

    /**
     * Show the specified resource.
     */
    public function show(SupportTicket $supportTicket)
    {
        $highlightedMessages = $supportTicket->messages()->where(function ($query) {
            $query->where('is_highlighted', true);
        })->get();

        return view('admin.supportTicket.show', compact('supportTicket', 'highlightedMessages'));
    }

    /**
     * setup ticket time scheduled.
     */
    public function setScheduled(SupportTicket $supportTicket, TimeScheduleRequest $request)
    {
        SupportTicketRepository::updateTicketTimeSchedule($supportTicket, $request);

        try {
            SupportTicketMessageEvent::dispatch($supportTicket->ticket_number);
        } catch (\Throwable $th) {
        }

        return back()->withSuccess('Ticket time schedule updated successfully');
    }

    /**
     * Send message to the specified support ticket.
     */
    public function sendMessage(Request $request, SupportTicket $supportTicket)
    {
        $data = $request->validate([
            'message' => 'required|string|min:1|max:5000',
        ]);

        $request->merge([
            'message' => trim($data['message']),
        ]);

        SupportTicketRepository::storeMessage($supportTicket, $request);

        try {
            SupportTicketMessageEvent::dispatch($supportTicket->ticket_number);
        } catch (\Throwable $th) {
        }

        return $this->json('message sent successfully', [
            'messages' => SupportTicketMessageResource::collection($supportTicket->messages),
        ]);
    }

    /**
     * fetch messages
     */
    public function fetchMessages(SupportTicket $supportTicket)
    {
        return $this->json('all messages', [
            'messages' => SupportTicketMessageResource::collection($supportTicket->messages),
        ]);
    }

    /**
     * update status.
     */
    public function updateStatus(Request $request, SupportTicket $supportTicket)
    {
        $request->validate([
            'status' => 'required|in:pending,confirm,completed,cancel',
        ]);

        SupportTicketRepository::updateTicketStatus($supportTicket, $request);

        try {
            SupportTicketMessageEvent::dispatch($supportTicket->ticket_number);
        } catch (\Throwable $th) {
        }

        return back()->withSuccess('Ticket status updated successfully');
    }

    /**
     * toggle user chat
     */
    public function chatToggle(SupportTicket $supportTicket)
    {
        $supportTicket->update([
            'user_chat' => ! $supportTicket->user_chat,
        ]);

        try {
            SupportTicketMessageEvent::dispatch($supportTicket->ticket_number);
        } catch (\Throwable $th) {
        }

        return back()->withSuccess('Customer Message toggle updated');
    }

    /**
     * pin message
     */
    public function pinMessage(SupportTicketMessage $supportTicketMessage)
    {
        $supportTicketMessage->update([
            'is_highlighted' => ! $supportTicketMessage->is_highlighted,
        ]);

        $supportTicketMessage->refresh();

        try {
            SupportTicketMessageEvent::dispatch($supportTicketMessage->supportTicket->ticket_number);
        } catch (\Throwable $th) {
        }

        if ($supportTicketMessage->is_highlighted) {
            $message = 'The message has been pinned successfully.';
        } else {
            $message = 'The message has been unpinned successfully';
        }

        return back()->withSuccess($message);
    }
}
