<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->string('reference_number')->unique();  // PAY-2024-00001
            $table->foreignId('adoption_application_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('payer_id')->constrained('users')->onDelete('restrict');
            $table->foreignId('recorded_by')->nullable()->constrained('users')->onDelete('set null');
            $table->enum('type', [
                'adoption_fee', 'donation', 'medical_fee', 'other'
            ])->default('adoption_fee');
            $table->decimal('amount', 10, 2);
            $table->string('currency', 3)->default('PHP');
            $table->enum('method', [
                'cash', 'bank_transfer', 'gcash', 'maya', 'credit_card', 'check', 'other'
            ])->default('cash');
            $table->enum('status', [
                'pending', 'completed', 'failed', 'refunded', 'cancelled'
            ])->default('pending');
            $table->string('proof_of_payment')->nullable();  // uploaded file
            $table->text('notes')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['status', 'type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};