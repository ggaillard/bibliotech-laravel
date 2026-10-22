# Qualité et mise en production

> **Séance 8** · Lecture 9 min · ▶️ [Faire la séance](../seances/seance-08.md)

## L'idée

Une application **en service** doit pouvoir évoluer sans casser. Trois filets :

1. Des **tests automatisés** — chaque comportement important est vérifié par du code ;
   un défaut corrigé reçoit un test qui empêche son retour (**non-régression**).
2. L'**intégration continue** — à chaque push, GitHub Actions rejoue les tests et vérifie le style.
3. Une **configuration de production** — rien de ce qui aide le développeur ne doit aider un attaquant.

**XSS** (*Cross-Site Scripting*) : une donnée saisie contient `<script>` et s'exécute dans le
navigateur des visiteurs (vol de session, redirection…). Blade échappe tout ce qui passe par `{{ }}` ;
`{!! !!}` affiche **sans** échapper.

## Le code à reconnaître

```php
public function test_le_resume_ne_peut_pas_injecter_de_script(): void
{
    $livre = Livre::factory()->create(['resume' => '<script>alert(1)</script>']);

    $this->get(route('livres.show', $livre->id))
        ->assertDontSee('<script>alert(1)</script>', false)   // false : comparer le HTML brut
        ->assertSee('&lt;script&gt;', false);
}
```
```dotenv
APP_ENV=production
APP_DEBUG=false          # sinon : traces, chemins, variables d'environnement affichés au public
```
```bash
composer install --no-dev --optimize-autoloader
php artisan migrate --force
php artisan config:cache && php artisan route:cache && php artisan view:cache
```

## Les pièges

- **`APP_DEBUG=true` en production** : la page d'erreur affiche le code et la configuration.
- **Le site pointé sur la racine du projet** au lieu de `public/` : `.env` téléchargeable.
- **Lancer les seeders de démonstration en production** : des comptes `password` en ligne.
- **Un test qui ne peut pas échouer** : vérifiez qu'il passe au rouge quand on casse le code.
- **`config:cache` puis modifier `.env`** : il faut relancer `config:cache`.

## ✅ Auto-évaluation

1. Quelle différence entre `{{ $x }}` et `{!! $x !!}` ? Quand le second est-il acceptable ?
2. Qu'est-ce qu'un test de non-régression ? Donnez celui de la faille XSS.
3. Citez trois réglages de production et le risque que chacun évite.
4. Que vérifie le workflow « Qualité » de l'onglet Actions ?
5. Pourquoi le serveur web doit-il pointer sur `projetLaravel/public` ?

<details><summary>Réponses</summary>

1. `{{ }}` échappe le HTML (affiché comme du texte), `{!! !!}` l'interprète. Acceptable seulement pour un HTML produit par le code, jamais pour une saisie.
2. Un test qui échoue si le défaut corrigé réapparaît : créer un livre avec `<script>` dans le résumé et vérifier que la fiche l'affiche échappé.
3. `APP_DEBUG=false` (pas de fuite d'informations) ; `APP_ENV=production` (comportements de dev désactivés, confirmations des commandes risquées) ; site pointé sur `public/` (`.env` inaccessible) ; pas de comptes de démo.
4. Que le code respecte le style Laravel (`pint --test`).
5. Seul `public/` doit être servi : le reste (dont `.env`, `vendor/`, le code) ne doit pas être accessible depuis le web.
</details>
