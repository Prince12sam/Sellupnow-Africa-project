<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\CountriesNowLocationImporter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ListoceanCountryController extends Controller
{
    private function normalizeIso2(?string $value): ?string
    {
        $value = strtoupper(trim((string) $value));

        if ($value === '') {
            return null;
        }

        return preg_match('/^[A-Z]{2}$/', $value) ? $value : null;
    }

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
            'country_code' => ['nullable', 'regex:/^[A-Za-z]{2}$/'],
            'dial_code' => 'nullable|string|max:10',
            'status' => 'required|in:0,1',
        ]);

        $data['country_code'] = $this->normalizeIso2($data['country_code'] ?? null);

        $db = DB::connection('listocean');

        $exists = $db->table('countries')
            ->where('country', $data['country'])
            ->exists();

        if ($exists) {
            return back()->withError(__('Country already exists'));
        }

        $countryId = (int) $db->table('countries')->insertGetId([
            'name' => $data['country'],
            'country' => $data['country'],
            'country_code' => $data['country_code'],
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
            'country_code' => ['nullable', 'regex:/^[A-Za-z]{2}$/'],
            'dial_code' => 'nullable|string|max:10',
            'status' => 'required|in:0,1',
        ]);

        $data['country_code'] = $this->normalizeIso2($data['country_code'] ?? null);

        DB::connection('listocean')->table('countries')->where('id', $id)->update([
            'name' => $data['country'],
            'country' => $data['country'],
            'country_code' => $data['country_code'],
            'dial_code' => $data['dial_code'] ?? null,
            'status' => (int) $data['status'],
            'updated_at' => now(),
        ]);

        return to_route('admin.siteCountry.index')->withSuccess(__('Updated Successfully'));
    }

    public function reimport(int $id)
    {
        $db = DB::connection('listocean');
        $country = $db->table('countries')->where('id', $id)->first();

        if (! $country) {
            return back()->withError(__('Country not found.'));
        }

        try {
            $result = app(CountriesNowLocationImporter::class)->import($id, $country->country);

            if (($result['ok'] ?? false) === true) {
                $msg = __('Re-import done: :states states and :cities cities imported.', [
                    'states' => (int) ($result['states_created'] ?? 0),
                    'cities' => (int) ($result['cities_created'] ?? 0),
                ]);

                return back()->withSuccess($msg);
            }

            return back()->withError(__('Import failed: :error', [
                'error' => (string) ($result['error'] ?? 'unknown error'),
            ]));
        } catch (\Throwable $th) {
            return back()->withError(__('Import failed: :error', ['error' => $th->getMessage()]));
        }
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
