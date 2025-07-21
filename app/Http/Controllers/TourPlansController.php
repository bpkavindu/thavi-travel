<?php

namespace App\Http\Controllers;

use App\Models\TourPlanDays;
use App\Models\TourPlanImages;
use App\Models\TourPlans;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TourPlansController extends Controller
{
    public function store(Request $request)
    {

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'dayCount' => 'required|integer|min:1',
            'days' => 'required|array|min:1',
            'days.*.title' => 'required|string|max:255',
            'days.*.description' => 'required|string',
            'special' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'images.*' => 'nullable|image|max:5120', // max 5MB per image
        ]);

        // Create TourPlan record
        $tourPlan = TourPlans::create([
            'user_id' => auth()->id(),
            'title' => $validated['title'],
            'special' => $request->input('special'),
            'price' => $validated['price'],
        ]);

        // Create Day records
        foreach ($validated['days'] as $dayData) {
            $tourPlandays = TourPlanDays::create([
                'tour_plan_id' => $tourPlan->id,
                'title' => $dayData['title'],
                'description' => $dayData['description'],
            ]);
        }

        // Store images and create records
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('tour_plans_images', 'public');
                 $tourPlanimg = TourPlanImages::create([
                    'tour_plan_id' => $tourPlan->id,
                    'path' => $path,
            ]);
            }
        }

        return redirect()->back()->with('success', 'Tour plan created successfully.');
    }

      public function update(Request $request, $id)
    {
        $tourPlan = TourPlans::findOrFail($id);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'special' => 'nullable|string|max:255',
            'days' => 'required|array',
            'days.*.title' => 'required|string|max:255',
            'days.*.description' => 'required|string',
            'removeImageIds' => 'nullable|array',
            'removeImageIds.*' => 'integer|exists:tour_plan_images,id',
            'newImages' => 'nullable|array',
            'newImages.*' => 'image|max:2048', // max 2MB each
        ]);

        // Update main tour plan info
        $tourPlan->title = $validated['title'];
        $tourPlan->price = $validated['price'];
        $tourPlan->special = $validated['special'] ?? null;
        $tourPlan->save();

        // Update days (assuming TourPlan hasMany TourPlanDay)
        // Simplest: delete all existing days and re-insert
        $tourPlan->days()->delete();
        foreach ($validated['days'] as $day) {
            $tourPlan->days()->create([
                'title' => $day['title'],
                'description' => $day['description'],
            ]);
        }

        // Delete removed images
        if (!empty($validated['removeImageIds'])) {
            $imagesToDelete = TourPlanImages::whereIn('id', $validated['removeImageIds'])->get();
            foreach ($imagesToDelete as $img) {
                Storage::delete($img->path); // Delete from storage
                $img->delete();              // Delete record
            }
        }

        // Save new uploaded images
        if ($request->hasFile('newImages')) {
            foreach ($request->file('newImages') as $file) {
                 $path = $file->store('tour_plans_images', 'public');
                $tourPlan->images()->create([
                    'path' => $path,
                ]);
            }
        }

        return redirect()->back()->with('success', 'Tour plan Update successfully.');
    }

    // Delete Tour Plan
    public function destroy($id)
    {
        $tourPlan = TourPlans::findOrFail($id);

        // Delete tour plan itself
        $tourPlan->delete();

         return redirect()->back()->with('success', 'Tour Plan deleted successfully');
    }

}
