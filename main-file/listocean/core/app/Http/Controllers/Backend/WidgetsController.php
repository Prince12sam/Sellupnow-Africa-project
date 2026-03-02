<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Backend\Widget;
use Illuminate\Http\Request;
use plugins\WidgetsBuilder\WidgetBuilderSetup;

class WidgetsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }
    public function index()
    {
        return view('backend.widgets.index');
    }

    public function widgetMarkup(Request $request)
    {
        $output = WidgetBuilderSetup::render_widgets_by_name_for_admin([
            'name' => $request->widget_name,
            'type' => 'new',
            'after' => false,
            'before' => false,
        ]);
        return $output;
    }

    public function newWidget(Request $request)
    {

        $this->validate($request, [
            'widget_name' => 'required',
            'widget_order' => 'required',
            'widget_location' => 'required',
        ]);

        unset($request['_token']);
        $widget_content = (array)$request->all();

        $widget_id = Widget::create([
            'widget_name' => $request->widget_name,
            'widget_order' => $request->widget_order,
            'widget_location' => $request->widget_location,
            'widget_content' => json_encode($widget_content),
        ])->id;

        $data['id'] = $widget_id;
        $data['status'] = 'ok';
        return response()->json($data);
    }


    public function updateWidget(Request $request)
    {
        $this->validate($request, [
            'widget_name' => 'required',
            'widget_order' => 'required',
            'widget_location' => 'required',
        ]);

        unset($request['_token']);
        $widget_content = (array)$request->all();

        if (!empty($request->id)) {
            Widget::findOrFail($request->id)->update([
                'widget_name' => $request->widget_name,
                'widget_order' => $request->widget_order,
                'widget_location' => $request->widget_location,
                'widget_content' => json_encode($widget_content),
            ]);
        }

        return response()->json('ok');
    }

    public function deleteWidget(Request $request)
    {
        Widget::findOrFail($request->id)->delete();
        return response()->json('ok');
    }

    public function updateOrderWidget(Request $request)
    {
        Widget::findOrFail($request->id)->update(['widget_order' => $request->widget_order]);
        return response()->json('ok');
    }
}
