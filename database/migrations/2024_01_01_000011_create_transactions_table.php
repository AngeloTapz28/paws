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
            $table->foreignId('payment_id')->constrained()->onDelete('cascade');
            $table->string('transaction_id')->nullable();    // external ref
            $table->string('gateway')->nullable();           // which payment gateway
            $table->decimal('amount', 10, 2);
            $table->enum('type', ['credit', 'debit', 'refund'])->default('credit');
            $table->enum('status', ['success', 'failed', 'pending'])->default('pending');
            $table->json('gateway_response')->nullable();    // raw gateway data
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};