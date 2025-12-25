<?php

namespace App\Http\Controllers;

use App\Models\Challenge;
use Illuminate\Http\Request;

class ChallengeController extends Controller
{
    public function index(Request $request)
    {
        $query = Challenge::query();

        // Filter by status
        if ($request->has('filter')) {
            switch ($request->filter) {
                case 'active':
                    $query->active();
                    break;
                case 'inactive':
                    $query->inactive();
                    break;
            }
        }

        // Sort
        if ($request->has('sort')) {
            $query->orderBy($request->sort, $request->get('direction', 'desc'));
        } else {
            $query->latest();
        }

        $challenges = $query->get();

        // Calculate stats
        $stats = [
            'active_count' => Challenge::active()->count(),
            'total_xp' => Challenge::active()->sum('xp_reward'),
            'pending_review' => 5, // This would come from a review system
        ];

        return view('Challenges', compact('challenges', 'stats'));
    }

    public function show(Challenge $challenge)
    {
        return response()->json($challenge);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'difficulty' => 'required|in:Easy,Medium,Hard,Expert',
            'category' => 'required|in:Savings,Budgeting,Investing,Learning',
            'frequency' => 'required|in:Daily,Weekly,Monthly,Quarterly,Yearly,One-Time',
            'xp_reward' => 'required|integer|min:0',
            'unlock_badge' => 'boolean',
            'icon' => 'nullable|string',
            'target_type' => 'nullable|string',
            'target_value' => 'nullable|string',
            'duration' => 'nullable|string',
            'type' => 'nullable|in:regular,seasonal,event',
            'is_active' => 'boolean',
            'win_conditions' => 'nullable|array'
        ]);

        $challenge = Challenge::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Challenge created successfully',
            'challenge' => $challenge
        ]);
    }

    public function update(Request $request, Challenge $challenge)
    {
        $validated = $request->validate([
            'name' => 'string|max:255',
            'description' => 'string',
            'difficulty' => 'in:Easy,Medium,Hard,Expert',
            'category' => 'in:Savings,Budgeting,Investing,Learning',
            'frequency' => 'in:Daily,Weekly,Monthly,Quarterly,Yearly,One-Time',
            'xp_reward' => 'integer|min:0',
            'unlock_badge' => 'boolean',
            'icon' => 'nullable|string',
            'target_type' => 'nullable|string',
            'target_value' => 'nullable|string',
            'duration' => 'nullable|string',
            'type' => 'nullable|in:regular,seasonal,event',
            'is_active' => 'boolean',
            'win_conditions' => 'nullable|array'
        ]);

        $challenge->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Challenge updated successfully',
            'challenge' => $challenge
        ]);
    }

    public function toggleStatus(Challenge $challenge)
    {
        $challenge->update(['is_active' => !$challenge->is_active]);

        return response()->json([
            'success' => true,
            'message' => 'Challenge status updated',
            'is_active' => $challenge->is_active
        ]);
    }

    public function destroy(Challenge $challenge)
    {
        $challenge->delete();

        return response()->json([
            'success' => true,
            'message' => 'Challenge deleted successfully'
        ]);
    }
}
