<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pets', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('pet_category_id')->constrained()->onDelete('restrict');
            $table->foreignId('breed_id')->nullable()->constrained()->onDelete('set null');
            $table->enum('gender', ['male', 'female', 'unknown'])->default('unknown');
            $table->date('date_of_birth')->nullable();
            $table->decimal('weight', 5, 2)->nullable();  // kg
            $table->enum('size', ['tiny', 'small', 'medium', 'large', 'extra_large'])->nullable();
            $table->string('color')->nullable();
            $table->text('description')->nullable();
            $table->text('special_needs')->nullable();
            $table->boolean('is_vaccinated')->default(false);
            $table->boolean('is_neutered')->default(false);
            $table->boolean('is_microchipped')->default(false);
            $table->enum('status', [
                'available',
                'pending',
                'adopted',
                'under_treatment',
                'not_available',
                'quarantine',
            ])->default('available');
            $table->enum('adoption_fee_type', ['fixed', 'donation', 'free'])->default('fixed');
            $table->decimal('adoption_fee', 8, 2)->default(0.00);
            $table->string('primary_image')->nullable();
            $table->json('images')->nullable();           // additional images array
            $table->foreignId('added_by')->constrained('users')->onDelete('restrict');
            $table->foreignId('vet_approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->boolean('is_vet_approved')->default(false);
            $table->boolean('is_admin_approved')->default(false);
            $table->timestamp('listed_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['status', 'is_admin_approved']);
            $table->index('pet_category_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pets');
    }
};