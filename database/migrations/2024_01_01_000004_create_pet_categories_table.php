<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pet_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();        // Dog, Cat, Bird, Rabbit, etc.
            $table->string('icon', 10)->nullable();  // emoji or icon class
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pet_categories');
    }
};