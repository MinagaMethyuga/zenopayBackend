<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Challenge extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'difficulty',
        'category',
        'frequency',
        'xp_reward',
        'unlock_badge',
        'badge_image',
        'icon',
        'target_type',
        'target_value',
        'duration',
        'type',
        'is_active',
        'win_conditions'
    ];

    protected $appends = [
        'badge_image_url',
    ];

    protected $casts = [
        'unlock_badge' => 'boolean',
        'is_active' => 'boolean',
        'win_conditions' => 'array',
        'xp_reward' => 'integer'
    ];

    public function getBadgeImageUrlAttribute(): ?string
    {
        return $this->badge_image ? Storage::url($this->badge_image) : null;
    }

    public function getDifficultyColorAttribute()
    {
        return match($this->difficulty) {
            'Easy' => 'emerald',
            'Medium' => 'yellow',
            'Hard' => 'red',
            'Expert' => 'purple',
            default => 'gray'
        };
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeInactive($query)
    {
        return $query->where('is_active', false);
    }
}
