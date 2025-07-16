<?php

namespace App\Http\Controllers;

use App\Models\Attraction;
use App\Models\Province;
use Illuminate\Http\Request;
use Inertia\Inertia;


class AttractionController extends Controller
{
  public function index()
{
    $attractions = Attraction::all();
    $province = Province::all();

    return Inertia::render('Attractions/IndexAttractions', [
        'attractions' => $attractions,
         'districts' => $province,
    ]);
}

public function store(Request $request)
{
    // Validate incoming data
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'rating' => 'required|numeric|min:0|max:5',
        'price' => 'required|string|max:3',
        'category' => 'required|string|max:50',
        'distance' => 'required|numeric|min:0',
        'country' => 'required|string|max:100',
        'city' => 'required|string|max:100',
        'image' => 'nullable|string', // base64 string or nullable
    ]);

    // Save to DB
    $attraction = Attraction::create($validated);

    // Return back or redirect with success message
    return redirect()->back()->with('success', 'Attraction saved successfully!');
}

}
