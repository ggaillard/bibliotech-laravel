<?php

namespace App\Http\Controllers;

use App\Models\Categorie;
use App\Models\Livre;
use Illuminate\View\View;

class AccueilController extends Controller
{
    /**
     * Page d'accueil : quelques chiffres et trois livres mis en avant.
     */
    public function index(): View
    {
        $stats = [
            'totalLivres' => Livre::count(),
            'livresDisponibles' => Livre::disponible()->count(),
            'totalCategories' => Categorie::actives()->count(),
        ];

        $livresEnVedette = Livre::with('categorie')
            ->disponible()
            ->latest()
            ->take(3)
            ->get();

        return view('welcome', [
            'stats' => $stats,
            'livresEnVedette' => $livresEnVedette,
        ]);
    }
}
