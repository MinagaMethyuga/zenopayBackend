<?php

namespace Database\Seeders;

use App\Models\Challenge;
use Illuminate\Database\Seeder;

class ChallengeSeeder extends Seeder
{
    public function run(): void
    {
        $challenges = [
            // Daily Challenges
            [
                'name' => 'Coffee Saver',
                'description' => 'Spend less than $5 on coffee today.',
                'difficulty' => 'Easy',
                'category' => 'Budgeting',
                'frequency' => 'Daily',
                'xp_reward' => 50,
                'unlock_badge' => false,
                'icon' => '☕',
                'target_value' => '$5 spent',
                'duration' => '1 Day',
                'type' => 'regular',
                'is_active' => true,
            ],
            [
                'name' => 'Walk to Campus',
                'description' => 'Save transportation costs by walking to campus today.',
                'difficulty' => 'Easy',
                'category' => 'Savings',
                'frequency' => 'Daily',
                'xp_reward' => 50,
                'unlock_badge' => false,
                'icon' => '🚶',
                'target_value' => 'Walk instead',
                'duration' => '1 Day',
                'type' => 'regular',
                'is_active' => true,
            ],
            [
                'name' => 'Pack Your Lunch',
                'description' => 'Bring lunch from home instead of eating out.',
                'difficulty' => 'Easy',
                'category' => 'Budgeting',
                'frequency' => 'Daily',
                'xp_reward' => 75,
                'unlock_badge' => false,
                'icon' => '🍱',
                'target_value' => 'Home lunch',
                'duration' => '1 Day',
                'type' => 'regular',
                'is_active' => true,
            ],

            // Weekly Challenges
            [
                'name' => 'No-Takeout Week',
                'description' => 'Complete a full week without ordering takeout or delivery.',
                'difficulty' => 'Hard',
                'category' => 'Budgeting',
                'frequency' => 'Weekly',
                'xp_reward' => 500,
                'unlock_badge' => true,
                'icon' => '🍽️',
                'target_value' => '$0 spent',
                'duration' => '7 Days',
                'type' => 'regular',
                'is_active' => true,
            ],
            [
                'name' => 'Weekly Savings Goal',
                'description' => 'Save at least $50 this week.',
                'difficulty' => 'Medium',
                'category' => 'Savings',
                'frequency' => 'Weekly',
                'xp_reward' => 300,
                'unlock_badge' => false,
                'icon' => '💰',
                'target_value' => '$50 saved',
                'duration' => '7 Days',
                'type' => 'regular',
                'is_active' => true,
            ],

            // Monthly Challenges
            [
                'name' => 'Textbook Saver',
                'description' => 'Save $100 towards textbooks this month.',
                'difficulty' => 'Medium',
                'category' => 'Savings',
                'frequency' => 'Monthly',
                'xp_reward' => 200,
                'unlock_badge' => false,
                'icon' => '📚',
                'target_value' => '$45 / $100',
                'duration' => '30 Days',
                'type' => 'regular',
                'is_active' => true,
            ],
            [
                'name' => 'Budget Master',
                'description' => 'Track all your expenses for 30 consecutive days.',
                'difficulty' => 'Hard',
                'category' => 'Budgeting',
                'frequency' => 'Monthly',
                'xp_reward' => 800,
                'unlock_badge' => true,
                'icon' => '📊',
                'target_value' => '30 Days',
                'duration' => '1 Month',
                'type' => 'regular',
                'is_active' => true,
            ],
            [
                'name' => 'Emergency Fund Builder',
                'description' => 'Build your emergency fund to $500.',
                'difficulty' => 'Expert',
                'category' => 'Savings',
                'frequency' => 'Monthly',
                'xp_reward' => 1000,
                'unlock_badge' => true,
                'icon' => '🛡️',
                'target_value' => '$500 saved',
                'duration' => '1 Month',
                'type' => 'regular',
                'is_active' => true,
            ],

            // Adaptive/Learning Challenges
            [
                'name' => 'Coffee Detox',
                'description' => 'Based on your spending history, cutting coffee spend by 10% this month could save you $40.',
                'difficulty' => 'Medium',
                'category' => 'Learning',
                'frequency' => 'Monthly',
                'xp_reward' => 350,
                'unlock_badge' => false,
                'icon' => '☕',
                'target_value' => '10% reduction',
                'duration' => '30 Days',
                'type' => 'regular',
                'is_active' => true,
            ],
            [
                'name' => 'Financial Literacy Course',
                'description' => 'Complete the introduction to investing course.',
                'difficulty' => 'Medium',
                'category' => 'Learning',
                'frequency' => 'One-Time',
                'xp_reward' => 500,
                'unlock_badge' => true,
                'icon' => '🎓',
                'target_value' => '5 modules',
                'duration' => 'Self-paced',
                'type' => 'regular',
                'is_active' => true,
            ],
            [
                'name' => 'First Investment',
                'description' => 'Make your first investment of at least $100.',
                'difficulty' => 'Hard',
                'category' => 'Investing',
                'frequency' => 'One-Time',
                'xp_reward' => 1000,
                'unlock_badge' => true,
                'icon' => '📈',
                'target_value' => '$100 invested',
                'duration' => 'Anytime',
                'type' => 'regular',
                'is_active' => true,
            ],

            // Income Challenges
            [
                'name' => 'Side Hustle Starter',
                'description' => 'Earn an extra $200 from a side project or gig.',
                'difficulty' => 'Expert',
                'category' => 'Income',
                'frequency' => 'Monthly',
                'xp_reward' => 1500,
                'unlock_badge' => true,
                'icon' => '💼',
                'target_value' => '$200 earned',
                'duration' => '30 Days',
                'type' => 'regular',
                'is_active' => true,
            ],
        ];

        foreach ($challenges as $challenge) {
            Challenge::create($challenge);
        }
    }
}
