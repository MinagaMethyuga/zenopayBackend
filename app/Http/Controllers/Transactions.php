<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

class Transactions extends Controller
{
    public function store(Request $request)
    {
        $user = $request->user(); // session auth

        $data = $request->validate([
            'type' => ['required', Rule::in(['income', 'expense'])],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'category' => ['required', 'string', 'max:80'],
            'icon_key' => ['nullable', 'string', 'max:60'],
            'note' => ['nullable', 'string', 'max:500'],

            'payment_method' => [
                'required',
                Rule::in(['cash', 'card', 'bank_transfer', 'mobile_wallet', 'cheque', 'other']),
            ],

            'occurred_at' => ['required', 'date'],
            'source' => ['sometimes', Rule::in(['manual', 'sms', 'import'])],
        ]);

        $tx = null;
        $updatedWallet = null;

        DB::transaction(function () use ($user, $data, &$tx, &$updatedWallet) {
            $amount = (float) $data['amount'];

            // Expense = subtract, Income = add
            $delta = $data['type'] === 'expense' ? -$amount : $amount;

            // Map payment_method -> wallet type (your app uses cash vs bank_transfer)
            $walletType = $data['payment_method'] === 'cash' ? 'cash' : 'bank';

            // Lock wallet row (avoid race conditions)
            $wallet = DB::table('wallets')
                ->where('user_id', $user->id)
                ->where('type', $walletType)
                ->lockForUpdate()
                ->first();

            // If wallet row doesn't exist yet, create it (safe default)
            if (!$wallet) {
                DB::table('wallets')->insert([
                    'user_id' => $user->id,
                    'type' => $walletType,
                    'name' => $walletType === 'cash' ? 'Cash' : 'Bank',
                    'bank_name' => null,
                    'card_last4' => null,
                    'balance' => 0,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                $wallet = DB::table('wallets')
                    ->where('user_id', $user->id)
                    ->where('type', $walletType)
                    ->lockForUpdate()
                    ->first();
            }

            $currentBalance = (float) ($wallet->balance ?? 0);
            $newBalance = $currentBalance + $delta;

            // Block negative (so your 422 makes sense)
            if ($newBalance < 0) {
                abort(response()->json([
                    'ok' => false,
                    'message' => "Not enough {$walletType} balance for this expense.",
                    'wallet' => [
                        'type' => $walletType,
                        'balance' => round($currentBalance, 2),
                    ],
                ], 422));
            }

            // Update wallet balance
            DB::table('wallets')
                ->where('id', $wallet->id)
                ->update([
                    'balance' => round($newBalance, 2),
                    'updated_at' => now(),
                ]);

            // Save transaction
            $tx = Transaction::create([
                'user_id' => $user->id,
                'type' => $data['type'],
                'amount' => $data['amount'],
                'category' => $data['category'],
                'icon_key' => $data['icon_key'] ?? null,
                'note' => $data['note'] ?? null,
                'payment_method' => $data['payment_method'],
                'occurred_at' => $data['occurred_at'],
                'source' => $data['source'] ?? 'manual',
            ]);

            $updatedWallet = [
                'type' => $walletType,
                'balance' => round($newBalance, 2),
            ];
        });

        return response()->json([
            'ok' => true,
            'message' => 'Transaction saved and wallet updated.',
            'transaction' => $tx,
            'wallet' => $updatedWallet,
        ], 201);
    }

    public function index(Request $request)
    {
        $user = $request->user();

        $filters = $request->validate([
            'type' => ['sometimes', Rule::in(['income', 'expense'])],
            'from' => ['sometimes', 'date'],
            'to' => ['sometimes', 'date'],
        ]);

        $q = Transaction::query()->where('user_id', $user->id);

        if (!empty($filters['type'])) $q->where('type', $filters['type']);
        if (!empty($filters['from'])) $q->where('occurred_at', '>=', $filters['from']);
        if (!empty($filters['to'])) $q->where('occurred_at', '<=', $filters['to']);

        $items = $q->orderByDesc('occurred_at')->limit(200)->get();

        return response()->json([
            'ok' => true,
            'transactions' => $items,
        ]);
    }
}
