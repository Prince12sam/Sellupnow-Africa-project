<?php

namespace Modules\NewsLetter\app\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\NewsLetter\app\Models\NewsLetter;

class NewsLetterController extends Controller
{
    public function subscribe(Request $request)
    {
        $request->validate(['email' => 'required|email|unique:news_letters']);

        $subscribe = NewsLetter::create(['email' => $request->email]);

        return !empty($subscribe) ? response()->json(['status'=>'success']) : response()->json(['status'=>'failed','msg' => __('Something Went wrong')]);
    }

    public function subscriber_verify(Request $request){
        $newsletter = NewsLetter::where('token',$request->token)->first();
        $title = __('Sorry');
        $description = __('your token is expired');
        if (!empty($newsletter)){
            NewsLetter::where('token',$request->token)->update([
                'verified' => 'yes'
            ]);
        }
        return redirect()->route('homepage')->with(toastr_success(__('Thanks. we are really thankful to you for subscribe our newsletter')));
    }
}
