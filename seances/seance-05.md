# 5️⃣ Séance 5 — Authentification, rôles, sécurité de la connexion

> **Durée** 2 h, dont 30 min d'**interrogation écrite n° 1** · **Tests** `php artisan test --group=seance-05`
> · **Fiche de cours** [Authentification et autorisation](../cours/05-authentification.md)

## 🎯 Objectifs

- Distinguer **authentification** (qui êtes-vous ?) et **autorisation** (avez-vous le droit ?)
- Connecter et déconnecter un utilisateur avec une **session**
- Protéger un groupe de routes par un **middleware** de rôle
- Résister à deux attaques classiques : l'**énumération de comptes** et la **force brute**

## 🎬 La situation

> **De :** Mme Lefèvre
> **Objet :** URGENT
>
> Mon neveu a tapé `/gestion/livres` sur son téléphone… et il a supprimé *Dune*.
> Seules les bibliothécaires doivent pouvoir gérer le catalogue. Et les adhérents devront bientôt
> avoir un compte pour emprunter.

Trois rôles : **adherent** (par défaut), **bibliothecaire**, **admin**.

---

## 1 ▶️ Lancer

Ouvrez `/gestion/livres` dans une fenêtre de navigation privée : rien ne vous arrête.
C'est ce que nous corrigeons.

## 2 🔍 Comprendre

1. Ouvrez `app/Models/User.php`. Quel *cast* s'applique à `password` ? Que fait-il à l'enregistrement ?
2. Dans phpMyAdmin, table `sessions` : que contient-elle quand vous naviguez sur le site ?
3. Pourquoi ne faut-il **pas** mettre `role` dans les champs remplissables (`Fillable`) du modèle `User` ?

## 3 ✏️ Modifier

### Mission 5.1 🟢 — Rôles, connexion, déconnexion

**Le rôle** :
```bash
php artisan make:migration add_role_to_users_table --table=users
```
```php
// up()
$table->string('role', 20)->default('adherent')->after('email')->index();
// down()
$table->dropIndex(['role']);
$table->dropColumn('role');
```

**Le contrôleur** :
```bash
php artisan make:controller Auth/ConnexionController
```
```php
public function create()
{
    return view('auth.connexion');
}

public function store(Request $request)
{
    $identifiants = $request->validate([
        'email' => ['required', 'email'],
        'password' => ['required', 'string'],
    ]);

    if (! Auth::attempt($identifiants)) {
        return back()->withErrors(['email' => 'Ces identifiants ne correspondent à aucun compte.'])
            ->onlyInput('email');
    }

    $request->session()->regenerate(); // nouvel identifiant de session après connexion

    return redirect()->intended('/');
}

public function destroy(Request $request)
{
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect('/');
}
```

**Les routes** (`/connexion`, `/deconnexion`) :
```php
Route::get('/connexion', [ConnexionController::class, 'create'])->middleware('guest')->name('login');
Route::post('/connexion', [ConnexionController::class, 'store'])->middleware('guest')->name('login.store');
Route::post('/deconnexion', [ConnexionController::class, 'destroy'])->middleware('auth')->name('logout');
```
⚠️ La route d'affichage **doit** s'appeler `login` : c'est là que Laravel renvoie un visiteur non connecté.

**La vue** `resources/views/auth/connexion.blade.php` : un formulaire POST vers `route('login')`,
`@csrf`, champs `email` et `password`. Dans le menu : « Connexion » pour un visiteur (`@guest`),
le nom et un bouton « Déconnexion » (formulaire POST) pour un utilisateur connecté (`@auth`).

✅ `php artisan test --group=seance-05 --filter=m1`

### Mission 5.2 🟢 — Protéger l'espace de gestion

**Un middleware de rôle** :
```bash
php artisan make:middleware VerifierRole
```
```php
public function handle(Request $request, Closure $next, string ...$roles): Response
{
    if (! in_array($request->user()?->role, $roles, true)) {
        abort(403, 'Accès réservé au personnel de la médiathèque.');
    }

    return $next($request);
}
```

Déclarez son alias dans `bootstrap/app.php`, dans `withMiddleware(…)` :
```php
$middleware->alias(['role' => \App\Http\Middleware\VerifierRole::class]);
```

Et protégez le groupe :
```php
Route::prefix('gestion')->name('gestion.')
    ->middleware(['auth', 'role:bibliothecaire,admin'])
    ->group(function () { /* … */ });
```
`auth` d'abord (connecté ? sinon → `/connexion`), puis `role` (bon rôle ? sinon → 403).

**Des comptes de démonstration** dans `DatabaseSeeder` (mot de passe `password`) :
`admin@bibliotech.test`, `bibliothecaire@bibliotech.test`, `adherent@bibliotech.test`.
Le rôle n'étant pas remplissable, affectez-le avec `$user->forceFill(['role' => $role])->save();`.

```bash
php artisan migrate --seed
```
Testez avec les trois comptes. Le lien « Gestion » du menu ne doit apparaître qu'au personnel.

✅ `php artisan test --group=seance-05 --filter=m2`

### Mission 5.3 🟡 — Inscription d'un adhérent

**Attendu :** `/inscription` (route `register`) crée un compte **adhérent** : nom, e-mail unique,
mot de passe d'au moins 8 caractères **confirmé** ; l'adhérent est connecté et renvoyé à l'accueil.
Un petit malin qui ajoute `role=admin` dans le formulaire reste **adhérent**.

> 💡 **Indices.** Règle `confirmed` : le champ `password_confirmation` doit être identique.
> `Password::min(8)` (`use Illuminate\Validation\Rules\Password;`). Ne hachez pas à la main si le
> modèle a le *cast* `hashed`. Et `User::create($request->all())`… est-ce une bonne idée ?

✅ `php artisan test --group=seance-05 --filter=m3`

### Mission 5.4 🟡 — Durcir la connexion

Deux attaques à parer :

1. **Énumération de comptes.** Si « compte inconnu » et « mauvais mot de passe » donnent deux messages
   différents, un attaquant teste des milliers d'e-mails et apprend lesquels sont adhérents.
   La règle `exists:users` dans la validation fait exactement cela. **Attendu :** le même message dans les deux cas.
2. **Force brute.** **Attendu :** au-delà de **5 tentatives par minute**, la connexion répond **429 Too Many Requests**.

> 💡 **Indice.** Laravel fournit un middleware de limitation de débit : cherchez `throttle` dans la
> [documentation du routage](https://laravel.com/docs/routing#rate-limiting).

✅ `php artisan test --group=seance-05`

## 4 ✅ Valider

```bash
php artisan test --group=seance-03 && php artisan test --group=seance-04   # toujours verts ?
php artisan test --group=seance-05 && php artisan test --group=socle
git add -A && git commit -m "feat: comptes, rôles, protection de la gestion" && git push
```

> Les tests des séances 3 et 4 se connectent avec `$this->bibliothecaire()` : s'ils passent au
> rouge, c'est votre protection qui refuse le rôle `bibliothecaire`.

---

## 📌 Concepts à connaître

1. **Authentification / autorisation** — *qui êtes-vous* (connexion) puis *avez-vous le droit* (rôle) : 401/redirection puis 403.
2. **Hachage** — on ne stocke jamais un mot de passe, mais son empreinte (bcrypt) ; on compare avec `Hash::check`.
3. **Middleware** — un filtre placé devant des routes : `auth`, `role:…`, `throttle:…`.
4. **Affectation de masse** — ne jamais rendre `role` remplissable depuis un formulaire.
5. **Énumération et force brute** — message d'erreur identique, nombre de tentatives limité.

## 📓 Journal de fin de séance (portail)

Ce que j'ai fait · ce qui m'a bloqué · ce que j'ai compris aujourd'hui.
