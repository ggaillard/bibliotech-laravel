<?php

namespace App\Http\Controllers;

use App\Models\Categorie;
use App\Models\Livre;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LivreController extends Controller
{
    /**
     * Catalogue public : tous les livres, avec leur catégorie.
     */
    public function index(): View
    {
        $livres = Livre::with('categorie')->orderBy('titre')->get();

        return view('livres.index', [
            'livres' => $livres,
            'categories' => Categorie::actives()->orderBy('nom')->get(),
            'stats' => [
                'totalLivres' => $livres->count(),
                'livresDisponibles' => $livres->where('disponible', true)->count(),
                'totalCategories' => Categorie::actives()->count(),
            ],
        ]);
    }

    /**
     * Fiche d'un livre. findOrFail() renvoie une page 404 si l'id n'existe pas.
     */
    public function show(int $id): View
    {
        $livre = Livre::with('categorie')->findOrFail($id);

        $livresAssocies = Livre::with('categorie')
            ->where('categorie_id', $livre->categorie_id)
            ->whereKeyNot($livre->id)
            ->take(3)
            ->get();

        return view('livres.show', [
            'livre' => $livre,
            'livresAssocies' => $livresAssocies,
        ]);
    }

    /**
     * Recherche par titre ou auteur : /recherche?q=hugo
     */
    public function search(Request $request): View
    {
        $query = trim((string) $request->query('q', ''));

        $livres = Livre::with('categorie')
            ->when($query !== '', fn ($builder) => $builder->recherche($query))
            ->orderBy('titre')
            ->get();

        return view('livres.search', [
            'livres' => $livres,
            'query' => $query,
            'total' => $livres->count(),
        ]);
    }
}
