# Règles de gestion et transactions

> **Séance 6** · Lecture 8 min · ▶️ [Faire la séance](../seances/seance-06.md)

## L'idée

Une **règle de gestion** est une contrainte de l'organisation, pas de la technique :
*3 emprunts maximum*, *21 jours de prêt*. Elle doit vivre **à un seul endroit** pour s'appliquer
partout — site, API, import… Ce n'est donc ni la vue (contournable), ni le contrôleur (il y en a
plusieurs) : c'est une classe de **service** (ou le modèle).

Une **transaction** regroupe plusieurs écritures en un **tout ou rien** : créer l'emprunt **et**
rendre le livre indisponible. Si la seconde échoue, la première est annulée (*rollback*).

```mermaid
flowchart TB
    subgraph T["DB::transaction"]
      direction TB
      V1{"livre disponible ?"} -- non --> X["EmpruntImpossible"]
      V1 -- oui --> V2{"moins de 3<br/>emprunts en cours ?"}
      V2 -- non --> X
      V2 -- oui --> E["INSERT emprunt"] --> U["UPDATE livre<br/>disponible = 0"]
    end
    X --> M["contrôleur : message « error »"]
    U --> OK["COMMIT"]
```

## Le code à reconnaître

```php
return DB::transaction(function () use ($adherent, $livre) {
    $livre = Livre::whereKey($livre->id)->lockForUpdate()->firstOrFail();

    if (! $livre->disponible) {
        throw new EmpruntImpossible("« {$livre->titre} » est déjà emprunté.");
    }
    if ($adherent->emprunts()->enCours()->count() >= 3) {
        throw new EmpruntImpossible('Vous avez déjà 3 emprunts en cours.');
    }

    $emprunt = $adherent->emprunts()->create([...]);
    $livre->update(['disponible' => false]);
    return $emprunt;
});
```
```php
// contrôleur
try {
    $this->service->emprunter($request->user(), $livre);
} catch (EmpruntImpossible $e) {
    return back()->with('error', $e->getMessage());
}
```

## Les pièges

- **Vérifier puis écrire hors transaction** : deux adhérents passent la vérification en même temps
  et empruntent le même livre (*race condition*). `lockForUpdate()` verrouille la ligne jusqu'à la fin de la transaction.
- **Supprimer la ligne au retour** : on perd l'historique. Une date `rendu_le` vaut mieux qu'un `DELETE`.
- **Coder la règle dans le bouton** (« griser si 3 emprunts ») : utile pour l'ergonomie, inutile pour la sécurité.

## ✅ Auto-évaluation

1. Donnez deux règles de gestion des emprunts et l'endroit où vous les avez codées.
2. Que se passe-t-il si une exception est levée au milieu d'un `DB::transaction` ?
3. Pourquoi l'emprunt est-il une table et pas une simple colonne `emprunte_par` dans `livres` ?
4. À quoi sert `lockForUpdate()` ?
5. Que retourne `Emprunt::enCours()` ? Écrivez le *scope*.

<details><summary>Réponses</summary>

1. Livre disponible ; 3 emprunts en cours maximum (ou durée 21 jours) — dans `ServiceEmprunt`.
2. Toutes les écritures de la transaction sont annulées, l'exception remonte.
3. Parce qu'un livre est emprunté de nombreuses fois et que chaque emprunt a ses dates : l'association porte des données et se répète.
4. À verrouiller la ligne lue jusqu'à la fin de la transaction : une autre transaction qui veut la même ligne attend.
5. Les emprunts non rendus : `return $query->whereNull('rendu_le');`
</details>
