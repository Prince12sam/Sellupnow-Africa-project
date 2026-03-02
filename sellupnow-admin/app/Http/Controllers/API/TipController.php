<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Tip;
use Illuminate\Http\Request;

class TipController extends Controller
{
    public function index(Request $request)
    {
        $tips = Tip::active()->orderBy('sort_order')->orderBy('id')->get(['id', 'title', 'content']);

        return $this->json('helpful hints', [
            'total' => $tips->count(),
            'tips'  => $tips,
        ]);
    }
}
