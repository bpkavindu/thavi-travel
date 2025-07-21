<?php

namespace App\Http\Controllers;

use App\Models\TourGuide;
use App\Models\TourPlans;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Illuminate\Support\Str; 

class TourGuideController extends Controller
{
    public function index()
{
    // dd(Auth::user()->user_type_id);
    if (Auth::user()->user_type_id == 4) {
        $guides = TourGuide::latest()->where('user_id',Auth::user()->id)->get();
        $plans = TourPlans::with(['days', 'images'])->latest()->where('user_id',Auth::user()->id)->get();

    }else{
    $guides = TourGuide::latest()->get();
    $plans = TourPlans::with(['days', 'images'])->latest()->get();

    }

    return Inertia::render('IndexTourGuides', [
        'guides' => $guides,
        'tourPlans' => $plans,
    ]);
}
 
public function update(Request $request, $id)
{
    $guide = TourGuide::findOrFail($id);

    $validated = $request->validate([
        'name' => 'required|string',
        'bio' => 'nullable|string',
        'experience' => 'nullable|integer',
        'rating' => 'nullable|numeric',
        'languages' => 'nullable|string',
        'locations' => 'nullable|string',
        'specialties' => 'nullable|string',
        'photo' => 'nullable|image|max:2048', // <-- image validation
    ]);

    if ($request->hasFile('photo')) {
        $validated['photo'] = $request->file('photo')->store('guides', 'public'); // Store in public disk
    }

    $guide->update($validated);

    return back()->with('success', 'Guide updated.');
}
}
