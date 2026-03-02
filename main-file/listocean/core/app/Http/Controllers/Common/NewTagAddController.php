<?php

namespace App\Http\Controllers\Common;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Blog\app\Models\Tag;

class NewTagAddController extends Controller
{
    // add new tag
    public function addNewTag(Request $request){

        $this->validate($request, [
            'tag_name' => 'required|unique:tags,name'
        ]);

        $tagName = $request->input('tag_name');

        $existingTag = Tag::where('name', $tagName)->first();
        if (!$existingTag) {
           $new_tag = Tag::create([
                'name' => $tagName,
                'status' => 'publish',
            ]);
            $tags = Tag::select('id', 'name', 'status')->where('status', 'publish')->get();
            return response()->json([
                'tags' => $tags,
                'new_tag' => $new_tag,
                'status' => 'success',
            ]);
        } else {
            return response()->json(['status' => 'exists']);
        }
    }

}
