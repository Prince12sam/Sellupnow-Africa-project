<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use Illuminate\Http\Request;

class FaqController extends Controller
{
    public function index(Request $request)
    {
        $faqs = Faq::active()->orderBy('sort_order')->orderBy('id')->get(['id', 'question', 'answer']);

        return $this->json('faq list', [
            'total' => $faqs->count(),
            'faqs'  => $faqs,
        ]);
    }
}
