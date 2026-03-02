<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\CountryResource;
use App\Models\Country;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class CountryController extends Controller
{
    public function index()
    {
        $countries = Cache::rememberForever('countries', function () {
            return Country::all();
        });

        return $this->json('all countries', [
            'countries' => CountryResource::collection($countries),
        ]);
    }

    public function fetchStatesByCountry(Request $request)
    {
        $request->validate([
            'country_id' => 'nullable|integer|exists:countries,id',
            'country' => 'nullable|string|max:255',
        ]);

        return $this->json('states by country', [
            'states' => [],
        ]);
    }

    public function fetchCitiesByState(Request $request)
    {
        $request->validate([
            'state_id' => 'nullable',
            'state' => 'nullable|string|max:255',
        ]);

        return $this->json('cities by state', [
            'cities' => [],
        ]);
    }
}
