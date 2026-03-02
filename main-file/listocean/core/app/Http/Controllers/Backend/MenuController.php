<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Backend\Menu;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    public function index()
    {
        $all_menu = Menu::all();
        return view('backend.pages.menu.index')->with([
            'all_menu' => $all_menu
        ]);
    }

    public function storeNewMenu(Request $request)
    {
        $this->validate($request, [
            'content' => 'nullable',
            'title' => 'required',
        ]);

        Menu::create([
            'content' => $request->page_content,
            'title' => $request->title,
        ]);

        return redirect()->back()->with([
            'msg' => __('New Menu Created...'),
            'type' => 'success'
        ]);
    }
    public function editMenu($id)
    {
        $page_post = Menu::find($id);

        return view('backend.pages.menu.edit')->with([
            'page_post' => $page_post
        ]);

    }
    public function updateMenu(Request $request, $id)
    {
        $this->validate($request, [
            'content' => 'nullable',
            'title' => 'required',
        ]);
        Menu::where('id', $id)->update([
            'content' => $request->menu_content,
            'title' => $request->title,
        ]);

        return redirect()->back()->with([
            'msg' => __('Menu updated...'),
            'type' => 'success'
        ]);
    }

    public function deleteMenu(Request $request, $id)
    {
        Menu::find($id)->delete();
        return redirect()->back()->with([
            'msg' => __('Menu Delete Success...'),
            'type' => 'danger'
        ]);
    }

    public function setDefaultMenu(Request $request, $id)
    {
        $lang = Menu::find($id);
        Menu::where(['status' => 'default'])->update(['status' => '']);

        Menu::find($id)->update(['status' => 'default']);
        $lang->status = 'default';
        $lang->save();
        return redirect()->back()->with([
            'msg' => __('Default Menu Set To') .' '. purify_html($lang->title),
            'type' => 'success'
        ]);
    }
    public function megaMenuItemSelectMarkup(Request $request){

        return render_mega_menu_item_select_markup($request->type,$request->menu_id);
    }
}
