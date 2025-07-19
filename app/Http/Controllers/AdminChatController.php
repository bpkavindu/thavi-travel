<?php

namespace App\Http\Controllers;

use App\Models\Chat;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AdminChatController extends Controller
{
   public function index()
{
    // Get users who have at least one chat and load latest chat
    $users = User::whereHas('chats')
        ->with(['latestChat']) // eager load the latest chat message
        ->get()
        ->map(function ($user) {
            return [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'latestMessage' => $user->latestChat ? [
                    'created_at' => $user->latestChat->created_at,
                    'text' => $user->latestChat->message,
                    'sender' => $user->latestChat->sender,
                ] : null
            ];
        });

    return Inertia::render('Auth/AdminChat', [
        'users' => $users
    ]);
}


   public function show(User $user)
{
    $messages = Chat::where('user_id', $user->id)
        ->orderBy('created_at', 'asc')
        ->get(['sender', 'message as text', 'created_at']);

    return Inertia::render('Auth/AdminChatView', [
        'user' => $user,
        'messages' => $messages,
    ]);
}

public function store(Request $request, User $user)
{
    Chat::create([
        'user_id' => $user->id,
        'sender' => 'admin',
        'message' => $request->input('message'),
    ]);

    return redirect()->back();
}


    public function send(Request $request, User $user)
    {
        $request->validate(['message' => 'required|string']);

        $chat = Chat::create([
            'user_id' => $user->id,
            'sender' => 'Admin',
            'message' => $request->message,
        ]);

        return back()->with('success', 'Message sent');
    }
}
