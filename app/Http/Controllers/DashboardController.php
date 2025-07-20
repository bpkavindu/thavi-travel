<?php

namespace App\Http\Controllers;

use App\Models\Chat;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
   public function index()
{
    $user = auth()->user();

    // Fetch chat messages related to this user (you can add admin-specific filtering)
    $chatMessages = Chat::where('user_id', $user->id)
        ->orderBy('created_at')
        ->get(['sender', 'message as text', 'created_at']);

    return Inertia::render('Dashboard', [
        'chatMessages' => $chatMessages,
        'userId' => $user->id,
    ]);
}
}
