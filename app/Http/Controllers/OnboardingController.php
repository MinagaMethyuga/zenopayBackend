<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class OnboardingController extends Controller
{
    public function store(Request $request)
    {
        // Must be logged in via session cookie
        $user = $request->user();
        if (!$user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $data = $request->validate([
            'cash_balance' => ['required', 'numeric', 'min:0'],
            'bank_name'    => ['required', 'string', 'max:80'],
            'bank_balance' => ['required', 'numeric', 'min:0'],

            // optional extras (safe)
            'card_name'  => ['nullable', 'string', 'max:40'],
            'card_last4' => ['nullable', 'string', 'size:4'],
        ]);

        // ✅ Create/Update Cash wallet
        $user->wallets()->updateOrCreate(
            ['type' => 'cash'],
            [
                'name' => 'Cash',
                'balance' => $data['cash_balance'],
                'bank_name' => null,
                'card_last4' => null,
            ]
        );

        // ✅ Create/Update Bank wallet
        $user->wallets()->updateOrCreate(
            ['type' => 'bank'],
            [
                'name' => $data['card_name'] ?? $data['bank_name'],
                'bank_name' => $data['bank_name'],
                'card_last4' => $data['card_last4'] ?? null,
                'balance' => $data['bank_balance'],
            ]
        );

        // ✅ Mark onboarded
        $user->profile()->updateOrCreate(
            ['user_id' => $user->id],
            ['onboarded' => true]
        );

        return response()->json([
            'ok' => true,
            'user' => $user->load(['profile', 'wallets']),
        ]);
    }
}
