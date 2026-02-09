<?php

namespace App\Http\Controllers;

use App\Models\Challenge;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ChallengeController extends Controller
{
    public function index(Request $request)
    {
        $query = Challenge::query();

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

        if ($request->has('sort')) {
            $query->orderBy($request->sort, $request->get('direction', 'desc'));
        } else {
            $query->latest();
        }

        $challenges = $query->get();

        $stats = [
            'active_count' => Challenge::active()->count(),
            'total_xp' => Challenge::active()->sum('xp_reward'),
            'pending_review' => 5,
        ];

        return view('Challenges', compact('challenges', 'stats'));
    }

    public function show(Challenge $challenge)
    {
        return response()->json($challenge);
    }

    public function store(Request $request)
    {
        // Accept both UI lower-case enums and DB enums, then normalize.
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'difficulty' => 'required|in:Easy,Medium,Hard,Expert',
            'category' => 'required|in:Income,Savings,Budgeting,Investing,Learning',

            'frequency' => 'required|in:Daily,Weekly,Monthly,Quarterly,Yearly,One-Time,daily,weekly,monthly,quarterly,yearly,one_time',
            'xp_reward' => 'required|integer|min:0',

            'unlock_badge' => 'boolean',
            'badge_image' => 'nullable|required_if:unlock_badge,1|image|mimes:png|max:2048',

            'icon' => 'nullable|string',
            'target_type' => 'nullable|string',
            'target_value' => 'nullable|string',
            'duration' => 'nullable|string',
            'type' => 'nullable|in:regular,seasonal,event',
            'is_active' => 'boolean',
        ]);

        $validated['frequency'] = $this->normalizeFrequency($validated['frequency'] ?? null);

        // Build win_conditions from form fields (optional)
        $validated['win_conditions'] = $this->buildWinConditionsFromRequest($request);

        if (($validated['unlock_badge'] ?? false) && $request->hasFile('badge_image')) {
            $validated['badge_image'] = $request->file('badge_image')->store('badges', 'public');
        } else {
            $validated['badge_image'] = null;
        }

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
            'category' => 'in:Income,Savings,Budgeting,Investing,Learning',
            'frequency' => 'in:Daily,Weekly,Monthly,Quarterly,Yearly,One-Time,daily,weekly,monthly,quarterly,yearly,one_time',
            'xp_reward' => 'integer|min:0',

            'unlock_badge' => 'boolean',
            'badge_image' => 'nullable|image|mimes:png|max:2048',

            'icon' => 'nullable|string',
            'target_type' => 'nullable|string',
            'target_value' => 'nullable|string',
            'duration' => 'nullable|string',
            'type' => 'nullable|in:regular,seasonal,event',
            'is_active' => 'boolean',
        ]);

        $winConditions = $this->buildWinConditionsFromRequest($request);
        if ($winConditions !== null) {
            $validated['win_conditions'] = $winConditions;
        }

        if (array_key_exists('frequency', $validated)) {
            $validated['frequency'] = $this->normalizeFrequency($validated['frequency']);
        }

        // If unlock_badge turned OFF, delete old badge file + clear DB
        if (array_key_exists('unlock_badge', $validated) && !$validated['unlock_badge']) {
            if ($challenge->badge_image) {
                Storage::disk('public')->delete($challenge->badge_image);
            }
            $validated['badge_image'] = null;
        }

        // If unlock_badge is ON and new PNG uploaded, replace old
        $unlockBadge = array_key_exists('unlock_badge', $validated)
            ? (bool)$validated['unlock_badge']
            : (bool)$challenge->unlock_badge;

        if ($unlockBadge && $request->hasFile('badge_image')) {
            if ($challenge->badge_image) {
                Storage::disk('public')->delete($challenge->badge_image);
            }
            $validated['badge_image'] = $request->file('badge_image')->store('badges', 'public');
        }

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
        if ($challenge->badge_image) {
            Storage::disk('public')->delete($challenge->badge_image);
        }

        $challenge->delete();

        return response()->json([
            'success' => true,
            'message' => 'Challenge deleted successfully'
        ]);
    }

    private function normalizeFrequency(?string $frequency): ?string
    {
        if (!$frequency) return null;

        return match ($frequency) {
            'daily' => 'Daily',
            'weekly' => 'Weekly',
            'monthly' => 'Monthly',
            'quarterly' => 'Quarterly',
            'yearly' => 'Yearly',
            'one_time' => 'One-Time',
            default => $frequency,
        };
    }

    /**
     * Build win_conditions JSON from form inputs.
     * Expects: win_condition_transaction_type, win_condition_transaction_category,
     * win_condition_min_amount, win_condition_sum_amount (optional).
     */
    private function buildWinConditionsFromRequest(Request $request): ?array
    {
        $type = $request->input('win_condition_transaction_type');
        $category = $request->input('win_condition_transaction_category');

        if (($type === null || $type === '') && ($category === null || $category === '')) {
            return null;
        }

        $minAmount = $request->input('win_condition_min_amount');
        $sumAmount = $request->input('win_condition_sum_amount');

        return [
            'transaction_type' => in_array($type, ['income', 'expense'], true) ? $type : 'income',
            'transaction_category' => is_string($category) ? trim($category) : '',
            'min_amount' => is_numeric($minAmount) ? (float) $minAmount : 0,
            'sum_amount' => filter_var($sumAmount, FILTER_VALIDATE_BOOLEAN),
        ];
    }
}
