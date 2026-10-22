# 📚 BiblioTech — Situation professionnelle Laravel · BTS SIO SLAM

<div align="center">

**[🗺️ Parcours](PARCOURS.md)** · **[📋 Ma progression](PROGRESSION.md)** · **[🚑 Dépannage](DEPANNAGE.md)** · **[🎓 Fiches de cours](cours/README.md)**

</div>

---

## 🎯 La situation

La **médiathèque** de la communauté de communes a mis en ligne il y a un an une petite
application Laravel, **BiblioTech** : un catalogue public de livres. Le prestataire est parti,
la médiathèque vous confie l'application.

Vous n'écrivez donc **pas** un projet à partir d'une page blanche : vous **reprenez** une
application qui tourne, vous la comprenez, puis vous la faites évoluer à la demande de la
responsable, Mme Lefèvre — exactement comme en stage ou en entreprise.

| Ce qui existe le premier jour | Ce que vous aurez livré en huit séances |
|---|---|
| Catalogue public, fiche d'un livre, recherche | Pages catégories et contact |
| Tables `livres` et `categories` | Table `auteurs`, reprise des données existantes |
| — | Espace de gestion : ajouter, modifier, supprimer |
| — | Comptes, rôles (adhérent, bibliothécaire, admin) |
| — | Emprunts avec règles de gestion |
| — | API REST pour l'application mobile de la communauté de communes |
| Une faille de sécurité cachée… | Un audit, des tests, une mise en ligne |

## 🚀 Démarrer en 3 minutes

1. En haut de ce dépôt : **Use this template → Create a new repository**, nommé `bibliotech-NOM-Prenom`.
2. Dans **votre** dépôt : **Code → Codespaces → Create codespace on main**.
3. Attendez la fin du message `✅ BiblioTech est prêt` dans le terminal (2 à 4 minutes la première fois).
4. Onglet **PORTS** → port **8000** → 🌐 : l'application s'ouvre. Port **8080** : phpMyAdmin.
5. Dans le terminal :

   ```bash
   php artisan test --group=socle      # 8 tests verts : tout fonctionne
   ```

Ensuite, ouvrez le **[Parcours](PARCOURS.md)** et commencez la séance 1.

## 🧪 Les tests sont votre cahier des charges

Chaque séance a son groupe de tests. Au départ ils sont **rouges** : c'est normal, ils
décrivent ce que vous allez construire. Une mission est terminée quand ses tests passent au vert.

```bash
php artisan test --group=seance-03   # les tests de la séance 3
php artisan test --group=socle       # ce qui fonctionnait déjà : doit TOUJOURS rester vert
```

À chaque `git push`, GitHub relance les mêmes tests sur MySQL : l'onglet **Actions** de votre
dépôt montre une coche verte ou une croix rouge par séance.

## 🗂️ Organisation du dépôt

```
bibliotech/
├── README.md              vous êtes ici
├── PARCOURS.md            la méthode et les 8 séances, dans l'ordre
├── PROGRESSION.md         vos cases à cocher (à committer)
├── DEPANNAGE.md           erreurs fréquentes, classées par message
├── seances/               un guide par séance (2 h)
├── cours/                 fiches de cours + auto-évaluations ; reference/ pour approfondir
├── e5/                    fiche de réalisation pour votre tableau de synthèse E5
├── .devcontainer/         l'environnement Codespaces (Apache, PHP 8.4, Composer)
├── docker-compose.yml     les trois conteneurs : app, mysql, phpmyadmin
└── projetLaravel/         ⬅ l'application BiblioTech (Laravel 13)
    ├── app/  routes/  resources/views/  database/
    └── tests/Feature/     Socle/ · Seance01/ … Seance08/ · Bonus/
```

## 🛠️ Environnement

| Conteneur | Rôle | Accès |
|---|---|---|
| `app` | Apache, PHP 8.4, Composer — VS Code est connecté ici | port **8000** |
| `mysql` | MySQL 8.4, base `bibliotech` | hôte `mysql`, `root` / `root` |
| `phpmyadmin` | Interface d'administration de la base | port **8080** |

Pas besoin de `php artisan serve` : Apache sert déjà `projetLaravel/public`.
Le terminal s'ouvre directement dans `projetLaravel/`.

---

*Projet pédagogique — BTS SIO option SLAM, 2026-2027. La médiathèque et ses personnages sont fictifs.*
