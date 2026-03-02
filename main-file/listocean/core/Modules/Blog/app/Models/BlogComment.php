<?php

namespace Modules\Blog\app\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BlogComment extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $table = 'blog_comments';
    protected $fillable = ['blog_id','user_id','name','email','message'];

    public function user(){
        return $this->belongsTo(User::class,'user_id','id');
    }
}
