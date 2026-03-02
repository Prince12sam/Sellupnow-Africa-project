<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Listing;
use Illuminate\Http\Request;

class ListingSearchController extends Controller
{
    public function search(Request $request)
    {
        $this->authorize('viewAny', Listing::class);

        $q = (string) $request->query('q', '');
        $results = Listing::query()
            ->when($q !== '', fn($b) => $b->where('title', 'like', "%{$q}%"))
            ->orderBy('id', 'desc')
            ->limit(15)
            ->get(['id', 'title']);

        return response()->json(['data' => $results]);
    }
}
