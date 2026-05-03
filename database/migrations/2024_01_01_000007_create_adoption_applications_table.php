<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('adoption_applications', function (Blueprint $table) {
            $table->id();
            $table->string('application_number')->unique(); // PAWS-2024-00001
            $table->foreignId('pet_id')->constrained()->onDelete('restrict');
            $table->foreignId('adopter_id')->constrained('users')->onDelete('restrict');
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->onDelete('set null');

            // Applicant Information
            $table->string('applicant_full_name');
            $table->string('applicant_email');
            $table->string('applicant_phone');
            $table->text('applicant_address');
            $table->string('housing_type');         // house, apartment, condo
            $table->boolean('has_yard')->default(false);
            $table->boolean('has_other_pets')->default(false);
            $table->text('other_pets_details')->nullable();
            $table->boolean('has_children')->default(false);
            $table->string('children_ages')->nullable();
            $table->text('reason_for_adopting');
            $table->text('experience_with_pets')->nullable();
            $table->string('occupation')->nullable();
            $table->string('working_hours')->nullable();  // who cares for pet when away?
            $table->text('emergency_contact')->nullable();
            $table->text('additional_notes')->nullable();

            // Workflow
            $table->enum('status', [
                'draft',
                'submitted',
                'under_review',
                'interview_scheduled',
                'approved',
                'rejected',
                'withdrawn',
                'completed',
            ])->default('submitted');
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamp('interview_scheduled_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->text('review_notes')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['status', 'adopter_id']);
            $table->index('pet_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('adoption_applications');
    }
};