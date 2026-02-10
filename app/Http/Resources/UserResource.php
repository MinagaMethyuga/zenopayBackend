<?php

namespace App\Http\Resources;

use App\Services\XpService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * API-friendly user payload including gamification fields from user_profiles.
 */
class UserResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $xpService = app(XpService::class);

        $profile = $this->resource->profile;
        $xp = $profile ? (int) $profile->xp : 0;
        $level = $profile
            ? $xpService->levelNameFromInt((int) $profile->level)
            : $xpService->levelForXp($xp);

        return array_merge(parent::toArray($request), [
            'total_xp' => $xp,
            'level' => $level,
            'xp_to_next_level' => $xpService->xpToNextLevel($xp),
        ]);
    }
}
