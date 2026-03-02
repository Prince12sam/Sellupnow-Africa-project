<?php

namespace App\Actions\Blog;

use App\Models\Backend\MetaData;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Modules\Blog\app\Models\Blog;

class BlogAction
{

    public function store_execute(Request $request) :void {

        $blog = new Blog();

        $blog->title = $request->title ?? '';
        $blog->blog_content = $request->blog_content ?? '';
        $blog->excerpt = $request->excerpt ?? '';

        $slug = !empty($request->slug) ? $request->slug : Str::slug($request->title);
        $slug_check = Blog::where(['slug' => $slug])->count();
        $slug = $slug_check > 0 ? $slug.'-2' : $slug;

        $blog->slug = $slug;
        $blog->category_id = $request->category_id;
        $blog->featured = $request->featured;
        $blog->visibility = $request->visibility;
        $blog->status = $request->status;
        $blog->admin_id = Auth::guard('admin')->user()->id;
        $blog->user_id = 0;
        $blog->author = Auth::guard('admin')->user()->name;
        $blog->image = $request->image ?? null;
        $blog->schedule_date = $request->schedule_date ?? null;
        $blog->views = 0;
        $blog->created_by = 'admin';

        $blog->save();

        // Attach tags to the blog
        $tagIds = $request->input('tag_id', []);
        if (!empty($tagIds)) {
            $blog->tags()->attach($tagIds);
        }

        // Insert meta_data
        $Metas = [
            'meta_title'=> purify_html($request->meta_title),
            'meta_tags'=> purify_html($request->meta_tags),
            'meta_description'=> purify_html($request->meta_description),
            'facebook_meta_tags'=> purify_html($request->facebook_meta_tags),
            'facebook_meta_description'=> purify_html($request->facebook_meta_description),
            'facebook_meta_image'=> $request->facebook_meta_image,
            'twitter_meta_tags'=> purify_html($request->twitter_meta_tags),
            'twitter_meta_description'=> purify_html($request->twitter_meta_description),
            'twitter_meta_image'=> $request->twitter_meta_image,
        ];

        $blog->meta_data()->create($Metas);
    }


    public function update_execute(Request $request ,$id) : void
    {
        $blog_update =  Blog::findOrFail($id);

        $blog_update->title = $request->title;
        $blog_update->blog_content = $request->blog_content;
        $blog_update->excerpt = $request->excerpt;

        $slug = !empty($request->slug) ? $request->slug : Str::slug($request->title);
        $slug_check = Blog::where(['slug' => $slug])->count();
        $slug = $slug_check > 1 ? $slug.'-3' : $slug;
        $blog_update->slug = $slug;
        $blog_update->category_id = $request->category_id;
        $blog_update->featured = $request->featured;
        $blog_update->visibility = $request->visibility;
        $blog_update->status = $request->status;
        $blog_update->image = $request->image;
        $blog_update->schedule_date = $request->schedule_date;
        $blog_update->views = $blog_update->views;

        // Attach tags to the blog
        $tagIds = $request->input('tag_id', []);
        $blog_update->tags()->detach();
        if (!empty($tagIds)) {
            $blog_update->tags()->attach($tagIds);
        }

        // update blog meta data
        $Metas = [
            'meta_title'=> purify_html($request->meta_title),
            'meta_tags'=> $request->meta_tags,
            'meta_description'=> purify_html($request->meta_description),
            'facebook_meta_tags'=> purify_html($request->facebook_meta_tags),
            'facebook_meta_description'=> purify_html($request->facebook_meta_description),
            'facebook_meta_image'=> $request->facebook_meta_image,
            'twitter_meta_tags'=> purify_html($request->twitter_meta_tags),
            'twitter_meta_description'=> purify_html($request->twitter_meta_description),
            'twitter_meta_image'=> $request->twitter_meta_image,
            ];

        // If there is existing meta_data, update it; otherwise, create a new one
        $metaData = $blog_update->meta_data;
        if ($metaData) {
            $metaData->update($Metas);
        } else {
            $blog_update->meta_data()->create($Metas);
        }

        DB::beginTransaction();
        try {
            $blog_update->save();
            DB::commit();

        }catch (\Throwable $th){
            DB::rollBack();
        }
    }

    public function clone_blog_execute(Request $request)
    {
        $blog_details = Blog::findOrFail($request->item_id);
        $cloned_data = Blog::create([
            'category_id' =>  $blog_details->category_id,
            'slug' => !empty($blog_details->slug) ? $blog_details->slug : Str::slug($blog_details->title),
            'blog_content' => $blog_details->blog_content,
            'title' => $blog_details->title,
            'status' => 'draft',
            'excerpt' => $blog_details->excerpt,
            'image' => $blog_details->image,
            'views' => 0,
            'user_id' => 0,
            'admin_id' => Auth::guard('admin')->user()->id,
            'author' => Auth::guard('admin')->user()->name,
            'schedule_date' => $blog_details->schedule_date,
            'featured' => $blog_details->featured,
            'created_by' => $blog_details->created_by,
        ]);


        $meta_object = optional($blog_details->meta_data);
        $Metas = [
            'meta_title'=> $meta_object->meta_title,
            'meta_tags'=> $meta_object->meta_tags,
            'meta_description'=> $meta_object->meta_description,
            'facebook_meta_tags'=> $meta_object->facebook_meta_tags,
            'facebook_meta_description'=> $meta_object->facebook_meta_description,
            'facebook_meta_image'=> $meta_object->facebook_meta_image,
            'twitter_meta_tags'=> $meta_object->twitter_meta_tags,
            'twitter_meta_description'=> $meta_object->twitter_meta_description,
            'twitter_meta_image'=> $meta_object->twitter_meta_image,
        ];

        $cloned_data->meta_data()->save(MetaData::create($Metas));
    }

    public function delete_execute(Request $request, $id, $type = 'delete')
    {
        switch ($type){
            case ('trashed_delete'):
                $blog = Blog::withTrashed()->find($id);
                $blog->forceDelete();
                $blog->meta_data()->delete();
                break;
            default:
                $blog = Blog::find($id);
                $blog->delete();
                break;
        }
    }
}
