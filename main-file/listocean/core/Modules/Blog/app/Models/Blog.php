<?php

namespace Modules\Blog\app\Models;

use App\Models\Backend\Admin;
use App\Models\Backend\Category;
use App\Models\Backend\MetaData;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Modules\Blog\Database\factories\BlogFactory;

class Blog extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $table = 'blogs';
    protected $fillable = ['category_id',
        'user_id','title','slug','blog_content',
        'image','author','excerpt','status','views',
        'visibility','featured','schedule_date',
        'admin_id','created_by','tag_name'
    ];

    protected $dates = ['deleted_at'];
    protected static function newFactory(): BlogFactory
    {
        return \Modules\Blog\Database\factories\BlogFactory::new();
    }

    public function user(){
        return $this->belongsTo(User::class,'user_id');
    }

    public function admin(){
        return $this->belongsTo(Admin::class,'admin_id');
    }

    public function meta_data(){
        return $this->morphOne(MetaData::class,'meta_taggable');
    }

    public function author_data(){
        if ($this->attributes['created_by'] === 'user'){
            return User::find($this->attributes['user_id']);
        }
        return Admin::find($this->attributes['admin_id']);
    }

    public function category(){
        return $this->belongsTo(Category::class,'category_id','id');
    }

    public function comments(){
        return $this->hasMany(BlogComment::class,'blog_id','id');
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'blog_tags', 'blog_id', 'tag_id');
    }

}
