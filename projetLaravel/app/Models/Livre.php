<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Livre extends Model
{
    use HasFactory;

    protected $table = 'livres';

    protected $fillable = [
        'titre',
        'auteur',
        'annee',
        'nb_pages',
        'isbn',
        'resume',
        'couverture',
        'disponible',
        'categorie_id',
    ];

    protected function casts(): array
    {
        return [
            'disponible' => 'boolean',
            'annee' => 'integer',
            'nb_pages' => 'integer',
        ];
    }

    /**
     * Un livre appartient à une catégorie (N → 1).
     */
    public function categorie(): BelongsTo
    {
        return $this->belongsTo(Categorie::class, 'categorie_id');
    }

    /**
     * Livres que l'on peut emprunter.
     */
    public function scopeDisponible(Builder $query): Builder
    {
        return $query->where('disponible', true);
    }

    /**
     * Recherche par titre ou auteur.
     * Le where(function…) produit : WHERE (titre LIKE ? OR auteur LIKE ?)
     * Les parenthèses empêchent le OR d'« avaler » les autres conditions (disponible = 1…).
     */
    public function scopeRecherche(Builder $query, string $terme): Builder
    {
        return $query->where(function (Builder $q) use ($terme) {
            $q->where('titre', 'like', '%'.$terme.'%')
                ->orWhere('auteur', 'like', '%'.$terme.'%');
        });
    }

    /**
     * Livres d'une catégorie, désignée par son slug.
     */
    public function scopeParCategorieSlug(Builder $query, string $slug): Builder
    {
        return $query->whereHas('categorie', function (Builder $q) use ($slug) {
            $q->where('slug', $slug);
        });
    }
}
