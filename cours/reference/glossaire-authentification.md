# 📚 Glossaire - Authentification

> 📎 **Document de référence** repris de la version 2025-2026 et mis à jour (MySQL, Laravel 13, noms en français).
> Les fiches de [cours/](../README.md) et la [documentation officielle](https://laravel.com/docs) font foi.


**Terme** | **Définition** | **Exemple**
---------|--------------|----------
**Authentification** | Processus de vérification de l'identité d'un utilisateur | Login avec email/password
**Autorisation** | Vérification des permissions d'un utilisateur authentifié | Accès au panel admin
**Session** | Mémoire utilisateur entre les requêtes HTTP | User stay logged in
**Token/Session ID** | Identificateur unique généré lors du login | Cookie XSRF-TOKEN
**Hash** | Transformation irréversible d'une donnée | Hash du password
**Bcrypt** | Algorithme de hachage sécurisé avec salt | Password hasher par défaut
**Middleware** | Filtre s'exécutant avant/après les routes | Auth middleware
**Rôle** | Niveau de permissions utilisateur | admin, bibliothécaire, user
**Permission** | Action spécifique autorisée | Can delete user
**CSRF** | Attaque par falsification de requête intersite | Laravel protège automatiquement
**Remember Me** | Garder session active au-delà du navigateur | Cookie persistant
**Logout** | Terminer la session utilisateur | Supprimer session + cookie
**Guard** | Système d'authentification (web, api, etc) | Auth::guard('web')

---

## 🛠️ Commandes Artisan pour Auth

### **Créer controllers d'authentification**
```bash
php artisan make:controller AuthController
php artisan make:controller Auth/LoginController
```

### **Créer modèles et migrations**
```bash
php artisan make:model User -m  # Model + Migration
php artisan migrate              # Exécuter migrations
php artisan migrate:refresh      # Réinitialiser tout
```

### **Créer middleware personnalisé**
```bash
php artisan make:middleware CheckRole
```

### **Tester avec Tinker (REPL)**
```bash
php artisan tinker
>>> User::create(['name' => 'Test', 'email' => 'test@example.com', 'password' => Hash::make('secret')])
>>> Auth::attempt(['email' => 'test@example.com', 'password' => 'secret'])
>>> Auth::user()
```

---

## 💻 Fonctions d'Aide Laravel

### **Vérifier l'authentification**
```php
auth()                      // Récupérer Auth manager
Auth::check()              // true si authentifié
Auth::guest()              // true si non-authentifié
Auth::user()               // Utilisateur courant (ou null)
Auth::id()                 // ID utilisateur courant
auth()->user()->email      // Email utilisateur courant
```

### **Authentification et Logout**
```php
Auth::attempt(['email' => $email, 'password' => $password])  // Login
Auth::login($user)                           // Login user (skip verify)
Auth::logout()                               // Logout
Auth::login($user, $remember = true)         // Login + remember me
```

### **Vérifications de rôles**
```php
Auth::user()->role === 'admin'               // Vérifier rôle
Auth::user()->role === 'bibliothécaire'     // Biblio?
Auth::user()->role === 'user'                // User normal?
```

### **En Templates Blade**
```php
@auth
    <p>Bienvenue {{ Auth::user()->name }}</p>
@endauth

@guest
    <a href="/login">Se connecter</a>
@endguest

@if(Auth::user()?->role === 'admin')
    <a href="/admin">Admin</a>
@endif
```

---

## 🔐 Hash et Password

```php
Hash::make('password')                       // Hasher un mot de passe
Hash::check('password', $hash)               // Vérifier un hash
Hash::needsRehash($hash)                     // Hash obsolète?
```

---

## 📝 Fichiers à Connaître

```
app/
├── Http/
│   ├── Controllers/
│   │   └── AuthController.php          # Contrôleur auth
│   └── Middleware/
│       ├── Authenticate.php            # Middleware auth
│       └── CheckRole.php               # Middleware rôles
├── Models/
│   └── User.php                        # Modèle User
└── Providers/
    └── RouteServiceProvider.php        # Config auth routes

routes/
├── web.php                             # Routes publiques/auth
└── api.php                             # Routes API

database/
└── migrations/
    └── create_users_table.php          # Table users
```

---

## 🔗 Routes Standards Auth

```
GET  /login                 # Afficher formulaire login
POST /login                 # Traiter login
GET  /logout                # Logout
GET  /register              # Afficher formulaire inscrit
POST /register              # Traiter inscription
GET  /forgot-password       # Password reset formulaire
```

---

Besoin de plus d'info ? → Continuez avec [la séance 5](../../seances/seance-05.md)
