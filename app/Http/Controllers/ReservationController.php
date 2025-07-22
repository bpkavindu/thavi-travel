<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class ReservationController extends Controller
{
    public function index()
    {
        if (Auth::user()->user_type_id == 4 || Auth::user()->user_type_id == 2) {
            $reservations = Reservation::with(['user', 'guide', 'tourPlan'])->where('guide_id', Auth::user()->id)
                ->latest()
                ->get();
        } else {
            $reservations = Reservation::with(['user', 'guide', 'tourPlan'])
                ->latest()
                ->get();
        }

        return Inertia::render('ReservationList', [
            'reservations' => $reservations,
        ]);
    }
    public function store(Request $request)
    {
        $validated = $request->validate([
            'phone' => 'required|string',
            'from_date' => 'required|date',
            'to_date' => 'required|date|after_or_equal:from_date',
            'guests' => 'required|integer|min:1',
            'tour_plan_id' => 'required|exists:tour_plans,id',
            'guide_id' => 'required|exists:users,id',
        ]);

        $user = auth()->user();

        $reservation = Reservation::create([
            'traveller_id' => $user->id,
            'guide_id' => $validated['guide_id'],
            'tour_plan_id' => $validated['tour_plan_id'],
            'guide_id' => $validated['guide_id'],
            'start_date' => $validated['from_date'],
            'end_date' => $validated['to_date'],
            'guest_count' => $validated['guests'],
            'phone' => $validated['phone'],
            'status' => \App\Enums\ReservationStatus::PENDING->value, // Use enum
        ]);

        return redirect()->back()->with('success', 'Reservation submitted successfully.');
    }
    public function destroy(Reservation $reservation)
    {
        $reservation->delete();

        return back()->with('success', 'Reservation canceled successfully.');
    }
}
