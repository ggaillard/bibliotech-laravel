# 3️⃣ Séance 3 — Espace de gestion : lister et ajouter

> **Durée** 2 h · **Tests** `php artisan test --group=seance-03` · **Fiche de cours** [Contrôleurs resource et validation](../cours/03-resource-validation.md)

## 🎯 Objectifs

- Générer un **contrôleur resource** et ses 7 routes en une ligne
- Construire un **formulaire** qui renvoie ses erreurs et garde la saisie
- **Valider** côté serveur, puis ranger la validation dans une **Form Request**

## 🎬 La situation

> **De :** Mme Lefèvre
> **Objet :** les nouveautés
>
> Chaque mois, nous recevons une vingtaine de livres et nous envoyons la liste à l'ancien
> prestataire, qui les ajoutait en base. Nous voudrions **les saisir nous-mêmes**, dans un espace
> réservé, avec une liste de tout le catalogue pour vérifier.

Toutes les adresses de cet espace commenceront par **`/gestion`**, et ses routes par **`gestion.`**.
Il ne sera protégé qu'en séance 5 : pour l'instant, n'importe qui peut l'ouvrir.

---

## 1 ▶️ Lancer

```bash
php artisan route:list --name=livres
```
Le public a déjà `livres.index`, `livres.show`, `livres.search`. Nos routes de gestion s'appelleront
`gestion.livres.*` pour ne pas les écraser.

## 2 🔍 Comprendre — les 7 actions d'une *resource*

| Verbe | URL | Action | Nom de route | Rôle |
|---|---|---|---|---|
| GET | `/gestion/livres` | `index` | `gestion.livres.index` | liste |
| GET | `/gestion/livres/create` | `create` | `gestion.livres.create` | formulaire d'ajout |
| POST | `/gestion/livres` | `store` | `gestion.livres.store` | enregistrer |
| GET | `/gestion/livres/{livre}` | `show` | `gestion.livres.show` | fiche |
| GET | `/gestion/livres/{livre}/edit` | `edit` | `gestion.livres.edit` | formulaire de modification *(séance 4)* |
| PUT | `/gestion/livres/{livre}` | `update` | `gestion.livres.update` | enregistrer la modification *(séance 4)* |
| DELETE | `/gestion/livres/{livre}` | `destroy` | `gestion.livres.destroy` | supprimer *(séance 4)* |

Question : pourquoi `create` et `store` sont-elles **deux** actions et pas une ?

## 3 ✏️ Modifier

### Mission 3.1 🟢 — Contrôleur resource et liste paginée

```bash
php artisan make:controller Gestion/LivreController --resource --model=Livre
```

Dans `routes/web.php` (en bas du fichier) :
```php
use App\Http\Controllers\Gestion;

Route::prefix('gestion')->name('gestion.')->group(function () {
    Route::resource('livres', Gestion\LivreController::class);
});
```
`php artisan route:list --path=gestion` : vos 7 routes sont là.

La méthode `index()` :
```php
public function index()
{
    $livres = Livre::with(['auteur', 'categorie'])->orderBy('titre')->paginate(15);

    return view('gestion.livres.index', ['livres' => $livres]);
}
```

La vue `resources/views/gestion/livres/index.blade.php` : partez du modèle
`resources/views/templates/index.blade.php` (un tableau Bootstrap avec des `TODO`). Une ligne par livre :
titre (lien vers `gestion.livres.show`), auteur, catégorie, statut. Sous le tableau : `{{ $livres->links() }}`.

✅ `php artisan test --group=seance-03 --filter=m1`

### Mission 3.2 🟢 — Le formulaire d'ajout

**`create()`** passe à la vue les listes des auteurs et des catégories :
```php
return view('gestion.livres.create', [
    'auteurs' => Auteur::orderBy('nom')->get(),
    'categories' => Categorie::orderBy('nom')->get(),
]);
```

**La vue** (modèle : `templates/create.blade.php`). Un formulaire `POST` vers `route('gestion.livres.store')`,
avec `@csrf`, et ces champs : `titre`, `auteur_id` et `categorie_id` (deux `<select>`), `annee`,
`nb_pages`, `isbn`, `resume`, `disponible` (case à cocher). Pour chaque champ, le même schéma :
```blade
<input type="text" name="titre" value="{{ old('titre') }}"
       class="form-control @error('titre') is-invalid @enderror">
@error('titre') <div class="invalid-feedback">{{ $message }}</div> @enderror
```
`old()` remet la saisie si la validation échoue ; `@error` affiche le message.

**`store()`** valide, enregistre, redirige avec un message :
```php
public function store(Request $request)
{
    $donnees = $request->validate([
        'titre' => ['required', 'string', 'max:255'],
        'auteur_id' => ['required', 'exists:auteurs,id'],
        'categorie_id' => ['required', 'exists:categories,id'],
        'annee' => ['nullable', 'integer', 'min:1450', 'max:'.now()->year],
        'nb_pages' => ['nullable', 'integer', 'min:1'],
        'isbn' => ['nullable', 'string', 'max:20', 'unique:livres,isbn'],
        'resume' => ['nullable', 'string'],
    ]);
    $donnees['disponible'] = $request->boolean('disponible');

    $livre = Livre::create($donnees);

    return redirect()->route('gestion.livres.index')
        ->with('success', "« {$livre->titre} » a été ajouté.");
}
```
Le message `success` est déjà affiché par le gabarit du site. Essayez d'envoyer le formulaire vide.

✅ `php artisan test --group=seance-03 --filter=m2`

### Mission 3.3 🟡 — Fiche de gestion et Form Request

**Attendu :**
- `/gestion/livres/{livre}` affiche le titre et le nom complet de l'auteur ; un id inconnu renvoie 404.
- Les règles de validation ne sont plus dans le contrôleur mais dans **`App\Http\Requests\LivreRequest`**,
  et `store()` reçoit `LivreRequest $request` au lieu de `Request $request`.

> 💡 **Indices.**
> - La signature générée est `show(Livre $livre)` : c'est la **liaison de modèle** (*route model binding*).
>   Laravel cherche le livre d'après l'id de l'URL — et renvoie 404 s'il n'existe pas. Aucun `findOrFail` à écrire.
> - `php artisan make:request LivreRequest` ; `authorize()` renvoie `true` pour l'instant (séance 5) ;
>   `rules()` renvoie votre tableau de règles. Dans `store()`, `$request->validated()` donne les données propres.
> - Pourquoi cette classe ? La séance 4 a besoin **des mêmes règles** pour la modification.

✅ `php artisan test --group=seance-03`

## 4 ✅ Valider

```bash
php artisan test --group=seance-03 && php artisan test --group=socle
git add -A && git commit -m "feat: espace de gestion, ajout de livres" && git push
```

---

## 📌 Concepts à connaître

1. **Contrôleur resource** — 7 actions standard, 7 routes générées par `Route::resource()`.
2. **Validation serveur** — la seule qui protège : le navigateur peut être contourné.
3. **`old()` et `@error`** — en cas d'erreur, le formulaire revient rempli avec les messages.
4. **Liaison de modèle** — `show(Livre $livre)` : Laravel charge le livre de l'URL ou renvoie 404.
5. **Message flash** — `->with('success', …)` : stocké en session pour **une seule** requête.

## 📓 Journal de fin de séance (portail)

Ce que j'ai fait · ce qui m'a bloqué · ce que j'ai compris aujourd'hui.
