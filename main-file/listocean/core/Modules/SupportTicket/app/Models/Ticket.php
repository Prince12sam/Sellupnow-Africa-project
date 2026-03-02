<?php

namespace Modules\SupportTicket\app\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\SupportTicket\Database\factories\TicketFactory;

class Ticket extends Model
{
    use HasFactory;

    protected $fillable = [
        'department_id',
        'admin_id',
        'user_id',
        'title',
        'subject',
        'priority',
        'description',
        'status',
        'via',
        'operating_system',
    ];

    protected static function newFactory(): TicketFactory
    {

    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id','id');
    }

    public function message()
    {
        return $this->hasMany(ChatMessage::class, 'ticket_id','id');
    }

    public function get_ticket_latest_message()
    {
        return $this->hasOne(ChatMessage::class, 'ticket_id','id')->latest();
    }
}
