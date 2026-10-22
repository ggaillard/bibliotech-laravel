# 7️⃣ Séance 7 — Une API REST pour le catalogue

> **Durée** 2 h · **Tests** `php artisan test --group=seance-07` · **Fiche de cours** [API REST](../cours/07-api-rest.md)

## 🎯 Objectifs

- Exposer des données en **JSON** avec les bons **codes HTTP**
- Définir un **contrat** de sortie avec une *API Resource*
- Protéger une écriture par **jeton** (Laravel Sanctum) et par rôle

## 🎬 La situation

> **De :** M. Benali, service numérique de la communauté de communes
> **Objet :** catalogue dans l'application mobile
>
> Notre application mobile « Ma ville » affichera le catalogue des trois médiathèques du territoire.
> Il nous faut une **API** : la liste des livres (filtrable par catégorie et disponibilité) et la fiche
> d'un livre. Plus tard, notre logiciel d'acquisitions **créera** les livres commandés : il aura un
> compte bibliothécaire.

| Verbe | URL | Accès | Réponse |
|---|---|---|---|
| GET | `/api/livres?categorie=roman&disponible=1` | public | 200, liste paginée |
| GET | `/api/livres/{livre}` | public | 200, ou 404 |
| POST | `/api/livres` | jeton **bibliothécaire** | 201 ; 401 sans jeton ; 403 adhérent ; 422 données invalides |

---

## 1 ▶️ Lancer

```bash
php artisan install:api
```
Cette commande installe **Sanctum** (les jetons), crée `routes/api.php` et la table
`personal_access_tokens`. Ajoutez le trait `HasApiTokens` au modèle `User`, comme demandé à l'écran.

Les requêtes se testent depuis le terminal du Codespace (Apache écoute sur le port 80 du conteneur) :
```bash
curl -s http://localhost/api/livres | head -c 400
```

## 2 🔍 Comprendre

1. Pourquoi ne pas renvoyer simplement `Livre::all()` ? Citez deux informations qui ne regardent pas l'application mobile.
2. Quelle différence entre **401** et **403** ?
3. Une route d'API nommée `livres.index`… alors qu'une route web porte déjà ce nom. Que se passe-t-il ?

## 3 ✏️ Modifier

### Mission 7.1 🟢 — `GET /api/livres`

**La ressource** : le contrat public d'un livre, indépendant de la table.
```bash
php artisan make:resource LivreResource
```
```php
public function toArray(Request $request): array
{
    return [
        'id' => $this->id,
        'titre' => $this->titre,
        'auteur' => $this->auteur?->nom_complet,
        'categorie' => $this->categorie?->nom,
        'annee' => $this->annee,
        'isbn' => $this->isbn,
        'disponible' => $this->disponible,
    ];
}
```

**Le contrôleur** : `php artisan make:controller Api/LivreController`
```php
public function index(Request $request)
{
    $livres = Livre::with(['auteur', 'categorie'])->orderBy('titre')->paginate(20);

    return LivreResource::collection($livres);   // data + links + meta
}
```

**La route**, dans `routes/api.php` (le préfixe `/api` est automatique) :
```php
Route::get('/livres', [LivreController::class, 'index'])->name('api.livres.index');
```
⚠️ Le nom **commence par `api.`** : sinon il écraserait la route web `livres.index` et le catalogue
du site renverrait du JSON. Le groupe `socle` vous préviendrait.

✅ `php artisan test --group=seance-07 --filter=m1`

### Mission 7.2 🟢 — `GET /api/livres/{livre}`

Une méthode `show(Livre $livre)` qui renvoie `new LivreResource($livre)`, et la route
`api.livres.show`. Essayez `curl -i http://localhost/api/livres/99999` : la liaison de modèle produit
un **404 en JSON** sans une ligne de code (voyez `shouldRenderJsonWhen` dans `bootstrap/app.php`).

✅ `php artisan test --group=seance-07 --filter=m2`

### Mission 7.3 🟡 — Filtres

**Attendu :** `?categorie=<slug>` ne garde que les livres de cette catégorie ; `?disponible=1`
seulement les disponibles ; les deux se combinent.

> 💡 **Indice.** `->when($request->filled('categorie'), fn ($q) => …)` n'ajoute la condition que si
> le paramètre est présent. Le modèle `Livre` a déjà un *scope* par slug de catégorie.

✅ `php artisan test --group=seance-07 --filter=m3`

### Mission 7.4 🟡 — Créer un livre avec un jeton

**Attendu :** `POST /api/livres` (route `api.livres.store`) :
- sans jeton → **401** ; jeton d'un adhérent → **403** ;
- jeton d'un bibliothécaire et données valides → **201** et le livre en JSON ;
- données invalides → **422** avec les erreurs en JSON.

> 💡 **Indices.** Middlewares `auth:sanctum` puis `role:bibliothecaire,admin` (celui de la séance 5).
> La validation ? Vous l'avez déjà : `LivreRequest`. Pour le code 201 :
> `(new LivreResource($livre))->response()->setStatusCode(201)`.

Pour essayer à la main, créez un jeton :
```bash
php artisan tinker
>>> App\Models\User::where('email', 'bibliothecaire@bibliotech.test')->first()->createToken('essai')->plainTextToken
```
```bash
curl -i -X POST http://localhost/api/livres -H "Accept: application/json" \
     -H "Authorization: Bearer <votre jeton>" -d "titre=Test"
```

✅ `php artisan test --group=seance-07`

### Mission 7.5 🔴 — Obtenir un jeton

**Attendu :** `POST /api/jetons` avec `email`, `password` et `appareil` renvoie `{"jeton": "…"}`
(201), ou une erreur 422 si les identifiants sont faux — même message que l'on se trompe d'e-mail
ou de mot de passe, et pas plus de 5 essais par minute.

## 4 ✅ Valider

```bash
php artisan test --group=seance-07 && php artisan test --group=socle
git add -A && git commit -m "feat: API REST du catalogue" && git push
```

> ✍️ **La séance 8 commence par l'interrogation écrite n° 2** (30 min, séances 5 à 7).

---

## 📌 Concepts à connaître

1. **REST** — des ressources (`/livres`), des verbes (GET, POST…), des codes de retour qui ont un sens.
2. **Codes HTTP** — 200 lu, 201 créé, 401 non authentifié, 403 interdit, 404 introuvable, 422 invalide.
3. **API Resource** — le contrat de sortie : on choisit les champs exposés, la table peut évoluer.
4. **Jeton** — une API ne garde pas de session : chaque requête porte `Authorization: Bearer …`.
5. **Pagination** — une API ne renvoie jamais « tout » : `data`, `links`, `meta`.

## 📓 Journal de fin de séance (portail)

Ce que j'ai fait · ce qui m'a bloqué · ce que j'ai compris aujourd'hui.
