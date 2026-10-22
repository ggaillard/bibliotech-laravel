# 📄 Fiche Mémo A4 - Séances 3 et 4 Contrôleurs & Vues

> 📎 **Document de référence** repris de la version 2025-2026 et mis à jour (MySQL, Laravel 13, noms en français).
> Les fiches de [cours/](../README.md) et la [documentation officielle](https://laravel.com/docs) font foi.


**À imprimer et utiliser pendant le TP et l'évaluation**

---

## 🎯 Commandes Artisan Essentielles

### **Contrôleurs**
```bash
# Créer un contrôleur resource (toutes les méthodes CRUD)
php artisan make:controller NomController --resource

# Créer un contrôleur simple
php artisan make:controller NomController
```

### **Routes**
```bash
# Lister toutes les routes
php artisan route:list

# Filtrer par nom
php artisan route:list --name=livres

# Filtrer par méthode
php artisan route:list --method=GET
```

### **Base de données**
```bash
# Créer une migration
php artisan make:migration create_table_name

# Exécuter les migrations
php artisan migrate

# Annuler la dernière migration
php artisan migrate:rollback

# Réinitialiser et réexécuter
php artisan migrate:fresh --seed
```

### **Modèles**
```bash
# Créer un modèle
php artisan make:model Nom

# Modèle + migration + contrôleur resource
php artisan make:model Nom -mcr
```

---

## 🎭 Structure d'un Contrôleur Resource

```php
namespace App\Http\Controllers;

use App\Models\Livre;
use Illuminate\Http\Request;

class LivreController extends Controller
{
    // 1. Afficher la liste (GET /livres)
    public function index()
    {
        $livres = Livre::with('categorie')->paginate(10);
        return view('livres.index', compact('livres'));
    }

    // 2. Afficher le formulaire de création (GET /livres/create)
    public function create()
    {
        return view('livres.create');
    }

    // 3. Enregistrer une nouvelle ressource (POST /livres)
    public function store(Request $request)
    {
        $validated = $request->validate([
            'titre' => 'required|max:255',
            // ... autres règles
        ]);
        
        Livre::create($validated);
        return redirect()->route('livres.index')
            ->with('success', 'Livre créé !');
    }

    // 4. Afficher une ressource (GET /livres/{id})
    public function show(Livre $livre)
    {
        return view('livres.show', compact('livre'));
    }

    // 5. Afficher le formulaire de modification (GET /livres/{id}/edit)
    public function edit(Livre $livre)
    {
        return view('livres.edit', compact('livre'));
    }

    // 6. Mettre à jour une ressource (PUT /livres/{id})
    public function update(Request $request, Livre $livre)
    {
        $validated = $request->validate([
            'titre' => 'required|max:255',
        ]);
        
        $livre->update($validated);
        return redirect()->route('livres.show', $livre)
            ->with('success', 'Livre modifié !');
    }

    // 7. Supprimer une ressource (DELETE /livres/{id})
    public function destroy(Livre $livre)
    {
        $livre->delete();
        return redirect()->route('livres.index')
            ->with('success', 'Livre supprimé !');
    }
}
```

---

## 🛣️ Routes Resource

### **Définition**
```php
// routes/web.php
Route::resource('livres', LivreController::class);

// Génère automatiquement 7 routes :
// GET    /livres           → index()
// GET    /livres/create    → create()
// POST   /livres           → store()
// GET    /livres/{id}      → show()
// GET    /livres/{id}/edit → edit()
// PUT    /livres/{id}      → update()
// DELETE /livres/{id}      → destroy()
```

### **Noms de routes générés**
```php
livres.index   → route('livres.index')
livres.create  → route('livres.create')
livres.store   → route('livres.store')
livres.show    → route('livres.show', $livre)
livres.edit    → route('livres.edit', $livre)
livres.update  → route('livres.update', $livre)
livres.destroy → route('livres.destroy', $livre)
```

---

## ✅ Règles de Validation Courantes

```php
$request->validate([
    // Obligatoire
    'champ' => 'required',
    
    // Type
    'email' => 'email',
    'url' => 'url',
    'date' => 'date',
    'integer' => 'integer',
    'numeric' => 'numeric',
    'boolean' => 'boolean',
    
    // Longueur
    'titre' => 'min:3|max:255',
    'isbn' => 'size:13',
    
    // Unicité
    'email' => 'unique:users,email',
    
    // Existe dans une table
    'categorie_id' => 'exists:categories,id',
    
    // Valeurs autorisées
    'statut' => 'in:actif,inactif',
    
    // Combinaisons
    'titre' => 'required|string|max:255|unique:livres',
]);
```

### **Messages personnalisés**
```php
$request->validate($rules, [
    'titre.required' => 'Le titre est obligatoire',
    'titre.max' => 'Le titre ne doit pas dépasser :max caractères',
]);
```

---

## 🎨 Blade Templates

### **Layout principal**
```blade
{{-- resources/views/layouts/app.blade.php --}}
<!DOCTYPE html>
<html>
<head>
    <title>@yield('title', 'BiblioTech')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <nav>...</nav>
    
    <main class="container">
        @yield('content')
    </main>
</body>
</html>
```

### **Vue enfant**
```blade
{{-- resources/views/livres/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Liste des livres')

@section('content')
    <h1>Livres</h1>
    <!-- Contenu -->
@endsection
```

### **Directives utiles**
```blade
{{-- Afficher une variable (échappée) --}}
{{ $variable }}

{{-- Afficher HTML brut (non échappé) --}}
{!! $html !!}

{{-- Structures de contrôle --}}
@if ($condition)
    ...
@elseif ($autre)
    ...
@else
    ...
@endif

@foreach ($items as $item)
    {{ $item->nom }}
@endforeach

@forelse ($livres as $livre)
    {{ $livre->titre }}
@empty
    <p>Aucun livre</p>
@endforelse

{{-- Vérifier l'existence --}}
@isset($variable)
    {{ $variable }}
@endisset

@empty($collection)
    <p>Vide</p>
@endempty
```

---

## 📝 Formulaires Blade

### **Formulaire de création**
```blade
<form action="{{ route('livres.store') }}" method="POST">
    @csrf
    
    <div class="mb-3">
        <label for="titre" class="form-label">Titre</label>
        <input type="text" 
               class="form-control @error('titre') is-invalid @enderror" 
               id="titre" 
               name="titre" 
               value="{{ old('titre') }}"
               required>
        @error('titre')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    
    <button type="submit" class="btn btn-primary">Enregistrer</button>
</form>
```

### **Formulaire de modification**
```blade
<form action="{{ route('livres.update', $livre) }}" method="POST">
    @csrf
    @method('PUT')
    
    <input type="text" 
           name="titre" 
           value="{{ old('titre', $livre->titre) }}">
    
    <button type="submit">Mettre à jour</button>
</form>
```

### **Formulaire de suppression**
```blade
<form action="{{ route('livres.destroy', $livre) }}" method="POST" 
      onsubmit="return confirm('Confirmer la suppression ?')">
    @csrf
    @method('DELETE')
    <button type="submit" class="btn btn-danger">Supprimer</button>
</form>
```

---

## 💬 Messages Flash

### **Dans le contrôleur**
```php
// Message de succès
return redirect()->route('livres.index')
    ->with('success', 'Livre créé avec succès !');

// Message d'erreur
return redirect()->back()
    ->with('error', 'Une erreur est survenue');

// Plusieurs messages
return redirect()->back()
    ->with('success', 'Opération réussie')
    ->with('info', 'Information complémentaire');
```

### **Dans la vue**
```blade
@if (session('success'))
    <div class="alert alert-success alert-dismissible fade show">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if (session('error'))
    <div class="alert alert-danger">
        {{ session('error') }}
    </div>
@endif
```

---

## 🔗 Liens et URLs

```blade
{{-- Lien vers une route nommée --}}
<a href="{{ route('livres.index') }}">Liste</a>

{{-- Avec paramètre --}}
<a href="{{ route('livres.show', $livre) }}">Détails</a>
<a href="{{ route('livres.edit', $livre->id) }}">Modifier</a>

{{-- URL absolue --}}
{{ url('/livres') }}

{{-- Asset (CSS, JS, images) --}}
<link href="{{ asset('css/app.css') }}" rel="stylesheet">
<img src="{{ asset('images/logo.png') }}">
```

---

## 📊 Pagination

### **Dans le contrôleur**
```php
$livres = Livre::paginate(10);
// ou
$livres = Livre::orderBy('titre')->paginate(15);
```

### **Dans la vue**
```blade
@foreach ($livres as $livre)
    {{ $livre->titre }}
@endforeach

{{-- Liens de pagination Bootstrap --}}
{{ $livres->links() }}

{{-- Informations de pagination --}}
<p>
    Affichage de {{ $livres->firstItem() }} à {{ $livres->lastItem() }}
    sur {{ $livres->total() }} résultats
</p>
```

---

## 🎨 Bootstrap 5 - Classes Utiles

### **Boutons**
```html
<button class="btn btn-primary">Principal</button>
<button class="btn btn-success">Succès</button>
<button class="btn btn-danger">Danger</button>
<button class="btn btn-warning">Avertissement</button>
<button class="btn btn-secondary">Secondaire</button>
```

### **Badges**
```html
<span class="badge bg-success">Actif</span>
<span class="badge bg-secondary">Inactif</span>
<span class="badge bg-primary">Nouveau</span>
```

### **Tableaux**
```html
<table class="table table-striped table-hover">
    <thead>
        <tr>
            <th>Colonne 1</th>
            <th>Colonne 2</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>Valeur 1</td>
            <td>Valeur 2</td>
        </tr>
    </tbody>
</table>
```

### **Cards**
```html
<div class="card">
    <div class="card-header">
        Titre
    </div>
    <div class="card-body">
        Contenu
    </div>
    <div class="card-footer">
        Actions
    </div>
</div>
```

---

## 🚨 Débogage

```php
// Afficher et arrêter
dd($variable);

// Afficher sans arrêter
dump($variable);

// Logs
\Log::info('Message');
\Log::error('Erreur');

// Dans Blade
@dump($variable)
@dd($variable)
```

---

**💡 Conseil** : Imprimez cette fiche et gardez-la à portée de main pendant le TP !

**Dernière mise à jour :** 6 octobre 2025
