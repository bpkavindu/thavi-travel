<?php

namespace App\Http\Controllers;

use App\Mail\GuideReservationNotification;
use App\Models\Reservation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
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

        $formattedPhone = ltrim($validated['phone'], '0');
        if (!str_starts_with($formattedPhone, '94')) {
            $formattedPhone = '94' . $formattedPhone;
        }
        $message = "Hello {$reservation->guide->name}, you have received a new reservation (Ref: #{$reservation->id}) from {$user->name}. Tour: {$reservation->tourPlan->title}, Guests: {$reservation->guest_count}, Date: {$reservation->start_date} to {$reservation->end_date}.";

        if (env('NOTIFY_USER_ID') == null) {
            $this->sendSms($formattedPhone, $message);
        }

        return redirect()->back()->with('success', 'Reservation submitted successfully.');
    }

    public function sendSms($to, $message)
    {
        $user_id   = env('NOTIFY_USER_ID');
        $api_key   = env('NOTIFY_API_KEY');
        $sender_id = env('NOTIFY_SENDER_ID');

        $response = Http::asForm()->post('https://app.notify.lk/api/v1/send', [
            'user_id'   => $user_id,
            'api_key'   => $api_key,
            'sender_id' => $sender_id,
            'to'        => $to,
            'message'   => $message,
        ]);

        if (!$response->successful()) {
            Log::error('SMS sending failed', ['response' => $response->body()]);
        }
    }


    public function destroy(Reservation $reservation)
    {
        $reservation->delete();

        return back()->with('success', 'Reservation canceled successfully.');
    }
}
