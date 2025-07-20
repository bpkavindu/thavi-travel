<?php

namespace App\Http\Controllers;

use App\Models\Chat;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ChatController extends Controller
{

     public function store(Request $request)
    {
        $request->validate([
            'message' => 'required|string',
        ]);

        $chat = Chat::create([
            'user_id' => auth()->id(),
            'sender' => 'user', // or 'admin'
            'message' => $request->message,
        ]);

        return redirect()->back()->with('success', 'Chat sent successfully');

    }

    // Admin sends message
    public function reply(Request $request, $userId)
    {
        $chat = Chat::create([
            'user_id' => $userId,
            'sender' => 'admin',
            'message' => $request->message,
        ]);

        return response()->json($chat);
    }

    // Admin loads chat with specific user
    public function userChats($userId)
    {
        return response()->json(Chat::where('user_id', $userId)->get());
    }
}
