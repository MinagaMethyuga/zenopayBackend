<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class Transactions extends Controller
{
    /**
     * Create a transaction (income or expense).
     * Recommended endpoint: POST /api/transactions
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            // Keep user_id for now (since you haven't wired auth yet).
            // Later: replace with $request->user()->id using Sanctum.
            'user_id' => ['required', 'integer', 'min:1'],

            'type' => ['required', Rule::in(['income', 'expense'])],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'category' => ['required', 'string', 'max:80'],
            'icon_key' => ['nullable', 'string', 'max:60'],
            'note' => ['nullable', 'string', 'max:500'],

            'payment_method' => [
                'required',
                Rule::in(['cash', 'card', 'bank_transfer', 'mobile_wallet', 'cheque', 'other']),
            ],

            // Flutter can send ISO string: "2026-02-03 14:25:00" or "2026-02-03T14:25:00"
            'occurred_at' => ['required', 'date'],

            'source' => ['sometimes', Rule::in(['manual', 'sms', 'import'])],
        ]);

        $tx = Transaction::create([
            'user_id' => $data['user_id'],
            'type' => $data['type'],
            'amount' => $data['amount'],
            'category' => $data['category'],
            'icon_key' => $data['icon_key'] ?? null,
            'note' => $data['note'] ?? null,
            'payment_method' => $data['payment_method'],
            'occurred_at' => $data['occurred_at'],
            'source' => $data['source'] ?? 'manual',
        ]);

        return response()->json([
            'ok' => true,
            'message' => 'Transaction saved.',
            'transaction' => $tx,
        ], 201);
    }

    /**
     * List transactions for a user.
     * GET /api/transactions?user_id=1&type=expense&from=2026-02-01&to=2026-02-28
     */
    public function index(Request $request)
    {
        $filters = $request->validate([
            'user_id' => ['required', 'integer', 'min:1'],
            'type' => ['sometimes', Rule::in(['income', 'expense'])],
            'from' => ['sometimes', 'date'],
            'to' => ['sometimes', 'date'],
        ]);

        $q = Transaction::query()
            ->where('user_id', $filters['user_id']);

        if (!empty($filters['type'])) {
            $q->where('type', $filters['type']);
        }

        if (!empty($filters['from'])) {
            $q->where('occurred_at', '>=', $filters['from']);
        }

        if (!empty($filters['to'])) {
            $q->where('occurred_at', '<=', $filters['to']);
        }

        $items = $q->orderByDesc('occurred_at')
            ->limit(200)
            ->get();

        return response()->json([
            'ok' => true,
            'transactions' => $items,
        ]);
    }
}
