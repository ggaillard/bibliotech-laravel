<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Ajoute la clé étrangère livres.categorie_id → categories.id
     *
     * Une migration qui MODIFIE une table existante : c'est ainsi qu'on fait
     * évoluer une base déjà en service, sans la recréer.
     */
    public function up(): void
    {
        Schema::table('livres', function (Blueprint $table) {
            $table->foreignId('categorie_id')
                ->nullable()
                ->after('disponible')          // la colonne de référence doit exister !
                ->constrained('categories')
                ->nullOnDelete();              // catégorie supprimée → livre « non classé »
        });
    }

    public function down(): void
    {
        Schema::table('livres', function (Blueprint $table) {
            $table->dropConstrainedForeignId('categorie_id');
        });
    }
};
