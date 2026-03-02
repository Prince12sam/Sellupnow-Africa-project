<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\CountriesNowLocationImporter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ListoceanCountryController extends Controller
{
    public function index(Request $request)
    {
        $search = (string) $request->get('search', '');

        $countries = DB::connection('listocean')
            ->table('countries')
            ->when($search !== '', function ($query) use ($search) {
                $query->where('country', 'like', '%'.$search.'%')
                    ->orWhere('country_code', 'like', '%'.$search.'%')
                    ->orWhere('dial_code', 'like', '%'.$search.'%');
            })
            ->orderBy('country')
            ->paginate(20)
            ->withQueryString();

        return view('admin.listocean-location.countries', compact('countries'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'country' => 'required|string|max:100',
            'country_code' => 'nullable|string|max:10',
            'dial_code' => 'nullable|string|max:10',
            'status' => 'required|in:0,1',
        ]);

        $db = DB::connection('listocean');

        $exists = $db->table('countries')
            ->where('country', $data['country'])
            ->exists();

        if ($exists) {
            return back()->withError(__('Country already exists'));
        }

        $countryId = (int) $db->table('countries')->insertGetId([
            'country' => $data['country'],
            'country_code' => $data['country_code'] ?? null,
            'dial_code' => $data['dial_code'] ?? null,
            'status' => (int) $data['status'],
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $importSummary = null;
        try {
            $result = app(CountriesNowLocationImporter::class)->import($countryId, $data['country']);
            if (($result['ok'] ?? false) === true) {
                $importSummary = __('Imported :cities cities and :towns towns', [
                    'cities' => (int) ($result['states_created'] ?? 0),
                    'towns' => (int) ($result['cities_created'] ?? 0),
                ]);
            } else {
                $importSummary = __('Auto-import failed: :error', [
                    'error' => (string) ($result['error'] ?? 'unknown error'),
                ]);
            }
        } catch (\Throwable $th) {
            $importSummary = __('Auto-import failed: :error', [
                'error' => $th->getMessage(),
            ]);
        }

        $msg = __('Created Successfully');
        if ($importSummary) {
            $msg .= ' — '.$importSummary;
        }

        return to_route('admin.siteCountry.index')->withSuccess($msg);
    }

    public function update(Request $request, int $id)
    {
        $data = $request->validate([
            'country' => 'required|string|max:100',
            'country_code' => 'nullable|string|max:10',
            'dial_code' => 'nullable|string|max:10',
            'status' => 'required|in:0,1',
        ]);

        DB::connection('listocean')->table('countries')->where('id', $id)->update([
            'country' => $data['country'],
            'country_code' => $data['country_code'] ?? null,
            'dial_code' => $data['dial_code'] ?? null,
            'status' => (int) $data['status'],
            'updated_at' => now(),
        ]);

        return to_route('admin.siteCountry.index')->withSuccess(__('Updated Successfully'));
    }

    public function destroy(int $id)
    {
        $db = DB::connection('listocean');

        $db->transaction(function () use ($db, $id) {
            $stateIds = $db->table('states')->where('country_id', $id)->pluck('id')->all();

            if (! empty($stateIds)) {
                $db->table('cities')->whereIn('state_id', $stateIds)->delete();
            }

            $db->table('cities')->where('country_id', $id)->delete();
            $db->table('states')->where('country_id', $id)->delete();
            $db->table('countries')->where('id', $id)->delete();
        });

        return back()->withSuccess(__('Deleted Successfully'));
    }
}
