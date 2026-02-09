<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

// ✅ Only import models that ACTUALLY exist
use App\Models\UserProfile;
use App\Models\Wallet;
use App\Models\Challenge;
use App\Models\Transaction;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'is_active',
        'provider',
        'provider_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships (ONLY what exists now)
    |--------------------------------------------------------------------------
    */

    // Gamification + onboarding
    public function profile()
    {
        return $this->hasOne(UserProfile::class);
    }

    // Cash / Bank wallets
    public function wallets()
    {
        return $this->hasMany(Wallet::class);
    }

    // Challenges user has (later we can pivot this)
    public function challenges()
    {
        return $this->hasMany(Challenge::class);
    }

    // Transactions made by user
    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }
}
