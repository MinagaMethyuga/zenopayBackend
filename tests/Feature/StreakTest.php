<?php

use App\Models\User;
use App\Models\UserProfile;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->app['config']->set('app.timezone', 'UTC');
});

function createUserWithProfile(array $profileAttrs = []): User
{
    $user = User::create([
        'name' => 'Test User',
        'email' => fake()->unique()->safeEmail(),
        'password' => Hash::make('password'),
    ]);
    $user->profile()->create(array_merge([
        'onboarded' => true,
        'xp' => 0,
        'level' => 1,
        'current_streak' => 0,
        'best_streak' => 0,
        'last_activity_date' => null,
        'last_login_date' => null,
    ], $profileAttrs));
    return $user;
}

test('creating a transaction when last_activity_date was yesterday increments current_streak', function () {
    $user = createUserWithProfile([
        'current_streak' => 3,
        'best_streak' => 5,
        'last_activity_date' => Carbon::yesterday('UTC'),
        'last_login_date' => Carbon::today('UTC'),
    ]);

    $today = Carbon::today('UTC')->toDateString();

    $response = $this->actingAs($user)
        ->postJson('/api/transactions', [
            'type' => 'income',
            'amount' => 10,
            'category' => 'Food',
            'payment_method' => 'cash',
            'occurred_at' => $today . ' 12:00:00',
        ]);

    $response->assertStatus(201);

    $me = $this->actingAs($user)->getJson('/api/auth/me');
    $me->assertOk();
    $me->assertJsonPath('user.current_streak', 4);
    $me->assertJsonPath('user.best_streak', 5);

    $user->profile->refresh();
    expect((int) $user->profile->current_streak)->toBe(4);
    expect($user->profile->last_activity_date->toDateString())->toBe($today);
});

test('two transactions on the same day do not double-increment streak', function () {
    $user = createUserWithProfile([
        'current_streak' => 2,
        'best_streak' => 2,
        'last_activity_date' => Carbon::today('UTC'),
        'last_login_date' => Carbon::today('UTC'),
    ]);

    $today = Carbon::today('UTC')->toDateString();

    $this->actingAs($user)->postJson('/api/transactions', [
        'type' => 'income',
        'amount' => 100,
        'category' => 'Salary',
        'payment_method' => 'cash',
        'occurred_at' => $today . ' 10:00:00',
    ])->assertStatus(201);

    $this->actingAs($user)->postJson('/api/transactions', [
        'type' => 'expense',
        'amount' => 5,
        'category' => 'Food',
        'payment_method' => 'cash',
        'occurred_at' => $today . ' 18:00:00',
    ])->assertStatus(201);

    $me = $this->actingAs($user)->getJson('/api/auth/me');
    $me->assertOk();
    $me->assertJsonPath('user.current_streak', 2);
});

test('creating a transaction when last_activity_date was 3 days ago resets current_streak to 1', function () {
    $user = createUserWithProfile([
        'current_streak' => 7,
        'best_streak' => 7,
        'last_activity_date' => Carbon::today('UTC')->subDays(3),
        'last_login_date' => Carbon::today('UTC'),
    ]);

    $today = Carbon::today('UTC')->toDateString();

    $this->actingAs($user)
        ->postJson('/api/transactions', [
            'type' => 'income',
            'amount' => 15,
            'category' => 'Transport',
            'payment_method' => 'cash',
            'occurred_at' => $today . ' 09:00:00',
        ])
        ->assertStatus(201);

    $me = $this->actingAs($user)->getJson('/api/auth/me');
    $me->assertOk();
    $me->assertJsonPath('user.current_streak', 1);
    $me->assertJsonPath('user.best_streak', 7);

    $user->profile->refresh();
    expect((int) $user->profile->current_streak)->toBe(1);
    expect($user->profile->last_activity_date->toDateString())->toBe($today);
});

test('auth me returns current_streak and best_streak after transaction updates streak', function () {
    $user = createUserWithProfile([
        'current_streak' => 0,
        'best_streak' => 0,
        'last_activity_date' => null,
        'last_login_date' => Carbon::today('UTC'),
    ]);

    $before = $this->actingAs($user)->getJson('/api/auth/me');
    $before->assertJsonPath('user.current_streak', 0);
    $before->assertJsonPath('user.best_streak', 0);

    $today = Carbon::today('UTC')->toDateString();
    $this->actingAs($user)->postJson('/api/transactions', [
        'type' => 'income',
        'amount' => 100,
        'category' => 'Other',
        'payment_method' => 'cash',
        'occurred_at' => $today . ' 12:00:00',
    ])->assertStatus(201);

    $after = $this->actingAs($user)->getJson('/api/auth/me');
    $after->assertOk();
    $after->assertJsonPath('user.current_streak', 1);
    $after->assertJsonPath('user.best_streak', 1);
});
