<?php

namespace App\Http\Controllers\Backend;

use App\Helpers\FlashMsg;
use App\Helpers\LanguageHelper;
use App\Http\Controllers\Controller;
use App\Models\Backend\Page;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PagesController extends Controller
{

    public function index(Request $request){
        $all_pages = Page::latest()->paginate(10);
        return view('backend.pages.page.index')->with([
            'all_pages' => $all_pages,
            'default_lang' => $request->lang ?? LanguageHelper::default_slug(),
        ]);
    }
    public function newPage(Request $request){
        return view('backend.pages.page.new')->with([
            'default_lang' => $request->lang ?? LanguageHelper::default_slug(),
        ]);
    }
    public function storeNewPage(Request $request)
    {
        $this->validate($request,[
            'title' => 'required',
            'page_content' => 'nullable',
            'status' => 'required',
            'slug' => 'nullable',
            'visibility' => 'nullable|string',
        ]);

        $page = new Page();

        $page->title =  purify_html($request->title);
        $page->page_content =  $request->page_content;
        $slug = !empty($request->slug) ? $request->slug : Str::slug($request->title);
        $slug_check = Page::where(['slug' => $slug])->count();
        $slug = $slug_check > 0 ? $slug.'-6' : $slug;

        $page->slug = purify_html($slug);
        $page->status = $request->status;
        $page->visibility = $request->visibility;
        $page->page_builder_status = $request->page_builder_status;
        $page->layout = $request->layout;
        $page->sidebar_layout = $request->sidebar_layout;
        $page->page_class = $request->page_class;
        $page->back_to_top = $request->back_to_top;
        $page->navbar_variant = $request->navbar_variant;
        $page->footer_variant = $request->footer_variant;
        $page->breadcrumb_status = $request->breadcrumb_status;
        $page->widget_style = $request->widget_style;
        $page->left_column = $request->left_column;
        $page->right_column = $request->right_column;

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

        $page->save();
        $page->meta_data()->create($Metas);

        return back()->with(FlashMsg::item_new('Page Created Succefully'));
    }

    public function editPage(Request $request,$id){
        $page_post = Page::find($id);
        return view('backend.pages.page.edit')->with([
            'page_post' => $page_post,
            'default_lang' => $request->lang ?? LanguageHelper::default_slug(),
        ]);
    }

    public function updatePage(Request $request,$id){

        $this->validate($request,[
            'title' => 'required',
            'page_content' => 'nullable',
            'status' => 'required',
            'slug' => 'nullable',
            'visibility' => 'nullable|string',
        ]);

        $page = Page::find($id);
        $page->title =  purify_html($request->title);
        $page->page_content =  $request->page_content;

        $slug = !empty($request->slug) ? $request->slug : Str::slug($request->title);
        $slug_check = Page::where(['slug' => $slug])->count();

        $slug = $slug_check > 1 ? $slug.'-5' : $slug;
        $page->slug = $slug;

        $page->status = $request->status;
        $page->visibility = $request->visibility;
        $page->page_builder_status = $request->page_builder_status;
        $page->layout = $request->layout;
        $page->sidebar_layout = $request->sidebar_layout;
        $page->page_class = $request->page_class;
        $page->back_to_top = $request->back_to_top;
        $page->navbar_variant = $request->navbar_variant;
        $page->footer_variant = $request->footer_variant;
        $page->breadcrumb_status = $request->breadcrumb_status;
        $page->widget_style = $request->widget_style;
        $page->left_column = $request->left_column;
        $page->right_column = $request->right_column;

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

        $page->save();

        if (!is_null($page->meta_data()->first())){
            $page->meta_data()->update($Metas);
        }else{
            $page->meta_data()->create($Metas);
        }

        return back()->with(FlashMsg::item_new('Page Updated Successfully'));
    }

    public function deletePageLangAll($id){
        $page = Page::find($id);
        $page->delete();
        $page->meta_data()->delete();

        return redirect()->back()->with(FlashMsg::item_delete());
    }

    public function bulkAction(Request $request){

        $all = Page::findOrFail($request->ids);
        foreach($all as $item){
            $item->delete();
            $item->meta_data()->delete();
        }
        return response()->json(['status' => 'ok']);
    }

    public function searchPage(Request $request)
    {
        $all_pages = Page::where('title', 'LIKE', "%". strip_tags($request->string_search) ."%")->paginate(10);
        $default_lang = $request->lang ?? LanguageHelper::default_slug();
        return $all_pages->total() >= 1 ? view('backend.pages.page.search-page', compact('all_pages', 'default_lang'))->render() : response()->json(['status'=>__('nothing')]);
    }
    function paginate(Request $request)
    {
        if($request->ajax()){
            if($request->string_search == ''){
                $all_pages = Page::latest()->paginate(10);
            }else{
                $all_pages = Page::where('title', 'LIKE', "%". strip_tags($request->string_search) ."%")->paginate(10);
            }
           $default_lang = $request->lang ?? LanguageHelper::default_slug();
            return view('backend.pages.page.search-page', compact('all_pages', 'default_lang'))->render();
        }
    }
}
