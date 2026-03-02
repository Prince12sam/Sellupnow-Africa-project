<?php

namespace App\Models\Backend;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;
    protected $table = "notifications";
    protected $fillable = [
        'notifiable_id',
        'notifiable_type',
        'title',
        'message',
        'read_at',
    ];
    public function notifiable()
    {
        return $this->morphTo();
    }
}
