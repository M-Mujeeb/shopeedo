<?php

namespace App\Http\Controllers;

use App\Models\BusinessSetting;
use Illuminate\Http\Request;

class PlatformController extends Controller
{
    //
    public function index()
    {
        // $rate = BusinessSetting::
        $platform_fee = BusinessSetting::where('type', 'platform_fee')->first();


        return view('backend.platform.index', compact('platform_fee'));
    }

    public function store(Request $request)
    {
        // Validate the input
        $validatedData = $request->validate([
            'platform_rate' => 'required|max:255',
        ]);

        // Create or update the platform fee in the database
        BusinessSetting::updateOrCreate(
            ['type' => 'platform_fee'],
            ['value' => $request->platform_rate]
        );

        return back()->with('success', 'Platform fee updated successfully.');
    }
}
