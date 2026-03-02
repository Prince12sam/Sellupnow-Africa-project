<?php

namespace Modules\Blog\app\Http\Controllers\Backend;

use App\Actions\Blog\BlogAction;
use App\Helpers\FlashMsg;
use App\Helpers\LanguageHelper;
use App\Http\Controllers\Controller;
use App\Mail\BasicMail;
use App\Models\Backend\Category;
use App\Models\Backend\Language;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Modules\Blog\app\Http\Requests\BlogInsertRequest;
use Modules\Blog\app\Http\Requests\BlogUpdateRequest;
use Modules\Blog\app\Models\Blog;
use Modules\Blog\app\Models\Tag;

class BlogController extends Controller
{

    public function index(Request $request){
        $blogs = Blog::latest()->paginate(10);
        return view('blog::backend.all-blogs',compact('blogs'));
    }

    public function newBlog(Request $request){
        $all_category = Category::all();
        $all_tags = Tag::all();
        return view('blog::backend.add-blog')->with([
            'all_category' => $all_category,
            'all_tags' => $all_tags,
            'default_lang' => $request->lang ?? LanguageHelper::default_slug(),
        ]);
    }

    public function searchBlog(Request $request)    {
        $blogs = Blog::where('title', 'LIKE', "%". strip_tags($request->string_search) ."%")->paginate(10);
        return $blogs->total() >= 1 ? view('blog::backend.search-blog', compact('blogs'))->render() : response()->json(['status'=>__('nothing')]);
    }
    function paginate(Request $request)
    {
        if($request->ajax()){
            $blogs = Blog::latest()->paginate(10);
            return view('blog::backend.search-blog', compact('blogs'))->render();
        }
    }

    public function storeNewBlog(BlogInsertRequest $request, BlogAction $blogAction) : RedirectResponse
    {
        $blogAction->store_execute($request);
        return back()->with(FlashMsg::item_new(__('Blog Created Successfully..')));
    }

    public function editBlog(Request $request,$id){
        $blog_post = Blog::with('tags')->find($id);
        $all_category = Category::select(['id','name'])->get();
        $all_tags = Tag::select(['id','name'])->get();
        return view('blog::backend.edit-blog')->with([
            'all_category' => $all_category,
            'all_tags' => $all_tags,
            'blog_post' => $blog_post,
        ]);
    }

    public function updateBlog(BlogUpdateRequest $request, BlogAction $blogAction,$id) : RedirectResponse
    {
        $blogAction->update_execute($request,$id);
        return back()->with(FlashMsg::item_update('Blog Updated Successfully..'));
    }

    public function deleteBlogAllLang(Request $request,BlogAction $action, $id)
    {
        $action->delete_execute($request,$id,'delete');
        return redirect()->back()->with(FlashMsg::item_delete('Blog Post Deleted Successfully..'));
    }

    public function bulkActionBlog(Request $request)
    {
        Blog::whereIn('id',$request->ids)->delete();
        return response()->json(['status' => 'ok']);
    }

    public function cloneBlog(Request $request, BlogAction $blogAction)
    {
        $blogAction->clone_blog_execute($request);
        return back()->with(FlashMsg::item_clone('Blog Cloned..'));
    }

    public function getTagsByAjax(Request $request)
    {
        $query = $request->get('query');
        $filterResult = Tag::Where('name', 'LIKE', '%' . $query . '%')->get();
        $html_markup = '';
        $result = [];
        foreach ($filterResult as $data) {
            array_push($result, $data->name);
        }
        return response()->json(['result' => $result]);
    }

    public function blogApprove(Request $request)
    {
        $this->validate($request, [
            'id' => 'required'
        ]);
        $msg = __('Approve Success');
        $blog = Blog::find($request->id);
        $blog->status = 'publish';
        $blog->save();

        if ($blog->user->email){
            try{
                Mail::to($blog->user->email)->send(new BasicMail([
                    'subject' => __('your blog is approve'),
                    'message' => __('congrats').'<br>'.__('your blog is now live'),
                    'message' => '<a href="'.route('frontend.blog.single',$blog->slug).'">'.__('Click Here').'</a>',
                ]));
            }catch(\Exception $e){
                return back()->with(['msg' => $msg, 'type' => 'success']);
                return redirect()->back()->with(['msg' => $msg.' '.__(',notification mail send failed'), 'type' => 'success']);
            }

            $msg .= ' '.__(',notification mail send');
        }


        return back()->with(['msg' => $msg, 'type' => 'success']);
    }


    //=============================== FORCE DELETE AND RESTORE FUNCTIONS =================================

    public function trashedBlogs(Request $request){
        $trashed_blogs = Blog::onlyTrashed()->get();
        $default_lang = $request->lang ?? LanguageHelper::default_slug();
        return view('backend.pages.blog.trashed',compact('trashed_blogs','default_lang'));
    }

    public function restoreTrashedBlog($id){
        Blog::withTrashed()->find($id)->restore();
        return back()->with(FlashMsg::settings_update('Trashed Blog Restored Successfully..'));
    }

    public function deleteTrashedBlog(Request $request, BlogAction $act, $id){

        $act->delete_execute($request,$id,'trashed_delete');
        return back()->with(FlashMsg::item_delete('Blog Post Deleted Forever'));
    }

    public function trashedBulkActionBlog(Request $request){
        Blog::withTrashed()->whereIn('id',$request->ids)->forceDelete();
        return response()->json(['status' => 'ok']);
    }


    public function blogSinglePageSettings()
    {
        return view('backend.pages.blog.blog-single');
    }

    public function updateBlogSinglePageSettings(Request $request)
    {
        $all_language = Language::all();
        foreach ($all_language as $lang) {
            $this->validate($request, [
                'blog_single_page_'.$lang->slug.'_related_post_title' => 'nullable|string',
                'blog_single_page_previous_post_'.$lang->slug.'_title' => 'nullable|string',
                'blog_single_page_next_post_'.$lang->slug.'_title' => 'nullable|string',
                'blog_single_page_previous_post_'.$lang->slug.'_url' => 'nullable|string',
                'blog_single_page_next_post_'.$lang->slug.'_url' => 'nullable|string',
                'blog_single_page_comments_'.$lang->slug.'_text' => 'nullable|string',
                'blog_single_page_comments_'.$lang->slug.'_title_text' => 'nullable|string',
                'blog_single_page_comments_button_'.$lang->slug.'_text' => 'nullable|string',
                'single_blog_page_comment_avatar_image' => 'nullable|string',
                'blog_single_page_login_title_'.$lang->slug.'_text' => 'nullable|string',
                'blog_single_page_login_button_'.$lang->slug.'_text' => 'nullable|string',

            ]);
            $fields = [
                'blog_single_page_'.$lang->slug.'_related_post_title',
                'blog_single_page_previous_post_'.$lang->slug.'_title',
                'blog_single_page_next_post_'.$lang->slug.'_title',
                'blog_single_page_previous_post_'.$lang->slug.'_url',
                'blog_single_page_next_post_'.$lang->slug.'_url',
                'blog_single_page_comments_'.$lang->slug.'_text',
                'blog_single_page_comments_'.$lang->slug.'_title_text',
                'blog_single_page_comments_button_'.$lang->slug.'_text',
                'blog_single_page_login_title_'.$lang->slug.'_text',
                'blog_single_page_login_button_'.$lang->slug.'_text',
                'single_blog_page_comment_avatar_image'

            ];
            foreach ($fields as $field) {
                if ($request->has($field)) {
                    update_static_option($field, $request->$field);
                }
            }

        }
        return redirect()->back()->with(FlashMsg::settings_update());
    }


    public function blogOthersPageSettings()
    {
        return view('backend.pages.blog.blog-others-settings');
    }

    public function updateBlogOthersPageSettings(Request $request)
    {


        $this->validate($request, [
            'blog_tags_video_icon_color' => 'nullable|string',
            'blog_search_video_icon_color' => 'nullable|string',
            'blog_category_video_icon_color' => 'nullable|string',
            'user_created_blog_video_icon_color' => 'nullable|string',
            'single_page_blog_video_icon_color' => 'nullable|string',

        ]);
        $fields = [
            'blog_category_video_icon_color',
            'blog_search_video_icon_color',
            'blog_tags_video_icon_color',
            'user_created_blog_video_icon_color',
            'single_page_blog_video_icon_color',

        ];
        foreach ($fields as $field) {
            if ($request->has($field)) {
                update_static_option($field, $request->$field);
            }
        }

        return redirect()->back()->with(FlashMsg::settings_update());
    }


    public function blogDetailsSettings()
    {
        return view('blog::backend.blog-details-settings');
    }

    public function blogDetailsSettingsUpdate(Request $request)
    {
        $this->validate($request, [
            'blog_share_title' => 'nullable|string',
            'blog_tag_title' => 'nullable|string',
            'related_blog_title' => 'nullable|string',
            'blog_comment_load_more_title' => 'nullable|string',
            'blog_comment_message_title' => 'nullable|string',
            'blog_comment_button_title' => 'nullable|string',
        ]);

        $all_fields = [
            'blog_share_title',
            'blog_tag_title',
            'related_blog_title',
            'blog_comment_load_more_title',
            'blog_comment_message_title',
            'blog_comment_button_title',
        ];
        foreach ($all_fields as $field) {
            update_static_option($field, $request->$field);
        }
        return redirect()->back()->with(FlashMsg::settings_update());
    }

}
