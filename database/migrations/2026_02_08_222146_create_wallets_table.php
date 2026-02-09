<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('wallets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            $table->enum('type', ['cash', 'bank']);

            $table->string('name'); // "Cash", "BOC Card", etc.
            $table->string('bank_name')->nullable();
            $table->string('card_last4')->nullable();

            $table->decimal('balance', 12, 2)->default(0);

            $table->timestamps();

            $table->unique(['user_id', 'type']); // 1 cash + 1 bank by default
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wallets');
    }
};
