<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('medical_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pet_id')->constrained()->onDelete('cascade');
            $table->foreignId('vet_id')->constrained('users')->onDelete('restrict');
            $table->date('examination_date');
            $table->string('diagnosis')->nullable();
            $table->text('symptoms')->nullable();
            $table->text('treatment')->nullable();
            $table->text('medications')->nullable();
            $table->decimal('weight_at_exam', 5, 2)->nullable();
            $table->enum('health_status', [
                'excellent', 'good', 'fair', 'poor', 'critical'
            ])->default('good');
            $table->boolean('fit_for_adoption')->default(true);
            $table->text('notes')->nullable();
            $table->string('attachment')->nullable();    // lab results, etc.
            $table->date('next_checkup_date')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('medical_records');
    }
};