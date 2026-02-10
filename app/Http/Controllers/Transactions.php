<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Services\XpService;
use App\Services\StreakService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

use App\Services\ChallengeProgressService; // ✅ ADD

class Transactions extends Controller
{
    public function store(Request $request, XpService $xpService, StreakService $streakService)
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

        DB::transaction(function () use ($user, $data, $xpService, &$tx, &$updatedWallet) {
            $amount = (float) $data['amount'];

            $delta = $data['type'] === 'expense' ? -$amount : $amount;

            $walletType = $data['payment_method'] === 'cash' ? 'cash' : 'bank';

            $wallet = DB::table('wallets')
                ->where('user_id', $user->id)
                ->where('type', $walletType)
                ->lockForUpdate()
                ->first();

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

            DB::table('wallets')
                ->where('id', $wallet->id)
                ->update([
                    'balance' => round($newBalance, 2),
                    'updated_at' => now(),
                ]);

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

            // Award XP exactly once per newly created transaction (atomic with the insert).
            $xpService->awardForNewTransaction($user);

            $updatedWallet = [
                'type' => $walletType,
                'balance' => round($newBalance, 2),
            ];
        });

        // ✅ STEP 9: Update challenge progress AFTER the transaction is committed
        if ($tx) {
            ChallengeProgressService::handleNewTransaction($tx);
            // And update the user's daily streak (login + transaction on same calendar day).
            $streakService->registerTransaction($user, $tx->occurred_at);
        }

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
