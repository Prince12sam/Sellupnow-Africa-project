<?php

namespace App\Http\Controllers\Backend;

use App\Helpers\FlashMsg;
use App\Http\Controllers\Controller;
use App\Models\Backend\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends Controller
{

    public function index(Request $request){
        $categories = Category::latest()->paginate(10);
        if(!empty($request->input('search_title'))){
            $search = $request->input('search_title');
            $categories = Category::where('name', 'LIKE', '%' . $search . '%')->latest()->paginate(10);
        }
        return view('backend.pages.category.index',compact('categories'));
    }

    public function addNewCategory(Request $request)
    {
        if($request->isMethod('post')){
            $request->validate(
                [
                    'name'=> 'required|unique:categories|max:191',
                    'slug'=> 'unique:categories|max:191',
                ],
                [
                    'name.unique' => __('Category Already Exists.'),
                    'slug.unique' => __('Slug Already Exists.'),
                ]
            );
            $request->slug=='' ? $slug = Str::slug($request->name) : $slug = $request->slug;
            $category = Category::create([
                'name' => $request->name,
                'description' => $request->description,
                'slug' => $slug,
                'icon' => $request->icon,
                'image' => $request->image,
                'mobile_icon' => $request->mobile_icon,

            ]);

            // category meta data add
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
            $category->metaData()->create($Metas);
            return redirect()->back()->with(FlashMsg::item_new('New Category Added'));
        }

        return view('backend.pages.category.add_category');
    }

    public function editCategory(Request $request, $id=null)
    {
        if($request->isMethod('post')){
            $request->validate(
                [
                    'name' => 'required|max:191|unique:categories,name,'.$id,
                    'slug'=> 'max:191|unique:categories,slug,'.$id,
                ],
                [
                    'name.unique' => __('Category Already Exists.'),
                    'slug.unique' => __('Slug Already Exists.'),
                ]
            );

            $old_slug = Category::select('slug')->where('id',$id)->first();
            $old_image = Category::select('image')->where('id',$id)->first();

            Category::where('id',$id)->update([
                'name'=>$request->name,
                'description' => $request->description,
                'slug'=>$request->slug ?? $old_slug->slug,
                'icon'=>$request->icon,
                'mobile_icon'=>$request->mobile_icon,
                'image'=>$request->image,
            ]);

            $category_meta_update =  Category::findOrFail($id);
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

            if(is_null($category_meta_update->metaData()->first())){
                $category_meta_update->metaData()->create($Metas);
            }else{
                $category_meta_update->metaData()->update($Metas);
            }


            return redirect()->back()->with(FlashMsg::item_new('Category Update Success'));
        }
        $category = Category::find($id);
        return view('backend.pages.category.edit_category',compact('category'));
    }

    public function changeStatus($id){
        $category = Category::select('status')->where('id',$id)->first();
        if($category->status==1){
            $status = 0;
        }else{
            $status = 1;
        }
        Category::where('id',$id)->update(['status'=>$status]);
        return redirect()->back()->with(FlashMsg::item_new('Status Change Success'));
    }

    public function deleteCategory($id){
        Category::find($id)->delete();
        return redirect()->back()->with(FlashMsg::item_new('Category Deleted Success'));
    }

    public function bulkAction(Request $request){
        Category::whereIn('id',$request->ids)->delete();
        return response()->json(['status' => 'ok']);
    }

    public function searchCategory(Request $request)
    {
        $categories = Category::where('name', 'LIKE', "%". strip_tags($request->string_search) ."%")->paginate(10);
        return $categories->total() >= 1 ? view('backend.pages.category.search-category', compact('categories'))->render() : response()->json(['status'=>__('nothing')]);
    }
    function paginate(Request $request)
    {
        if($request->ajax()){
            if($request->string_search == ''){
                $categories = Category::latest()->paginate(10);
            }else{
                $categories = Category::where('name', 'LIKE', "%". strip_tags($request->string_search) ."%")->paginate(10);
            }
            return view('backend.pages.category.search-category', compact('categories'))->render();
        }
    }

}
