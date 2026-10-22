<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Categorie extends Model
{
    use HasFactory;

    protected $table = 'categories';

    protected $fillable = [
        'nom',
        'description',
        'slug',
        'couleur',
        'icone',
        'active',
    ];

    protected function casts(): array
    {
        return [
            'active' => 'boolean',
        ];
    }

    /**
     * Une catégorie regroupe plusieurs livres (1 → N).
     */
    public function livres(): HasMany
    {
        return $this->hasMany(Livre::class, 'categorie_id');
    }

    /**
     * Catégories visibles sur le site.
     */
    public function scopeActives(Builder $query): Builder
    {
        return $query->where('active', true);
    }

    /**
     * Recherche dans le nom ou la description.
     * where(function…) écrit les parenthèses : WHERE (nom LIKE ? OR description LIKE ?).
     * Dans un scope, Eloquent les ajouterait de lui-même ; écrites dans un contrôleur,
     * des conditions where()->orWhere() sans regroupement casseraient les autres filtres.
     */
    public function scopeRecherche(Builder $query, string $terme): Builder
    {
        return $query->where(function (Builder $q) use ($terme) {
            $q->where('nom', 'like', '%'.$terme.'%')
                ->orWhere('description', 'like', '%'.$terme.'%');
        });
    }

    public function scopeParSlug(Builder $query, string $slug): Builder
    {
        return $query->where('slug', $slug);
    }
}
