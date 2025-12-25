<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Challenge;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ChallengesApiController extends Controller
{
    /**
     * Get all active challenges with filtering
     */
    public function index(Request $request): JsonResponse
    {
        $query = Challenge::query();

        // Filter by status
        if ($request->has('filter')) {
            $filter = $request->get('filter');
            if ($filter === 'active') {
                $query->where('is_active', true);
            } elseif ($filter === 'inactive') {
                $query->where('is_active', false);
            }
        }

        // Filter by frequency
        if ($request->has('frequency')) {
            $query->where('frequency', $request->get('frequency'));
        }

        // Filter by category
        if ($request->has('category')) {
            $query->where('category', $request->get('category'));
        }

        // Filter by difficulty
        if ($request->has('difficulty')) {
            $query->where('difficulty', $request->get('difficulty'));
        }

        // Only get challenges that are currently available
        $query->where(function($q) {
            $q->whereNull('starts_at')
                ->orWhere('starts_at', '<=', now());
        })
            ->where(function($q) {
                $q->whereNull('ends_at')
                    ->orWhere('ends_at', '>=', now());
            });

        $challenges = $query->orderBy('created_at', 'desc')->get();

        // Transform challenges for mobile app
        $transformedChallenges = $challenges->map(function ($challenge) {
            return [
                'id' => $challenge->id,
                'name' => $challenge->name,
                'description' => $challenge->description,
                'difficulty' => $challenge->difficulty,
                'category' => $challenge->category,
                'frequency' => $challenge->frequency,
                'xp_reward' => $challenge->xp_reward,
                'unlock_badge' => $challenge->unlock_badge,
                'icon' => $challenge->icon ?? '🎯',
                'target_type' => $challenge->target_type,
                'target_value' => $challenge->target_value,
                'duration' => $challenge->duration,
                'type' => $challenge->type,
                'is_active' => $challenge->is_active,
                'win_conditions' => $challenge->win_conditions,
                'starts_at' => $challenge->starts_at?->toIso8601String(),
                'ends_at' => $challenge->ends_at?->toIso8601String(),
                'created_at' => $challenge->created_at->toIso8601String(),
                'updated_at' => $challenge->updated_at->toIso8601String(),
            ];
        });

        return response()->json($transformedChallenges);
    }

    /**
     * Get a single challenge by ID
     */
    public function show(int $id): JsonResponse
    {
        $challenge = Challenge::find($id);

        if (!$challenge) {
            return response()->json([
                'error' => 'Challenge not found'
            ], 404);
        }

        return response()->json([
            'id' => $challenge->id,
            'name' => $challenge->name,
            'description' => $challenge->description,
            'difficulty' => $challenge->difficulty,
            'category' => $challenge->category,
            'frequency' => $challenge->frequency,
            'xp_reward' => $challenge->xp_reward,
            'unlock_badge' => $challenge->unlock_badge,
            'icon' => $challenge->icon ?? '🎯',
            'target_type' => $challenge->target_type,
            'target_value' => $challenge->target_value,
            'duration' => $challenge->duration,
            'type' => $challenge->type,
            'is_active' => $challenge->is_active,
            'win_conditions' => $challenge->win_conditions,
            'starts_at' => $challenge->starts_at?->toIso8601String(),
            'ends_at' => $challenge->ends_at?->toIso8601String(),
            'created_at' => $challenge->created_at->toIso8601String(),
            'updated_at' => $challenge->updated_at->toIso8601String(),
        ]);
    }

    /**
     * Get daily challenges
     */
    public function daily(): JsonResponse
    {
        $challenges = Challenge::where('is_active', true)
            ->where('frequency', 'Daily')
            ->where(function($q) {
                $q->whereNull('starts_at')
                    ->orWhere('starts_at', '<=', now());
            })
            ->where(function($q) {
                $q->whereNull('ends_at')
                    ->orWhere('ends_at', '>=', now());
            })
            ->orderBy('xp_reward', 'desc')
            ->limit(5)
            ->get();

        $transformedChallenges = $challenges->map(function ($challenge) {
            return [
                'id' => $challenge->id,
                'name' => $challenge->name,
                'description' => $challenge->description,
                'difficulty' => $challenge->difficulty,
                'category' => $challenge->category,
                'frequency' => $challenge->frequency,
                'xp_reward' => $challenge->xp_reward,
                'unlock_badge' => $challenge->unlock_badge,
                'icon' => $challenge->icon ?? '🎯',
                'target_type' => $challenge->target_type,
                'target_value' => $challenge->target_value,
                'duration' => $challenge->duration,
                'type' => $challenge->type,
                'is_active' => $challenge->is_active,
                'win_conditions' => $challenge->win_conditions,
                'starts_at' => $challenge->starts_at?->toIso8601String(),
                'ends_at' => $challenge->ends_at?->toIso8601String(),
                'created_at' => $challenge->created_at->toIso8601String(),
                'updated_at' => $challenge->updated_at->toIso8601String(),
            ];
        });

        return response()->json($transformedChallenges);
    }

    /**
     * Get statistics for the challenges page
     */
    public function stats(): JsonResponse
    {
        $activeCount = Challenge::where('is_active', true)->count();
        $totalXP = Challenge::where('is_active', true)->sum('xp_reward');
        $challengesByFrequency = Challenge::where('is_active', true)
            ->selectRaw('frequency, count(*) as count')
            ->groupBy('frequency')
            ->get();

        return response()->json([
            'active_count' => $activeCount,
            'total_xp' => $totalXP,
            'by_frequency' => $challengesByFrequency,
            'total_challenges' => Challenge::count(),
        ]);
    }

    /**
     * Accept a challenge (for user progress tracking)
     */
    public function accept(Request $request, int $id): JsonResponse
    {
        $challenge = Challenge::find($id);

        if (!$challenge) {
            return response()->json([
                'error' => 'Challenge not found'
            ], 404);
        }

        if (!$challenge->is_active) {
            return response()->json([
                'error' => 'Challenge is not active'
            ], 400);
        }

        // Here you would typically create a UserChallenge record
        // to track the user's progress on this challenge

        return response()->json([
            'success' => true,
            'message' => 'Challenge accepted successfully',
            'challenge' => [
                'id' => $challenge->id,
                'name' => $challenge->name,
                'xp_reward' => $challenge->xp_reward,
            ]
        ]);
    }
}
