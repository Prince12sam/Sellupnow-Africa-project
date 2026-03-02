<?php

namespace Modules\CountryManage\app\Http\Controllers;

use App\Helpers\FlashMsg;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Modules\CountryManage\app\Models\Country;

class CountryController extends Controller
{
    public function all_country(Request $request)
    {
        if($request->isMethod('post')){

            $request->validate([
                'country'=> 'required|unique:countries|max:191',
            ]);

          $country = Country::create([
                'country' => $request->country,
                'status' => $request->status,
            ]);

            if (!empty($country->country)) {
                try {
                    $response = Http::get('https://restcountries.com/v3.1/name/'.$country->country);
                    if ($response->ok()) {
                        $countryData = $response->json()[0] ?? null;
                        $dialCode = null;

                        // Check if the country data has the 'idd' key and retrieve the dialing code
                        if (isset($countryData['idd']['root'])) {
                            $dialCode = $countryData['idd']['root'];
                            if (isset($countryData['idd']['suffixes'][0])) {
                                $dialCode .= $countryData['idd']['suffixes'][0];
                            }
                        }

                        if ($countryData) {
                            $country->update([
                                'country_code' => $countryData['cca2'] ?? null,
                                'dial_code' =>  $dialCode,
                                'latitude' => $countryData['latlng'][0] ?? null,
                                'longitude' => $countryData['latlng'][1] ?? null,
                            ]);
                        } else {
                            FlashMsg::error(__('Country data not found for ' . $country->country));
                        }

                    } else {
                        FlashMsg::error(__('Failed to fetch country data for ' . $country->country));
                    }
                } catch (\Exception $e) {
                    FlashMsg::error(__('Error occurred while fetching country data: ') . $e->getMessage());
                }
            } else {
                FlashMsg::error(__('Invalid country name'));
            }

            FlashMsg::item_new(__('New Country Successfully Added'));
        }

        $all_countries = Country::latest()->paginate(10);
        return view('countrymanage::country.all-country',compact('all_countries'));
    }

    public function change_status_country($id)
    {
        $country = Country::select('status')->where('id',$id)->first();
        $country->status==1 ? $status=0 : $status=1;
        Country::where('id',$id)->update(['status'=>$status]);
        return redirect()->back()->with(FlashMsg::item_new(__('Status Successfully Changed')));
    }

    public function edit_country(Request $request)
    {
        $request->validate([
            'edit_country'=> 'required|max:191|unique:countries,country,'.$request->country_id,
        ]);
        Country::where('id',$request->country_id)->update([
            'country'=>$request->edit_country,
        ]);
        return redirect()->back()->with(FlashMsg::item_new(__('Country Successfully Updated')));
    }

    public function delete_country($id)
    {
        Country::find($id)->delete();
        return redirect()->back()->with(FlashMsg::item_delete(__('Country Successfully Deleted')));
    }

    public function bulk_action_country(Request $request){
        Country::whereIn('id',$request->ids)->delete();
        return redirect()->back()->with(FlashMsg::item_delete(__('Selected Country Successfully Deleted')));
    }

    public function import_settings()
    {
        return view('countrymanage::country.import-country');
    }

    public function update_import_settings(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt|max:150000'
        ]);

        //: work on file mapping
        if ($request->hasFile('csv_file')) {
            $file = $request->csv_file;
            $extenstion = $file->getClientOriginalExtension();
            if ($extenstion == 'csv') {
                //copy file to temp folder

                $old_file = Session::get('import_csv_file_name');
                if (file_exists('assets/uploads/import/' . $old_file)) {
                    @unlink('assets/uploads/import/' . $old_file);
                }
                $file_name_with_ext = $file->getClientOriginalName();

                $file_name = pathinfo($file_name_with_ext, PATHINFO_FILENAME);
                $file_name = strtolower(Str::slug($file_name));

                $file_tmp_name = $file_name . time() . '.' . $extenstion;
                $file->move('assets/uploads/import', $file_tmp_name);

                $data = array_map('str_getcsv', file('assets/uploads/import/' . $file_tmp_name));
                $csv_data = array_slice($data, 0, 1);

                Session::put('import_csv_file_name', $file_tmp_name);

                return view('countrymanage::country.import-country', [
                    'import_data' => $csv_data,
                ]);
            }

        }
        FlashMsg::item_update(__('something went wrong try again!'));
        return back();
    }

    public function import_to_database_settings(Request $request)
    {
        $file_tmp_name = Session::get('import_csv_file_name');
        $data = array_map('str_getcsv', file('assets/uploads/import/' . $file_tmp_name));

        $csv_data = current(array_slice($data, 0, 1));
        $csv_data = array_map(function ($item) {
            return trim($item);
        }, $csv_data);

        $imported_countries = 0;
        $x = 0;
        $country = array_search($request->country, $csv_data, true);

        foreach ($data as $index => $item) {
            if($x == 0){
                $x++;
                continue ;
            }
            $find_country = Country::where('country', $item[$country] )->count();

            if ($find_country < 1) {
                $country_data = [
                    'country' => $item[$country] ?? '',
                    'status' => $request->status,
                ];
            }
            if ($find_country < 1) {
                Country::create($country_data);
                $imported_countries++;
            }
        }
        FlashMsg::item_new($imported_countries.' '. __('Countries imported successfully'));
        return redirect()->route('admin.country.import.csv.settings');
    }

    // pagination
    function pagination(Request $request)
    {
        if($request->ajax()){
            $all_countries = Country::latest()->paginate(10);
            return view('countrymanage::country.search-result', compact('all_countries'))->render();
        }
    }

    // search category
    public function search_country(Request $request)
    {
        $all_countries= Country::where('country', 'LIKE', "%". strip_tags($request->string_search) ."%")
            ->paginate(10);
        if($all_countries->total() >= 1){
            return view('countrymanage::country.search-result', compact('all_countries'))->render();
        }else{
            return response()->json([
                'status'=>__('nothing')
            ]);
        }
    }
}
