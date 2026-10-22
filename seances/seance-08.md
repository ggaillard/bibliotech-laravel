# 8️⃣ Séance 8 — Qualité, sécurité, mise en ligne

> **Durée** 2 h, dont 30 min d'**interrogation écrite n° 2** · **Tests** `php artisan test --group=seance-08`
> · **Fiche de cours** [Qualité et mise en production](../cours/08-qualite-production.md)

## 🎯 Objectifs

- Mener un **audit de sécurité** simple et corriger une faille réelle
- Écrire **ses propres tests** et faire respecter un **style de code** par la CI
- **Mettre en ligne** l'application avec une configuration de production
- Présenter la réalisation dans son **tableau de synthèse E5**

## 🎬 La situation

> **De :** Mme Lefèvre
> **Objet :** ouverture au public
>
> La direction veut ouvrir BiblioTech aux habitants le mois prochain. Avant, le DSI de la
> communauté de communes demande trois choses : un **audit de sécurité**, la preuve que
> l'application est **testée**, et une **version en ligne** pour la recette.

---

## 1 ▶️ Lancer

```bash
php artisan test                    # toute la suite : où en êtes-vous ?
composer audit                      # des dépendances avec des failles connues ?
./vendor/bin/pint --test            # le code respecte-t-il le style Laravel ?
```

## 2 🔍 Comprendre — la grille d'audit

Parcourez l'application **et son code** avec cette grille. Notez chaque constat dans votre journal.

| # | Risque (OWASP) | Ce que vous vérifiez |
|---|---|---|
| A | Injection SQL | aucune requête construite en collant une saisie (`DB::raw`, `whereRaw` avec une variable) |
| B | **XSS** | toute donnée saisie par un utilisateur est affichée avec `{{ }}`, jamais `{!! !!}` |
| C | CSRF | chaque formulaire POST/PUT/DELETE contient `@csrf` |
| D | Contrôle d'accès | chaque route de gestion passe par `auth` **et** `role` ; l'API d'écriture par un jeton |
| E | Affectation de masse | `role` n'est jamais remplissable ; les contrôleurs utilisent `validated()` |
| F | Authentification | mots de passe hachés, message d'erreur neutre, tentatives limitées |
| G | Configuration | `.env` n'est pas versionné ; en production `APP_DEBUG=false` |
| H | Composants vulnérables | `composer audit` ne signale rien |

## 3 ✏️ Modifier

### Mission 8.1 🟡 — Trouver et corriger la faille

**Attendu :** la grille révèle une faille présente **depuis le premier jour**. Une bibliothécaire
malveillante (ou un compte volé) peut saisir dans un livre un texte qui **exécute du JavaScript**
chez tous les visiteurs qui ouvrent la fiche. Trouvez-la, prouvez-la, corrigez-la.

> 💡 **Indices.** Ajoutez un livre dont le résumé est `<script>alert('XSS')</script>` et ouvrez sa
> fiche publique. Puis : `grep -rn '{!!' resources/views`.
> Rédigez dans votre journal : *où*, *quel risque*, *quelle correction*, *comment le test le prouve*.

✅ `php artisan test --group=seance-08 --filter=m1`

### Mission 8.2 🟢 — Vos propres tests

Les tests fournis ne couvrent pas tout. **Attendu :** au moins **deux** tests à vous dans
`tests/Feature/Etudiant/`, sur un comportement que personne n'a encore testé.

```php
<?php

namespace Tests\Feature\Etudiant;

use App\Models\Livre;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MesTestsTest extends TestCase
{
    use RefreshDatabase;

    public function test_un_adherent_ne_voit_pas_le_lien_gestion(): void
    {
        $this->actingAs($this->adherent())
            ->get('/')
            ->assertDontSee(route('gestion.livres.index'));
    }
}
```

Idées : un livre rendu n'est plus « en cours » ; un visiteur ne peut pas ouvrir `/mes-emprunts` ;
la fiche d'un livre emprunté n'affiche pas le bouton « Emprunter » ; l'API ne renvoie pas plus de 20 livres par page.
**Un bon test échoue quand le code est faux** : cassez volontairement le code, vérifiez que votre test passe au rouge.

✅ `php artisan test --group=seance-08 --filter=m2`

### Mission 8.3 🟢 — Le style du code

```bash
./vendor/bin/pint --test     # liste les écarts
./vendor/bin/pint            # les corrige
git diff                     # relisez ce qui a changé
```
Le workflow **Qualité** de l'onglet *Actions* refait cette vérification à chaque push.

### Mission 8.4 🟡 — Mise en ligne

**Attendu :** une URL publique où l'application tourne en **production**, communiquée dans
`PROGRESSION.md`.

Hébergeur proposé : **alwaysdata**, offre gratuite (PHP, MySQL, accès SSH, sans carte bancaire —
vérifiez les conditions au moment de vous inscrire). La démarche vaut pour tout hébergement PHP :

1. Créez une base **MySQL** et un utilisateur dans l'interface de l'hébergeur.
2. En SSH : `git clone` de **votre** dépôt, puis dans `projetLaravel/` :
   ```bash
   composer install --no-dev --optimize-autoloader
   cp .env.example .env && php artisan key:generate
   ```
3. Dans `.env` de **production** :
   `APP_ENV=production`, `APP_DEBUG=false`, `APP_URL=https://…`, et les `DB_*` de l'hébergeur.
4. ```bash
   php artisan migrate --force
   php artisan db:seed --class=CategorieSeeder --force   # pas les comptes de démo !
   php artisan config:cache && php artisan route:cache && php artisan view:cache
   ```
5. Faites pointer le site sur le dossier **`projetLaravel/public`** (et rien d'autre : `.env` ne doit pas être accessible).
6. Créez le compte administrateur avec `php artisan tinker`, et un mot de passe solide.

**Recette** : connexion, ajout d'un livre, emprunt, retour, `/api/livres`. Une URL inconnue doit
afficher votre page 404 — **pas** une trace d'erreur Laravel.

> Pas d'hébergement possible ? Montrez la recette dans votre Codespace, port 8000 rendu **public**
> (clic droit → *Port Visibility*) le temps de la démonstration.

### Mission 8.5 🟢 — Fiche de réalisation E5

Complétez [e5/FICHE-REALISATION.md](../e5/FICHE-REALISATION.md) : elle prépare la ligne
« BiblioTech » de votre **tableau de synthèse** (épreuve E5) et vos captures pour le portfolio.

## 4 ✅ Valider

```bash
php artisan test --group=seance-08 && php artisan test
git add -A && git commit -m "fix: faille XSS sur le résumé ; tests et mise en ligne" && git push
```
Dans l'onglet **Actions** : toutes les séances et la qualité au vert.

---

## 📌 Concepts à connaître

1. **XSS** — `{{ }}` échappe le HTML, `{!! !!}` l'exécute : jamais de `{!! !!}` sur une saisie utilisateur.
2. **OWASP Top 10** — la liste de référence des risques web ; chaque séance en a traité un.
3. **Test de non-régression** — un test qui échoue quand on réintroduit le défaut qu'il protège.
4. **Intégration continue** — chaque push relance les tests et le style : le rouge se voit tout de suite.
5. **Configuration de production** — `APP_DEBUG=false`, caches, secrets hors du dépôt, site pointé sur `public/`.

## 📓 Journal de fin de séance (portail)

Le bilan de la situation professionnelle : ce que vous savez faire maintenant que vous ne saviez
pas faire il y a huit séances, et ce que vous mettrez en avant à l'oral.
