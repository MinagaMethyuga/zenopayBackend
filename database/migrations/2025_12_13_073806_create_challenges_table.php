<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('challenges', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description');
            $table->enum('difficulty', ['Easy', 'Medium', 'Hard', 'Expert'])->default('Easy');
            $table->enum('category', ['Income','Savings', 'Budgeting', 'Investing', 'Learning'])->default('Income');
            $table->enum('frequency', ['Daily', 'Weekly', 'Monthly', 'Quarterly', 'Yearly', 'One-Time'])->default('Weekly');
            $table->integer('xp_reward')->default(0);
            $table->boolean('unlock_badge')->default(false);
            $table->string('icon')->nullable();
            $table->string('target_type')->nullable(); // 'amount', 'actions', 'time'
            $table->string('target_value')->nullable(); // '500 LKR', '10 Actions', '7 Days'
            $table->string('duration')->nullable();
            $table->enum('type', ['regular', 'seasonal', 'event'])->default('regular');
            $table->boolean('is_active')->default(true);
            $table->json('win_conditions')->nullable();
            $table->timestamp('starts_at')->nullable(); // When challenge becomes available
            $table->timestamp('ends_at')->nullable(); // When challenge expires
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('challenges');
    }
};
