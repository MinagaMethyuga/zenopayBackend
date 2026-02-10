<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UserProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'onboarded',
        'xp',
        'level',
        'current_streak',
        'best_streak',
        'last_activity_date',
        'last_login_date',
    ];

    protected $casts = [
        'onboarded' => 'boolean',
        'last_activity_date' => 'date',
        'last_login_date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
