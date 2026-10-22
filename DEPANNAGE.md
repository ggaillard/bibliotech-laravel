# 🚑 Dépannage — classé par ce que vous voyez à l'écran

> Cherchez (Ctrl+F) un morceau du message d'erreur. Toujours commencer par **lire la première
> ligne** de l'erreur Laravel : elle dit presque toujours où regarder.
>
> Une erreur absente de cette page ? Ouvrez une *issue* avec le message complet : elle y sera ajoutée.

---

## Au démarrage du Codespace

### Le port 8000 affiche « Forbidden » ou une page Apache
Le projet n'est pas encore prêt (le script d'installation tourne encore) ou il a échoué.
Relancez-le, il ne casse rien :
```bash
bash /workspace/.devcontainer/post-create.sh
```

### `SQLSTATE[HY000] [2002] Connection refused` · `getaddrinfo for mysql failed`
Le serveur MySQL démarre plus lentement que PHP. Attendez 30 secondes et recommencez.
Vérifiez dans `projetLaravel/.env` : `DB_HOST=mysql` (le **nom du conteneur**, pas `127.0.0.1`).

### `No application encryption key has been specified`
```bash
php artisan key:generate
```

### `The stream or file ".../storage/logs/laravel.log" could not be opened: Permission denied`
Apache n'a pas le droit d'écrire dans `storage/`. Depuis `projetLaravel/` :
```bash
chown -R www-data:www-data storage bootstrap/cache && chmod -R ug+rwX storage bootstrap/cache
```

### Le Codespace s'est arrêté (inactivité)
Rouvrez-le depuis GitHub (**Code → Codespaces**). Vos fichiers et votre base sont conservés
tant que le Codespace n'est pas supprimé.

---

## Dans le navigateur

### `419 | Page Expired`
Le formulaire n'a pas de jeton CSRF. Ajoutez `@csrf` juste après la balise `<form …>`.

### `Route [gestion.livres.index] not defined.`
Le nom de route n'existe pas (faute de frappe, ou route pas encore créée).
```bash
php artisan route:list            # toutes les routes et leurs noms
php artisan route:list --path=gestion
```

### `Target class [App\Http\Controllers\…] does not exist.`
Le `use` en haut de `routes/web.php` manque ou le nom de classe est mal écrit (majuscules comprises).

### `Undefined variable $livres`
La vue utilise une variable que le contrôleur ne lui a pas transmise :
`return view('…', ['livres' => $livres]);`

### `Call to a member function … on null` (ex. `nom_complet` on null)
Une relation est vide (un livre sans auteur). Utilisez l'opérateur `?->` :
`{{ $livre->auteur?->nom_complet }}`

### `Unable to locate a class or view for component [livre-card]`
Le composant doit être dans `resources/views/components/livre-card.blade.php`.

### `403 | Accès réservé au personnel de la médiathèque` (à partir de la séance 5)
Vous êtes connecté avec un compte adhérent. Comptes de démonstration (mot de passe `password`) :
`admin@bibliotech.test` · `bibliothecaire@bibliotech.test` · `adherent@bibliotech.test`.

### `429 | Too Many Requests` sur la page de connexion
Cinq tentatives par minute, c'est voulu (séance 5). Attendez une minute.

### `Add [titre] to fillable property to allow mass assignment`
Le champ n'est pas déclaré dans `$fillable` du modèle. Ajoutez-le — **sauf** s'il ne doit jamais
venir d'un formulaire (comme `role`).

---

## Dans la base de données

### `SQLSTATE[42S22]: Column not found: 1054 Unknown column '…'`
- La migration n'a pas été lancée : `php artisan migrate`.
- Ou une migration fait `->after('colonne')` sur une colonne **qui n'existe pas**.
  (C'était le cas l'an dernier : SQLite ignorait l'erreur, MySQL la refuse.)

### `SQLSTATE[42S21]: Column already exists: 1060 Duplicate column name 'auteur_id'`
Une migration a échoué à mi-chemin (séance 2). Sous MySQL, ce qui a été fait avant l'erreur
n'est pas annulé : la colonne existe déjà, la relance bute dessus. Supprimez la colonne dans
phpMyAdmin (ou `php artisan migrate:fresh --seed` si votre code est à jour), corrigez la
migration, relancez.

### `SQLSTATE[23000]: Integrity constraint violation: 1451 Cannot delete or update a parent row`
Vous supprimez une ligne encore référencée par une clé étrangère (une catégorie qui a des livres).
C'est la base qui vous protège : traitez le cas dans le contrôleur (séance 4).

### `SQLSTATE[23000]: … 1062 Duplicate entry '…' for key 'livres_isbn_unique'`
Deux livres avec le même ISBN. Ajoutez la règle `unique` dans la validation (séance 3).

### Je veux repartir d'une base propre
```bash
php artisan migrate:fresh --seed     # ⚠️ efface toutes les données
```

---

## Dans les tests

### `Class "App\Models\Auteur" not found` · `Route [categories.index] not defined.`
La mission correspondante n'est pas encore faite : le test **décrit** ce que vous devez construire.
C'est normal au début de chaque séance.

### `Failed asserting that two strings are identical. Expected 'http://localhost/contact'`
La route existe mais pas avec la bonne URL ou le bon nom. Relisez l'énoncé de la mission.

### Un test du groupe `socle` est devenu rouge
Vous avez cassé quelque chose qui marchait : c'est une **régression**. Annulez votre dernière
modification (`git diff` pour la voir) et cherchez ce qui a changé.

### Tous les tests de gestion passent en 302 au lieu de 200 (séance 5)
C'est la protection par connexion qui fonctionne. Les tests utilisent `$this->bibliothecaire()` :
vérifiez que votre middleware accepte bien le rôle `bibliothecaire`.

---

## Git et GitHub

### `! [rejected] main -> main (fetch first)`
Le dépôt GitHub contient des commits que vous n'avez pas (modification faite sur le site) :
```bash
git pull --rebase && git push
```

### La croix rouge dans l'onglet Actions
Cliquez dessus, puis sur l'étape en rouge : le message est le même que dans votre terminal.
