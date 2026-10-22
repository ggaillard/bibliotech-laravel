# API REST

> **Séance 7** · Lecture 9 min · ▶️ [Faire la séance](../seances/seance-07.md)

## L'idée

Une **API** sert des **données** (JSON) à un autre programme — ici l'application mobile de la
communauté de communes. Le style **REST** : des **ressources** nommées par des URL au pluriel, des
**verbes** HTTP pour les actions, des **codes** de retour qui ont un sens.

| Code | Sens | Exemple BiblioTech |
|---|---|---|
| 200 | OK | liste des livres |
| 201 | Créé | livre ajouté par l'API |
| 401 | Non authentifié | pas de jeton |
| 403 | Interdit | jeton d'un adhérent |
| 404 | Introuvable | `/api/livres/99999` |
| 422 | Données invalides | titre vide |
| 429 | Trop de requêtes | limitation de débit |

Une API n'a pas de session : chaque requête **porte** son identité dans un en-tête
`Authorization: Bearer <jeton>`. Le jeton est créé une fois, stocké **haché** côté serveur, et peut être révoqué.

## Le code à reconnaître

```php
// routes/api.php — préfixe /api automatique
Route::get('/livres', [Api\LivreController::class, 'index'])->name('api.livres.index');
Route::post('/livres', [Api\LivreController::class, 'store'])
    ->middleware(['auth:sanctum', 'role:bibliothecaire,admin'])->name('api.livres.store');
```
```php
class LivreResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return ['id' => $this->id, 'titre' => $this->titre, 'auteur' => $this->auteur?->nom_complet];
    }
}
return LivreResource::collection(Livre::paginate(20));       // { data, links, meta }
```
```bash
curl -i -H "Accept: application/json" -H "Authorization: Bearer 1|abc…" http://localhost/api/livres
```

## Les pièges

- **Renvoyer le modèle brut** : on expose des colonnes internes, et le jour où la table change, l'application mobile casse. La *Resource* est un **contrat**.
- **Oublier `Accept: application/json`** côté client : hors de `/api/…`, une erreur de validation renvoie alors une redirection HTML. Dans BiblioTech, `bootstrap/app.php` force le JSON pour `/api/*` — un client correct envoie quand même l'en-tête.
- **Collision de noms de routes** entre web et API.
- **Pas de pagination** : 50 000 livres d'un coup.

## ✅ Auto-évaluation

1. Quelle URL et quel verbe pour : lister les livres ? en créer un ? lire le livre 7 ?
2. 401 ou 403 : un adhérent connecté par jeton tente `POST /api/livres`. Et sans jeton ?
3. Pourquoi une *API Resource* plutôt que `return Livre::all();` ?
4. Où se trouve l'identité de l'appelant dans une requête d'API ?
5. Que contiennent les clés `data`, `links` et `meta` d'une réponse paginée ?

<details><summary>Réponses</summary>

1. `GET /api/livres` ; `POST /api/livres` ; `GET /api/livres/7`.
2. Adhérent : 403. Sans jeton : 401.
3. Pour choisir les champs exposés (contrat stable) et ne pas divulguer les colonnes internes.
4. Dans l'en-tête `Authorization: Bearer <jeton>`.
5. Les éléments de la page ; les liens première/précédente/suivante/dernière ; la page courante, le total, la taille de page…
</details>
