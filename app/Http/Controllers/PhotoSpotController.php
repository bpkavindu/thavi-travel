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
        return Inertia::render('PhotoSpots', [
            'spots' => PhotoSpot::all(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|max:2048',  // max 2MB
            'qr_code' => 'nullable|string|max:100',
        ]);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('public/images');
            $validated['image_path'] = str_replace('public/', '', $path);
        }

        PhotoSpot::create($validated);

        return redirect()->route('photo-spots.index')->with('success', 'Photo spot added!');
    }
}
