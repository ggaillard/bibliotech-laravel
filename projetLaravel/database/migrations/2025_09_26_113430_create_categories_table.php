<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Crée la table des catégories (Roman, Policier, Informatique…).
     */
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
            $table->text('description')->nullable();
            $table->string('slug')->unique();          // morceau d'URL : « science-fiction »
            $table->string('couleur', 7)->default('#6B7280'); // couleur hexadécimale
            $table->timestamps();

            $table->index('nom');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
