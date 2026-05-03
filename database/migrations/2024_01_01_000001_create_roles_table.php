<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();           // admin, staff, adopter, vet
            $table->string('display_name');             // Admin, Staff, Adopter, Vet
            $table->text('description')->nullable();
            $table->string('color', 20)->default('secondary'); // badge color
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('roles');
    }
};