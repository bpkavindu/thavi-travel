<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
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
}
