# 📚 Templates Blade de Démarrage

Ce dossier contient des **templates de démarrage** pour gagner du temps pendant les TP.

## 🎯 Utilisation

Ces templates fournissent la **structure HTML de base** avec :
- ✅ Bootstrap déjà intégré
- ✅ Directives Blade (@extends, @section, @csrf)
- ✅ Gestion des erreurs de validation (@error)
- ✅ Classes CSS Bootstrap
- ✅ Structure responsive

## 📁 Fichiers Disponibles

| Fichier | Description | Quand l'utiliser |
|---------|-------------|------------------|
| `index.blade.php` | Liste avec tableau | Vue INDEX d'un contrôleur resource |
| `show.blade.php` | Détails d'une ressource | Vue SHOW d'un contrôleur resource |
| `create.blade.php` | Formulaire de création | Vue CREATE d'un contrôleur resource |
| `edit.blade.php` | Formulaire de modification | Vue EDIT d'un contrôleur resource |

## 🚀 Comment Utiliser

### Méthode 1 : Copier-Coller (RAPIDE)

```bash
# Copier le template dans votre dossier de vues
mkdir -p resources/views/gestion/livres
cp resources/views/templates/index.blade.php resources/views/gestion/livres/index.blade.php

# Modifier le contenu selon vos besoins
```

### Méthode 2 : S'inspirer (APPRENTISSAGE)

Ouvrir le template, comprendre la structure, puis créer votre propre vue.

## ✏️ Zones à Compléter (marquées TODO)

Tous les templates contiennent des commentaires `{{-- TODO: ... --}}` pour guider :

```blade
{{-- TODO: Ajouter le bouton "Ajouter" avec route vers create --}}
{{-- TODO: Boucle @foreach pour afficher les données --}}
```

## 💡 Conseils

- **Pendant le TP guidé** : Utilisez ces templates pour gagner 15-20 minutes
- **Pendant le TP autonome** : Essayez de créer vos vues sans les templates

## 📖 Exemple Complet (Livres)

Pour créer la vue `index` des livres :

1. Copier `templates/index.blade.php` → `gestion/livres/index.blade.php`
2. Remplacer les TODO :

```blade
{{-- Avant (TODO) --}}
{{-- TODO: Ajouter le bouton "Ajouter" avec route vers create --}}

{{-- Après (complété) --}}
<a href="{{ route('gestion.livres.create') }}" class="btn btn-primary">
    Ajouter un livre
</a>
```

## ⏱️ Gain de Temps Estimé

- **INDEX** : ~10 minutes (structure tableau + pagination)
- **SHOW** : ~5 minutes (structure card)
- **CREATE** : ~8 minutes (formulaire avec validation)
- **EDIT** : ~8 minutes (formulaire pré-rempli)

**TOTAL** : ~30 minutes gagnées sur la création des 4 vues ! ⚡

---

**💡 Astuce** : Ces templates sont fournis pour vous aider à vous concentrer sur la logique Laravel (routes, contrôleurs, validation) plutôt que sur le HTML/CSS.
