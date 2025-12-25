<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->enum('type', ['income', 'expense']);
            $table->decimal('amount', 10, 2);
            $table->string('category');
            $table->enum('payment_method', ['cash', 'card', 'bank_transfer', 'mobile_wallet', 'cheque', 'other'])->default('cash');
            $table->date('date');
            $table->text('description')->nullable();
            $table->timestamps();

            // Optional: Add foreign key if you have a users table
            // $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            // Add indexes for better query performance
            $table->index('user_id');
            $table->index('type');
            $table->index('date');
        });
    }

    public function down()
    {
        Schema::dropIfExists('transactions');
    }
};
