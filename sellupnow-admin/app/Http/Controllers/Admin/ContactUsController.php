<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactUs;
use Illuminate\Http\Request;

class ContactUsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $contactUs = ContactUs::firstOrCreate([]);

        return view('admin.contact-us', compact('contactUs'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ContactUs $contactUs)
    {
        $data = $request->validate([
            'phone' => 'nullable|string|max:30',
            'whatsapp' => 'nullable|string|max:30',
            'messenger' => 'nullable|url|max:255',
            'email' => 'nullable|email|max:255',
        ]);

        ContactUs::updateOrCreate(['id' => $contactUs->id], $data);

        return back()->with('success', __('Contact Us Updated Successfully'));
    }
}
