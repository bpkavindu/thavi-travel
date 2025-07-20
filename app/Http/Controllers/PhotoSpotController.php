<?php

namespace App\Http\Controllers;

use App\Models\PhotoSpot;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class PhotoSpotController extends Controller
{
    public function index()
    {
        $spots = PhotoSpot::all();
        return Inertia::render('PhotoSpots', [
            'spots' => $spots
        ]);
    }
    public function store(Request $request)
{
    $data = $request->validate([
        'name' => 'required|string',
        'location' => 'required|string',
        'province' => 'nullable|string',
        'description' => 'nullable|string',
        'best_time' => 'nullable|string',
        'difficulty' => 'required|string',
        'rating' => 'required|numeric|min:0|max:5',
        'likes' => 'nullable|integer',
        'image' => 'nullable|image|max:2048',
    ]);

    if ($request->hasFile('image')) {
        $data['image'] = $request->file('image')->store('photo_spots', 'public');
    }

    PhotoSpot::create($data);

    return redirect()->back()->with('success', 'Photo spot added!');
}

}
