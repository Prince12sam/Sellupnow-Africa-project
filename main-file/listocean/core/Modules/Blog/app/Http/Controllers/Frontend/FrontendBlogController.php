<?php

namespace Modules\Blog\app\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Backend\Category;
use App\Models\Backend\Page;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Modules\Blog\app\Models\Blog;
use Modules\Blog\app\Models\BlogComment;
use Modules\Blog\app\Models\BlogTag;
use Modules\Blog\app\Models\Tag;

class FrontendBlogController extends Controller
{
    public function blog_single($slug)
    {

        $blog_post = Blog::with('category','tags','comments')->where('slug', $slug)->first();
        $related_blog = Blog::where('status','publish')->inRandomOrder()->take(3)->get();

        if(empty($blog_post)){
            abort(404);
        }


       $blogPageId = get_static_option('blog_page');
       $blogPageSlug = Page::select('slug')->find($blogPageId);
        if ($blogPageSlug) {
            $all_blog_route = url('/' . $blogPageSlug->slug);
        } else {
            $all_blog_route = url('/');
        }

        return view('blog::frontend.blog-single')->with([
            'blog_post' => $blog_post,
            'related_blog' => $related_blog,
            'all_blog_route' => $all_blog_route,
        ]);
    }

    public function blog_comment(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'blog_id' => 'required',
            'message' => 'required',
        ], [
            'blog_id.required' => __('The blog ID is required.'),
            'message.required' => __('The message field is required.'),
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'validation_error',
                'errors' => $validator->errors(),
            ]);
        }

        $user = Auth::guard('web')->user();
        if (empty($user)) {
            return response()->json([
                'status' => 'error_auth',
                'error' => __('Please login to comment.')
            ]);
        }


        BlogComment::create([
            'blog_id' => $request->blog_id,
            'user_id' => $user->id,
            'name' => $user->fullname,
            'email' => $user->email,
            'message' => $request->message
        ]);

        return response()->json([
            'status' => 'success'
        ]);
    }

    public function category_wise_blog_page($cat_slug)
    {
        $category_name = Category::select('id','name','slug')->where(['slug' => $cat_slug])->first();
        if(empty($category_name)){
            abort(404);
        }

        $blogPageId = get_static_option('blog_page');
        $blogPageSlug = Page::select('slug')->find($blogPageId);
        if ($blogPageSlug) {
            $all_blog_route = url('/' . $blogPageSlug->slug);
        } else {
            $all_blog_route = url('/');
        }

        $all_blogs = Blog::where('category_id',$category_name->id)->orderBy('id', 'desc')->paginate(6);
        return view('blog::frontend.blog-category',compact('all_blogs','category_name', 'all_blog_route'));
    }

    public function tags_wise_blog_page($tag_id)
    {

        $blog_tag_ids = BlogTag::where('tag_id', $tag_id)->pluck('blog_id')->toArray();
        if(empty($blog_tag_ids)){
            abort(404);
        }
        $all_blogs = Blog::with('category','tags','comments')->whereIn('id', $blog_tag_ids)->orderBy('id', 'desc')->paginate(10);
        if(empty($all_blogs)){
            abort(404);
        }
        $tag = Tag::find($tag_id);

        $blogPageId = get_static_option('blog_page');
        $blogPageSlug = Page::select('slug')->find($blogPageId);
        if ($blogPageSlug) {
            $all_blog_route = url('/' . $blogPageSlug->slug);
        } else {
            $all_blog_route = url('/');
        }

        return view('blog::frontend.blog-tags',compact('all_blogs', 'tag', 'all_blog_route'));
    }

    public function loadMoreComments(Request $request, $blog_id){
        $blog_post = Blog::with('comments', 'user')->where('id', $blog_id)->firstOrFail();
        $offset = $request->input('offset', 0);
        $limit = 2;
        $comments = $blog_post->comments()->latest()->with('user')->skip($offset)->take($limit)->get();

        // Transform comments into a format suitable for rendering
        $renderedComments = [];
        foreach ($comments as $comment) {
            $renderedComments[] = [
                'user_image' => render_image_markup_by_attachment_id($comment->user->image, '', 'thumb'),
                'name' => $comment->name,
                'message' => $comment->message,
                'created_at' => $comment->created_at->diffForHumans(), // Format date
            ];
        }

        return response()->json(['comments' => $renderedComments]);
    }

}
