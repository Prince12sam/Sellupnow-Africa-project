<?php

namespace App\Models\Backend;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubCategory extends Model
{
    use HasFactory;

    protected $table = 'sub_categories';
    protected $fillable = ['name','slug','category_id','status','image', 'description'];

    public function category(){
        return $this->belongsTo('App\Models\Backend\Category');
    }

    public function childcategories(){
        return $this->hasMany('App\Models\Backend\ChildCategory');
    }
    public function listings(){
        return $this->hasMany('App\Models\Backend\Listing');
    }

    public function metaData(){
        return $this->morphOne(MetaData::class,'meta_taggable');
    }
}
