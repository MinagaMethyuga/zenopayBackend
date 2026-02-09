<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('user_badges', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('challenge_id')->constrained()->cascadeOnDelete();

            $table->string('badge_image')->nullable(); // store path (same as challenges.badge_image)
            $table->timestamp('unlocked_at')->nullable();

            $table->timestamps();

            $table->unique(['user_id', 'challenge_id']); // one badge per challenge per user
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_badges');
    }
};
