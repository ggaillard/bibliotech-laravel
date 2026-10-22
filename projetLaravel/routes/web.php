<?php

use App\Http\Controllers\AccueilController;
use App\Http\Controllers\LivreController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Routes web de BiblioTech
|--------------------------------------------------------------------------
| Chaque ligne associe une URL (et un verbe HTTP) à une action.
| ->name('…') donne un nom à la route : dans les vues on écrit
| route('livres.index') plutôt que '/livres' — si l'URL change, rien ne casse.
*/

// Accueil
Route::get('/', [AccueilController::class, 'index'])->name('home');

// Page statique : pas besoin de contrôleur
Route::view('/about', 'about')->name('about');

// Catalogue public
Route::get('/livres', [LivreController::class, 'index'])->name('livres.index');
Route::get('/livre/{id}', [LivreController::class, 'show'])->whereNumber('id')->name('livres.show');
Route::get('/recherche', [LivreController::class, 'search'])->name('livres.search');

// Démonstration : paramètre optionnel dans l'URL (séance 1)
Route::get('/demo/hello/{nom?}', function (string $nom = 'Étudiant') {
    return view('demo.hello', ['nom' => $nom]);
})->name('demo.hello');
