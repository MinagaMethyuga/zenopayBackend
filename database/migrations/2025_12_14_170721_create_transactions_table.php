<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();

            // When you later add auth (Sanctum), you can fill this automatically from the token.
            $table->unsignedBigInteger('user_id');

            $table->enum('type', ['income', 'expense']);
            $table->decimal('amount', 12, 2);

            // Customizable category (simple MVP). Later you can normalize into categories table.
            $table->string('category', 80);

            // Custom icons: store a key string from your Flutter icon set (e.g. "restaurant", "bus", "shopping_cart")
            $table->string('icon_key', 60)->nullable();

            // Notes (what you called description)
            $table->string('note', 500)->nullable();

            $table->enum('payment_method', [
                'cash',
                'card',
                'bank_transfer',
                'mobile_wallet',
                'cheque',
                'other',
            ])->default('cash');

            // Use datetime (better for charts & sorting, and SMS import later)
            $table->dateTime('occurred_at');

            // For future automation / imports (manual now, later "sms")
            $table->enum('source', ['manual', 'sms', 'import'])->default('manual');

            $table->timestamps();

            // Indexes
            $table->index(['user_id', 'occurred_at']);
            $table->index(['user_id', 'type', 'occurred_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
