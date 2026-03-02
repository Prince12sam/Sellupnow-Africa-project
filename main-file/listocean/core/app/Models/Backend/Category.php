<?php

namespace App\Models\Backend;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $table = 'categories';
    protected $fillable = ['name','slug','icon','image','status','mobile_icon', 'description'];

    public function subcategories(){
        return $this->hasMany(SubCategory::class,'category_id','id');
    }

    public function listings(){
        return $this->hasMany(Listing::class,'category_id','id')->where('status',1);
    }

    public function metaData(){
        return $this->morphOne(MetaData::class,'meta_taggable');
    }
}
