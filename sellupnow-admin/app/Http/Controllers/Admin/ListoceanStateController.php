<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ListoceanStateController extends Controller
{
    public function index(Request $request)
    {
        $search = (string) $request->get('search', '');
        $countryId = $request->filled('country_id') ? (int) $request->get('country_id') : null;

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
                's.status',
                's.timezone',
                'c.country as country_name',
            ])
            ->when($countryId !== null, function ($query) use ($countryId) {
                $query->where('s.country_id', $countryId);
            })
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('s.state', 'like', '%'.$search.'%')
                        ->orWhere('c.country', 'like', '%'.$search.'%');
                });
            })
            ->orderBy('c.country')
            ->orderBy('s.state')
            ->paginate(20)
            ->withQueryString();

        return view('admin.listocean-location.states', compact('states', 'countries'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'country_id' => 'required|integer',
            'state' => 'required|string|max:150',
            'timezone' => 'nullable|string|max:120',
            'status' => 'required|in:0,1',
        ]);

        DB::connection('listocean')->table('states')->insert([
            'country_id' => (int) $data['country_id'],
            'state' => $data['state'],
            'timezone' => $data['timezone'] ?? null,
            'status' => (int) $data['status'],
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return to_route('admin.siteState.index')->withSuccess(__('Created Successfully'));
    }

    public function update(Request $request, int $id)
    {
        $data = $request->validate([
            'country_id' => 'required|integer',
            'state' => 'required|string|max:150',
            'timezone' => 'nullable|string|max:120',
            'status' => 'required|in:0,1',
        ]);

        DB::connection('listocean')->table('states')->where('id', $id)->update([
            'country_id' => (int) $data['country_id'],
            'state' => $data['state'],
            'timezone' => $data['timezone'] ?? null,
            'status' => (int) $data['status'],
            'updated_at' => now(),
        ]);

        return to_route('admin.siteState.index')->withSuccess(__('Updated Successfully'));
    }

    public function destroy(int $id)
    {
        $db = DB::connection('listocean');

        $db->transaction(function () use ($db, $id) {
            $db->table('cities')->where('state_id', $id)->delete();
            $db->table('states')->where('id', $id)->delete();
        });

        return back()->withSuccess(__('Deleted Successfully'));
    }
}
