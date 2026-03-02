<?php

namespace Modules\Blog\app\Http\Controllers\Backend;

use App\Helpers\FlashMsg;
use App\Helpers\LanguageHelper;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\Blog\app\Models\Tag;

class BlogTagsController extends Controller
{

    public function index(Request $request){
        $all_tags = Tag::select(['id','name','status'])->paginate(10);
        return view('blog::backend.tags.all-tags')->with([
            'all_tags' => $all_tags,
            'default_lang' => $request->lang ?? LanguageHelper::default_slug(),
        ]);
    }

    public function newTags(Request $request){
        $this->validate($request,[
            'name' => 'required|string|max:191|unique:tags',
            'status' => 'required|string|max:191',
        ]);

        $tags = new Tag();
        $tags->name = $request->name;
        $tags->status = $request->status;
        $tags->save();
        return redirect()->back()->with(FlashMsg::item_new('Blog Tags Added'));
    }

    public function updateTags(Request $request){
        $this->validate($request,[
            'name' => 'required|string|max:191|unique:tags,name,'.$request->id,
            'status' => 'required|string|max:191',
        ]);

        $tags =  Tag::findOrFail($request->id);
        $tags->name = $request->name;
        $tags->status = $request->status;
        $tags->save();

        return back()->with(FlashMsg::item_update());
    }

    public function deleteTagsAllLang(Request $request,$id){

        if (Blog::where('tag_name',$id)->first()){
            return redirect()->back()->with([
                'msg' => __('You can not delete this tag, It already associated with a post...'),
                'type' => 'danger'
            ]);
        }
        $tags =  Tag::where('id',$id)->first();
        $tags->delete();
        return back()->with(FlashMsg::item_delete());
    }

    public function bulkAction(Request $request){
        Tag::whereIn('id',$request->ids)->delete();
        return response()->json(['status' => 'ok']);
    }

    public function searchTags(Request $request)
    {
        $all_tags = Tag::where('name', 'LIKE', "%". strip_tags($request->string_search) ."%")->paginate(10);
        return $all_tags->total() >= 1 ? view('blog::backend.tags.search-tags', compact('all_tags'))->render() : response()->json(['status'=>__('nothing')]);
    }
    function paginateTag(Request $request)
    {
        if($request->ajax()){
            if($request->string_search == ''){
                $all_tags = Tag::latest()->paginate(10);
            }else{
                $all_tags = Tag::where('name', 'LIKE', "%". strip_tags($request->string_search) ."%")->paginate(10);
            }
            return view('blog::backend.tags.search-tags', compact('all_tags'))->render();
        }
    }
}
