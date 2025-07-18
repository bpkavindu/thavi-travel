<?php

namespace App\Http\Controllers;

use App\Models\Attraction;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AttractionMapController extends Controller
{
    public function showMap()
{
    // Later, you can fetch attractions from DB instead of hardcoded
    return Inertia::render('Attractions/AttractionMap', [
        'locations' => Attraction::all(),
         
    ]);
}
}
