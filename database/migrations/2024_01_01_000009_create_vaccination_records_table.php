<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vaccination_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pet_id')->constrained()->onDelete('cascade');
            $table->foreignId('administered_by')->constrained('users')->onDelete('restrict');
            $table->string('vaccine_name');
            $table->string('manufacturer')->nullable();
            $table->string('batch_number')->nullable();
            $table->date('date_administered');
            $table->date('next_due_date')->nullable();
            $table->boolean('is_booster')->default(false);
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vaccination_records');
    }
};