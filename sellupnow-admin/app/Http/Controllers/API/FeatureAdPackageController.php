<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\FeaturedAdPackage;
use Illuminate\Http\Request;

class FeatureAdPackageController extends Controller
{
    public function index(Request $request)
    {
        $packages = FeaturedAdPackage::active()
            ->orderBy('price')
            ->get();

        return $this->json('featured ad packages', [
            'total'    => $packages->count(),
            'packages' => $packages,
        ]);
    }
}
