# 4️⃣ Séance 4 — Gestion : modifier, supprimer ; les catégories en autonomie

> **Durée** 2 h · **Tests** `php artisan test --group=seance-04` · **Fiche de cours** [Formulaires, verbes HTTP, CSRF](../cours/04-formulaires-csrf.md)

## 🎯 Objectifs

- Écrire un formulaire de **modification** pré-rempli, envoyé en **PUT**
- **Supprimer** proprement : verbe DELETE, jeton CSRF, confirmation
- Traduire une **règle de gestion** en code
- Refaire seul, sur une autre table, ce qui a été fait en guidé

## 🎬 La situation

> **De :** Mme Lefèvre
> **Objet :** RE: les nouveautés
>
> Merci, la saisie fonctionne ! Deux remarques : nous faisons des fautes de frappe et nous ne
> pouvons pas les **corriger** ; et nous **retirons** des livres abîmés.
> Il faudrait aussi pouvoir créer la catégorie « Bande dessinée ». Attention : une catégorie qui
> contient encore des livres ne doit **jamais** disparaître.

---

## 1 ▶️ Lancer

Ajoutez un livre depuis `/gestion/livres/create`, puis ouvrez les outils de développement du
navigateur (F12 → Réseau) et renvoyez le formulaire : observez la requête **POST** et son champ `_token`.

## 2 🔍 Comprendre

1. Un formulaire HTML ne connaît que GET et POST. Comment Laravel reçoit-il un **PUT** ?
   (Cherchez ce que génère `@method('PUT')`.)
2. Que se passe-t-il si on retire `@csrf` d'un formulaire ? Essayez : quel code HTTP ?
3. Pourquoi une suppression ne doit-elle **jamais** être un simple lien `<a href="…/supprimer">` ?

## 3 ✏️ Modifier

### Mission 4.1 🟢 — Modifier un livre

**`edit()`** : comme `create()`, mais avec le livre (`edit(Livre $livre)` — liaison de modèle).

**La vue `edit.blade.php`** reprend le formulaire d'ajout. Deux différences :
```blade
<form action="{{ route('gestion.livres.update', $livre) }}" method="POST">
    @csrf
    @method('PUT')
    <input name="titre" value="{{ old('titre', $livre->titre) }}" …>
```
`old('titre', $livre->titre)` : la saisie en cas d'erreur, sinon la valeur actuelle.

> ✂️ **Ne copiez pas deux fois les mêmes champs.** Déplacez-les dans
> `gestion/livres/_formulaire.blade.php` et incluez-les dans les deux vues : `@include('gestion.livres._formulaire')`.
> Dans `create()`, passez un livre vide (`new Livre()`) pour que `$livre->titre` existe.

**`update()`** :
```php
public function update(LivreRequest $request, Livre $livre)
{
    $livre->update($request->validated());

    return redirect()->route('gestion.livres.index')->with('success', "« {$livre->titre} » a été modifié.");
}
```

**Le piège de l'ISBN.** Modifiez un livre sans toucher à son ISBN : « Un livre porte déjà cet ISBN ».
C'est le sien ! Dans `LivreRequest`, la règle doit ignorer le livre en cours de modification :
```php
use Illuminate\Validation\Rule;
// …
'isbn' => ['nullable', 'string', 'max:20', Rule::unique('livres', 'isbn')->ignore($this->route('livre'))],
```
En ajout, `$this->route('livre')` vaut `null` : la règle ne change rien.

✅ `php artisan test --group=seance-04 --filter=m1`

### Mission 4.2 🟢 — Supprimer un livre

Dans chaque ligne du tableau de la liste :
```blade
<form action="{{ route('gestion.livres.destroy', $livre) }}" method="POST" class="d-inline"
      onsubmit="return confirm('Retirer ce livre du catalogue ?')">
    @csrf
    @method('DELETE')
    <button class="btn btn-sm btn-outline-danger">Supprimer</button>
</form>
```
Et `destroy(Livre $livre)` : `$livre->delete();` puis la redirection avec un message.

✅ `php artisan test --group=seance-04 --filter=m2`

### Mission 4.3 🟡 — Les catégories, en autonomie

**Attendu :** un CRUD des catégories sous `/gestion/categories` (routes `gestion.categories.*`,
**sans** `show`) :
- créer et renommer une catégorie : `nom` obligatoire et unique, `couleur` au format `#RRGGBB` ;
- le **slug est calculé** à partir du nom : « Bande dessinée » → `bande-dessinee` ;
- supprimer une catégorie **vide** : message `success` ;
- **règle de gestion** : une catégorie qui contient des livres n'est pas supprimée — redirection
  vers la liste avec un message `error`.

> 💡 **Indices.**
> - `Route::resource('categories', …)->except('show')`. Attention : Laravel met au singulier en
>   **anglais** et appelle le paramètre `{category}`. Ajoutez `->parameters(['categories' => 'categorie'])`
>   pour que `destroy(Categorie $categorie)` fonctionne.
> - `Str::slug($nom)` (`use Illuminate\Support\Str;`).
> - Une catégorie a-t-elle des livres ? `$categorie->livres()->exists()`.
> - La base vous protège déjà (clé étrangère) ; mais une erreur SQL n'est pas un message pour la
>   bibliothécaire : c'est au code de vérifier **avant**.

✅ `php artisan test --group=seance-04 --filter=m3`

### Mission 4.4 🔴 — Filtrer la liste de gestion

**Attendu :** la liste `/gestion/livres` se filtre par titre/auteur (`?q=`) et par catégorie
(`?categorie=<id>`), et les filtres **restent** quand on change de page.
*(test : `--group=bonus --filter=s4_m4` ; cherchez `withQueryString`.)*

## 4 ✅ Valider

```bash
php artisan test --group=seance-04 && php artisan test --group=socle
git add -A && git commit -m "feat: modification, suppression, gestion des catégories" && git push
```

> ✍️ **La séance 5 commence par l'interrogation écrite n° 1** (30 min, sur papier, séances 1 à 4).
> Révisez avec les auto-évaluations des fiches 1 à 4.

---

## 📌 Concepts à connaître

1. **Verbes HTTP** — GET lit, POST crée, PUT modifie, DELETE supprime ; `@method` les simule dans un formulaire.
2. **CSRF** — le jeton `@csrf` prouve que le formulaire vient du site ; sans lui : 419.
3. **`old('champ', $valeur)`** — la saisie précédente, sinon la valeur en base.
4. **`Rule::unique()->ignore()`** — une valeur unique… sauf pour l'enregistrement qu'on modifie.
5. **Règle de gestion** — vérifiée dans le code avant d'agir, avec un message compréhensible.

## 📓 Journal de fin de séance (portail)

Ce que j'ai fait · ce qui m'a bloqué · ce que j'ai compris aujourd'hui.
