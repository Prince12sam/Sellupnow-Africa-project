<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Backend\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminProfileController extends Controller
{
    public function adminProfileUpdate(Request $request){
        $this->validate($request,[
            'name' => 'required|string|max:191',
            'email' => 'required|email|max:191',
            'image' => 'nullable|string',
        ]);

        Admin::find(Auth::user()->id)->update([
            'name'=>$request->name,
            'email' => $request->email ,
            'image' => $request->image ,
            'about' => $request->about
        ]);
        return redirect()->back()->with(['msg' => __('Profile Update Success' ), 'type' => 'success']);
    }

    public function adminPasswordChange(Request $request){
        $this->validate($request, [
            'old_password' => 'required|string',
            'password' => 'required|string|min:8|confirmed'
        ]);

        $user = Admin::findOrFail(Auth::guard('admin')->user()->id);
        if (Hash::check($request->old_password ,$user->password)){
            $user->password = Hash::make($request->password);
            $user->save();
            Auth::logout();
            return redirect()->route('admin.login')->with(['msg'=> __('Password Changed Successfully'),'type'=> 'success']);
        }
        return redirect()->back()->with(['msg'=> __('Somethings Going Wrong! Please Try Again or Check Your Old Password'),'type'=> 'danger']);
    }

    public function adminLogout(){
        Auth::logout();
        return redirect()->route('admin.login')->with(['msg'=>__('You Logged Out !!'),'type'=> 'danger']);
    }

    public function adminProfile(){
        return view('auth.admin.edit-profile');
    }

    public function adminPassword(){
        return view('auth.admin.change-password');
    }

}
