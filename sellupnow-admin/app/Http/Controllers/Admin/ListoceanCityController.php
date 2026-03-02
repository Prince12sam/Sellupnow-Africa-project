<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ListoceanCityController extends Controller
{
    public function index(Request $request)
    {
        $search = (string) $request->get('search', '');
        $countryId = $request->filled('country_id') ? (int) $request->get('country_id') : null;
        $stateId = $request->filled('state_id') ? (int) $request->get('state_id') : null;

        $countries = DB::connection('listocean')
            ->table('countries')
            ->select(['id', 'country'])
            ->orderBy('country')
            ->get();

        $states = DB::connection('listocean')
            ->table('states as s')
            ->leftJoin('countries as c', 'c.id', '=', 's.country_id')
            ->select([
                's.id',
                's.state',
                's.country_id',
                'c.country as country_name',
            ])
            ->when($countryId !== null, function ($query) use ($countryId) {
                $query->where('s.country_id', $countryId);
            })
            ->orderBy('c.country')
            ->orderBy('s.state')
            ->get();

        $cities = DB::connection('listocean')
            ->table('cities as ci')
            ->leftJoin('states as s', 's.id', '=', 'ci.state_id')
            ->leftJoin('countries as c', 'c.id', '=', 'ci.country_id')
            ->select([
                'ci.id',
                'ci.city',
                'ci.country_id',
                'ci.state_id',
                'ci.status',
                's.state as state_name',
                'c.country as country_name',
            ])
            ->when($countryId !== null, function ($query) use ($countryId) {
                $query->where('s.country_id', $countryId);
            })
            ->when($stateId !== null, function ($query) use ($stateId) {
                $query->where('ci.state_id', $stateId);
            })
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('ci.city', 'like', '%'.$search.'%')
                        ->orWhere('s.state', 'like', '%'.$search.'%')
                        ->orWhere('c.country', 'like', '%'.$search.'%');
                });
            })
            ->orderBy('c.country')
            ->orderBy('s.state')
            ->orderBy('ci.city')
            ->paginate(20)
            ->withQueryString();

        return view('admin.listocean-location.cities', compact('cities', 'countries', 'states'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'state_id' => 'required|integer',
            'city' => 'required|string|max:150',
            'status' => 'required|in:0,1',
        ]);

        $state = DB::connection('listocean')->table('states')->where('id', (int) $data['state_id'])->first();
        if (! $state) {
            return back()->withError(__('State not found'));
        }

        DB::connection('listocean')->table('cities')->insert([
            'country_id' => $state->country_id ?? null,
            'state_id' => (int) $data['state_id'],
            'city' => $data['city'],
            'status' => (int) $data['status'],
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return to_route('admin.siteCity.index')->withSuccess(__('Created Successfully'));
    }

    public function update(Request $request, int $id)
    {
        $data = $request->validate([
            'state_id' => 'required|integer',
            'city' => 'required|string|max:150',
            'status' => 'required|in:0,1',
        ]);

        $state = DB::connection('listocean')->table('states')->where('id', (int) $data['state_id'])->first();
        if (! $state) {
            return back()->withError(__('State not found'));
        }

        DB::connection('listocean')->table('cities')->where('id', $id)->update([
            'country_id' => $state->country_id ?? null,
            'state_id' => (int) $data['state_id'],
            'city' => $data['city'],
            'status' => (int) $data['status'],
            'updated_at' => now(),
        ]);

        return to_route('admin.siteCity.index')->withSuccess(__('Updated Successfully'));
    }

    public function destroy(int $id)
    {
        DB::connection('listocean')->table('cities')->where('id', $id)->delete();

        return back()->withSuccess(__('Deleted Successfully'));
    }
}
