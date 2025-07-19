<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserType;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Inertia\Inertia;
use Inertia\Response;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
public function index(): Response
    {
        // dd(User::all());
       $users = User::with('user_type')->get();

    return Inertia::render('Auth/UserList', [
        'users' => $users,
    ]);
    }

    public function create(): Response
    {
         return Inertia::render('Auth/Register', [
        'userTypes' => UserType::select('id', 'name')->where('show_global_form',1)->get()
    ]);
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|lowercase|email|max:255|unique:'.User::class,
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'user_type_id'=>'required'
        ]);
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'user_type_id' => $request->user_type_id,
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect(RouteServiceProvider::HOME);
    }
    public function update(Request $request, User $user)
{
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email',
        'status' => 'required',
        'password' => 'required',
    ]);

    $user->name = $validated['name'];
    $user->email = $validated['email'];
    $user->status = $validated['status'];
    // dd($validated['password']);

    if (!empty($validated['password'])) {
       $user->password = Hash::make($validated['password']);
    }

    $user->save();

    return redirect()->back()->with('success', 'User updated successfully.');
}
}
