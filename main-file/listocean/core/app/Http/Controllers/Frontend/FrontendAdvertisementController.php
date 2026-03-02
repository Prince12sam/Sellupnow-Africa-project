<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Backend\Advertisement;
use App\Models\Frontend\Visitor;
use Illuminate\Http\Request;
use Stevebauman\Location\Facades\Location;

class FrontendAdvertisementController extends Controller
{
    public function home_advertisement_click_store(Request $request)
    {
        // Increment 'click' count for the advertisement
        Advertisement::where('id',$request->id)->increment('click');

        // Store visitor data
        $ip = $request->ip();
        $userAgent = $request->header('User-Agent');

        // Check if a visitor record exists within the last 10 minutes
        $existingVisitor = Visitor::where('ip_address', $ip)
            ->where('user_agent', $userAgent)
            ->where('created_at', '>=', now()->subMinutes(5))
            ->first();

        if ($existingVisitor) {
            // Update existing record
            $existingVisitor->touch(); // Update the timestamp
        } else {
            // Retrieve location data
            $location = Location::get($ip);
            // Create a new Visitor record
            Visitor::create([
                'advertisement_id' => $request->id,
                'ip_address' => $ip,
                'user_agent' => $userAgent,
                'country' => $location->countryName ?? null,
                'city' => $location->cityName ?? null,
                'country_code' => $location->countryCode ?? null,
                'latitude' => $location->latitude ?? null,
                'longitude' => $location->longitude ?? null,
            ]);
        }

        return response()->json('success');
    }

    public function home_advertisement_impression_store(Request $request)
    {
        // Increment 'impression' count for the advertisement
        Advertisement::where('id',$request->id)->increment('impression');

        // Store visitor data
        $ip = $request->ip();
        $userAgent = $request->header('User-Agent');

        // Check if a visitor record exists within the last 10 minutes
        $existingVisitor = Visitor::where('ip_address', $ip)
            ->where('user_agent', $userAgent)
            ->where('created_at', '>=', now()->subMinutes(5))
            ->first();

        if ($existingVisitor) {
            // Update existing record
            $existingVisitor->touch();
        } else {
            // Retrieve location data
            $location = Location::get($ip);
            // Create a new Visitor record
            Visitor::create([
                'advertisement_id' => $request->id,
                'ip_address' => $ip,
                'user_agent' => $userAgent,
                'country' => $location->countryName ?? null,
                'city' => $location->cityName ?? null,
                'country_code' => $location->countryCode ?? null,
                'latitude' => $location->latitude ?? null,
                'longitude' => $location->longitude ?? null,
            ]);
        }

        return response()->json('success');
    }
}
