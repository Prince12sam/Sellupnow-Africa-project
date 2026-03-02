<?php

namespace Modules\SupportTicket\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\SupportTicket\Database\factories\ChatMessageFactory;

class ChatMessage extends Model
{
    use HasFactory;


    protected $fillable = ['ticket_id','message','attachment','notify','type'];
    protected static function newFactory(): ChatMessageFactory
    {
    }
    public function ticket()
    {
        return $this->belongsTo(Ticket::class,'ticket_id','id');
    }
}
