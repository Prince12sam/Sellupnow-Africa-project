<?php

namespace Modules\NewsLetter\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\NewsLetter\Database\factories\NewsLetterFactory;

class NewsLetter extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $table = 'news_letters';
    protected $fillable = ['email','token','verified'];

    protected static function newFactory(): NewsLetterFactory
    {
    }
}
