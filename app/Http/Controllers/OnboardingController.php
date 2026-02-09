<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Wallet;

class OnboardingController extends Controller
{
    public function store(Request $request)
    {
        // If you're using session auth, user must be logged in
        $user = auth()->user();
        if (!$user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $data = $request->validate([
            'cash_balance' => ['required', 'numeric', 'min:0'],
            'bank_name'    => ['required', 'string', 'max:255'],
            'bank_balance' => ['required', 'numeric', 'min:0'],
        ]);

        $cash = Wallet::updateOrCreate(
            ['user_id' => $user->id, 'type' => 'cash'],
            ['name' => 'Cash', 'balance' => $data['cash_balance']]
        );

        $bank = Wallet::updateOrCreate(
            ['user_id' => $user->id, 'type' => 'bank'],
            [
                'name'      => $data['bank_name'] . ' Card',
                'bank_name' => $data['bank_name'],
                'balance'   => $data['bank_balance'],
            ]
        );

        return response()->json([
            'message' => 'Onboarding saved',
            'cash' => $cash,
            'bank' => $bank,
        ], 200);
    }
}
