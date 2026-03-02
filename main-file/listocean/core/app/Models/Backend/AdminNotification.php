<?php

namespace App\Models\Backend;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdminNotification extends Model
{
    use HasFactory;
    protected $fillable = ['identity','user_id','type','message','is_read'];

    public static function unread_notification()
    {
        return self::where('is_read','unread')->latest()->take(20)->get();
    }

    public function listing()
    {
        return $this->belongsTo(Listing::class,'identity','id');
    }

}
