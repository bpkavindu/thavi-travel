<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Inertia\Inertia;

class AiTourPlannerController extends Controller
{
    public function index(){
       {
        return Inertia::render('AITourPlanner');
    } 
    }
    public function handleForm(Request $request)
    {
        $data = $request->validate([
            'destination' => 'required|string',
            'days' => 'required|integer|min:1',
            'budget' => 'required|string',
            'group_size' => 'required|integer|min:1',
        ]);

        return view('tour-planner-result', compact('data'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'destination' => 'required|string|max:255',
            'days' => 'required|string',
            'budget' => 'required|string',
            'groupSize' => 'required|string',
        ]);

        // Generate itinerary
        $itinerary = $this->generateItinerary($validated);

        // Store in session (or DB if needed)
        session(['itinerary' => $itinerary]);

        return redirect()->route('tour.itinerary');
    }

    public function showItinerary()
    {
        $itinerary = session('itinerary');

        if (!$itinerary) {
            return redirect('/'); // Or show error
        }

        return Inertia::render('Itinerary', [
            'itinerary' =>  $itinerary,
        ]);
    }

    private function generateItinerary($data)
{
    $activities = $this->getAIActivities($data);

    return [
        'destination' => $data['destination'],
        'days' => $data['days'],
        'budget' => $data['budget'],
        'groupSize' => $data['groupSize'],
        'activities' => $activities,
    ];
}

private function getAIActivities($data)
{
    $prompt = "Generate a list of 5 travel activities for a trip to {$data['destination']} lasting {$data['days']} days, "
            . "for a group of {$data['groupSize']} people with a budget of {$data['budget']}. "
            . "List only the activities, comma-separated.";

    $response = Http::withHeaders([
        'Authorization' => 'Bearer ' . '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',
    ])->post('https://openrouter.ai/api/v1/chat/completions', [
        'model' => 'deepseek/deepseek-chat',
        'messages' => [
            ['role' => 'user', 'content' => $prompt],
        ],
    ]);

    if ($response->successful()) {
        $content = $response['choices'][0]['message']['content'] ?? '';
        return array_map('trim', explode(',', $content));
    }
dd($response);
    return ['Explore the destination', 'Visit local attractions'];
}

    // private function generateItinerary($data)
    // {

    //     $activities = [
    //         '1-3 days' => ['City walking tour', 'Museum visit', 'Local food tasting'],
    //         '4-7 days' => ['City tour', 'Day trip', 'Cultural experience', 'Boat cruise'],
    //         '8-14 days' => ['Multi-city trip', 'Adventure sports', 'Cultural immersion'],
    //         '15+ days' => ['In-depth country tour', 'Relaxation + adventure mix', 'Scenic excursions']
    //     ];

    //     return [
    //         'destination' => $data['destination'],
    //         'days' => $data['days'],
    //         'budget' => $data['budget'],
    //         'groupSize' => $data['groupSize'],
    //         'activities' => $activities[$data['days']] ?? ['Explore destination'],
    //     ];
    // }
}
