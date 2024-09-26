<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('films', function (Blueprint $table) {
            $table->id();
            $table->string('uuid');
            $table->string('name');
            $table->integer('year');
            $table->string('image_base')->nullable();
            $table->string('image_relative')->nullable();
            $table->string('image_main')->nullable();
            $table->string('image_thumb')->nullable();
            $table->string('image_preload')->nullable();
            $table->text('full_description')->nullable();
            $table->string('productionCountries')->nullable();
            $table->string('producer')->nullable();
            $table->string('writer')->nullable();
            $table->string('featuring')->nullable();
            $table->string('url')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('films');
    }
};
