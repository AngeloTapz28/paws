<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE adoption_applications MODIFY status ENUM(
            'draft','submitted','under_review','interview_scheduled',
            'approved','rejected','withdrawn','completed','returned'
        ) DEFAULT 'submitted'");

        Schema::table('adoption_applications', function (Blueprint $table) {
            $table->text('return_reason')->nullable()->after('rejection_reason');
            $table->timestamp('returned_at')->nullable()->after('completed_at');
        });
    }

    public function down(): void
    {
        Schema::table('adoption_applications', function (Blueprint $table) {
            $table->dropColumn(['return_reason', 'returned_at']);
        });
    }
};