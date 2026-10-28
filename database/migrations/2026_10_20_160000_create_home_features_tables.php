<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('home_sections', function (Blueprint $table) {
            $table->id();
            $table->string('key', 40)->unique();
            $table->string('title', 120);
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('home_features', function (Blueprint $table) {
            $table->id();
            $table->unsignedTinyInteger('slot')->unique();
            $table->string('image', 2000);
            $table->string('title', 80);
            $table->string('description', 300);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('home_features');
        Schema::dropIfExists('home_sections');
    }
};
