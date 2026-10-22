<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Crée la table des livres.
     */
    public function up(): void
    {
        Schema::create('livres', function (Blueprint $table) {
            $table->id();
            $table->string('titre');
            $table->string('auteur');                  // texte libre pour l'instant (voir séance 2)
            $table->unsignedSmallInteger('annee')->nullable();
            $table->unsignedInteger('nb_pages')->nullable();
            $table->string('isbn')->nullable()->unique();
            $table->text('resume')->nullable();
            $table->string('couverture')->nullable();
            $table->boolean('disponible')->default(true);
            $table->timestamps();

            $table->index(['titre', 'auteur']);
            $table->index('disponible');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('livres');
    }
};
