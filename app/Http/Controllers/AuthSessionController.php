<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthSessionController extends Controller
{
    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:6'],
        ]);

        $user = User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        // ✅ create profile row for gamification + onboarding flag
        $user->profile()->create([
            'onboarded' => false,
            'xp' => 0,
            'level' => 1,
            'current_streak' => 0,
            'best_streak' => 0,
            'last_activity_date' => null,
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        return response()->json([
            'ok' => true,
            'user' => new UserResource($user->load('profile')),
        ], 201);
    }

    public function login(Request $request)
    {
        $data = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (!Auth::attempt($data)) {
            return response()->json(['message' => 'Invalid email or password'], 401);
        }

        $request->session()->regenerate();

        return response()->json([
            'ok' => true,
            'user' => new UserResource($request->user()->load('profile')),
        ]);
    }

    public function me(Request $request)
    {
        if (!$request->user()) {
            return response()->json(['user' => null], 401);
        }

        return response()->json([
            'user' => new UserResource($request->user()->load(['profile', 'wallets'])),
        ]);
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->json(['ok' => true]);
    }
}
