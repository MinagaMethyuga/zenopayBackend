<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
        'icon',
        'target_type',
        'target_value',
        'duration',
        'type',
        'is_active',
        'win_conditions'
    ];

    protected $casts = [
        'unlock_badge' => 'boolean',
        'is_active' => 'boolean',
        'win_conditions' => 'array',
        'xp_reward' => 'integer'
    ];

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
