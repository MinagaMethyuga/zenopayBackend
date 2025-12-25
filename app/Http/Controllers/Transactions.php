<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Exception;

class Transactions extends Controller
{
    public function Income(Request $request)
    {
        try {
            $validated = $request->validate([
                'userid' => 'required|integer',
                'amount' => 'required|numeric|min:0.01',
                'category' => 'required|string|max:255',
                'payment_method' => 'required|in:cash,card,bank_transfer,mobile_wallet,cheque,other',
                'date' => 'required|date',
                'description' => 'nullable|string|max:500',
            ]);

            // Insert into database
            DB::table('transactions')->insert([
                'user_id' => $validated['userid'],
                'type' => 'income',
                'amount' => $validated['amount'],
                'category' => $validated['category'],
                'payment_method' => $validated['payment_method'],
                'date' => $validated['date'],
                'description' => $validated['description'] ?? null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Income transaction recorded successfully'
            ], 200);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to record income transaction',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function Expense(Request $request)
    {
        try {
            $validated = $request->validate([
                'userid' => 'required|integer',
                'amount' => 'required|numeric|min:0.01',
                'category' => 'required|string|max:255',
                'payment_method' => 'required|in:cash,card,bank_transfer,mobile_wallet,cheque,other',
                'date' => 'required|date',
                'description' => 'nullable|string|max:500',
            ]);

            // Insert into database
            DB::table('transactions')->insert([
                'user_id' => $validated['userid'],
                'type' => 'expense',
                'amount' => $validated['amount'],
                'category' => $validated['category'],
                'payment_method' => $validated['payment_method'],
                'date' => $validated['date'],
                'description' => $validated['description'] ?? null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Expense transaction recorded successfully'
            ], 200);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to record expense transaction',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    
}
