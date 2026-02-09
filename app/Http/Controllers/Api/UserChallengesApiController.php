<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\UserChallenge;
use Illuminate\Http\Request;

class UserChallengesApiController extends Controller
{
    // GET /api/my-challenges?status=active|completed
    public function index(Request $request)
    {
        $user = $request->user();
        if (!$user) return response()->json(['error' => 'Unauthenticated'], 401);

        $status = $request->query('status');

        $q = UserChallenge::with('challenge')
            ->where('user_id', $user->id);

        if (in_array($status, ['active', 'completed'], true)) {
            $q->where('status', $status);
        }

        $rows = $q->orderByDesc('updated_at')->get();

        $data = $rows->map(function ($uc) {
            $c = $uc->challenge;
            if (!$c) return null; // safety

            return [
                'user_challenge' => [
                    'id' => $uc->id,
                    'status' => $uc->status,
                    'progress' => (float) $uc->progress,
                    'accepted_at' => optional($uc->accepted_at)->toIso8601String(),
                    'completed_at' => optional($uc->completed_at)->toIso8601String(),
                ],
                'challenge' => [
                    'id' => $c->id,
                    'name' => $c->name,
                    'description' => $c->description,
                    'difficulty' => $c->difficulty,
                    'category' => $c->category,
                    'frequency' => $c->frequency,
                    'xp_reward' => (int) $c->xp_reward,
                    'unlock_badge' => (bool) $c->unlock_badge,
                    'badge_image_url' => $c->badge_image_url,
                    'icon' => $c->icon ?? '🎯',
                    'target_type' => $c->target_type,
                    'target_value' => $c->target_value,
                    'duration' => $c->duration,
                    'type' => $c->type,
                    'is_active' => (bool) $c->is_active,
                    'win_conditions' => $c->win_conditions,
                ],
            ];
        })->filter()->values();

        return response()->json($data);
    }
}
