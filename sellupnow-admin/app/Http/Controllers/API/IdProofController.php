<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\IdProofType;
use Illuminate\Http\Request;

class IdProofController extends Controller
{
    public function index(Request $request)
    {
        $types = IdProofType::active()->orderBy('id')->get(['id', 'name']);

        return $this->json('id proof types', [
            'total' => $types->count(),
            'types' => $types,
        ]);
    }
}
