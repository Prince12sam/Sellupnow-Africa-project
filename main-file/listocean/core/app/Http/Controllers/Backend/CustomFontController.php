<?php

namespace App\Http\Controllers\Backend;

use App\Helpers\FlashMsg;
use App\Http\Controllers\Controller;
use App\Models\Backend\CustomFont;
use Illuminate\Http\Request;

class CustomFontController extends Controller
{
    public function addCustomFont(Request $request)
    {
        $request->validate([
            'files' => 'required',
        ]);

        if($request->hasfile('files'))
        {
            foreach($request->file('files') as $key => $file)
            {
                if($file->getClientOriginalExtension() == "ttf"){
                    \Validator::make(["font_file_".$key => $file], [
                        "font_file_".$key => ["file","required",'mimetypes:font/ttf,font/sfnt']
                    ])->validated();
                }else{
                    \Validator::make(["font_file_".$key => $file], [
                        "font_file_".$key => ["file","required",'mimes:woff,woff2,eot']
                    ])->validated();
                }

                if(in_array($file->getClientOriginalExtension(),['ttf','woff','woff2','eot'])){
                    $name = $file->getClientOriginalName();
                    $path = 'assets/common/fonts/custom-fonts/css/'.$name;
                    // remove file type
                    $file_name = pathinfo($path, PATHINFO_FILENAME);
                    $file->move('assets/common/fonts/custom-fonts/css', $name);
                    $insert[$key]['file'] = $file_name;
                    $insert[$key]['path'] = $path;
                }
            }
        }
        CustomFont::insert($insert);
        return redirect()->back()->with(['status', 'msg' => __('Custom Font has been uploaded Successfully')]);
    }


    // Delete File
    public function deleteFontFile(Request $request) {
        $find_file = CustomFont::find($request->id);
        if(file_exists($find_file->path)){
            unlink($find_file->path);
        }
        CustomFont::where("id", $find_file->id)->delete();
        return redirect()->back()->with("success", __('Custom Font Deleted Successfully.'));
    }

    // add custom font css
    public function updateCssCustomFont(Request $request)
    {
        update_static_option('google_font',$request->google_font);
        update_static_option('custom_font',$request->custom_font);
        file_put_contents('assets/common/fonts/custom-fonts/css/custom_font.css', $request->custom_css_area);
        return redirect()->back()->with(['msg' => __('Custom Font Css Successfully Added...'), 'type' => 'success']);
    }


    // body font status change
    public function changeStatusCustomFont($id)
    {
        CustomFont::where('status', 1)->update(['status'=>0]);
        $custom_font = CustomFont::select('status')->where('id',$id)->first();
        CustomFont::where('id',$id)->update(['status'=>1]);
        return redirect()->back()->with(FlashMsg::item_new('Body Font Add Success'));
    }

    // heading font add
    public function changeStatusCustomFontHeading($id)
    {
        CustomFont::where('status', 2)->update(['status'=>0]);
        $custom_font = CustomFont::select('status')->where('id',$id)->first();
        CustomFont::where('id',$id)->update(['status'=>2]);
        return redirect()->back()->with(FlashMsg::item_new('Heading Font Add Success'));
    }

    public function typographySettings()
    {
        $all_google_fonts = file_get_contents('assets/frontend/webfonts/google-fonts.json');
        $custom_css = '/* Write Custom Css Here */';

        if (file_exists('assets/common/fonts/custom-fonts/css/custom_font.css')) {
            $custom_css = file_get_contents('assets/common/fonts/custom-fonts/css/custom_font.css');
        }

        $all_fonts = CustomFont::all();
        $fonts = CustomFont::select('status')->where('status', '!=', 0)->get();
        $custom_font = $fonts->count();
        return view('backend.general-settings.typograhpy', compact('all_fonts', 'custom_font'))->with(['google_fonts' => json_decode($all_google_fonts), 'custom_css' => $custom_css]);
    }

    public function getSingleFontVariant(Request $request)
    {
        $all_google_fonts = file_get_contents('assets/frontend/webfonts/google-fonts.json');
        $decoded_fonts = json_decode($all_google_fonts, true);
        return response()->json($decoded_fonts[$request->font_family]);
    }

    public function updateTypographySettings(Request $request)
    {
        $this->validate($request, [
            'body_font_family' => 'required|string|max:191',
            'body_font_variant' => 'required',
            'heading_font' => 'nullable|string',
            'heading_font_family' => 'nullable|string|max:191',
            'heading_font_variant' => 'nullable',
        ]);

        $save_data = [
            'body_font_family',
            'heading_font_family',
            'extra_body_font',
            'google_font',
            'custom_font',
        ];
        foreach ($save_data as $item) {
            update_static_option($item, $request->$item);
        }

        $body_font_variant = !empty($request->body_font_variant) ?  $request->body_font_variant : ['regular'];
        $heading_font_variant = !empty($request->heading_font_variant) ?  $request->heading_font_variant : ['regular'];
        update_static_option('heading_font', $request->heading_font);
        update_static_option('body_font_variant', serialize($body_font_variant));
        update_static_option('heading_font_variant', serialize($heading_font_variant));

        //Extra
        $fonts = [
            'body_font_family_three',
            'body_font_family_four',
            'body_font_family_five',
        ];

        foreach($fonts as $font){
            update_static_option($font, $request->$font);
        }

        $fonts_variants = [
            'body_font_variant_three',
            'body_font_variant_four',
            'body_font_variant_five',
        ];
        foreach ($fonts_variants as $vari){
            $body_font_variant = !empty($request->$vari) ?  $request->$vari : ['400'];
            update_static_option($vari, serialize($body_font_variant));
        }
        CustomFont::where('status', '!=', 0)->update(['status'=>0]);
        return redirect()->back()->with(['msg' => __('Typography Settings Updated..'), 'type' => 'success']);
    }
}
