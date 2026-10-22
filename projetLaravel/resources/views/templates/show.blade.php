{{-- Template de démarrage pour la vue SHOW --}}
{{-- À utiliser pendant le TP pour gagner du temps --}}

@extends('layouts.app', ['title' => 'Détails de la ressource'])


@section('content')
<div class="container mt-4">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1>Détails de la ressource</h1>
        </div>
        <div class="col-md-4 text-end">
            {{-- TODO: Ajouter les boutons "Modifier" et "Retour" --}}
        </div>
    </div>

    {{-- Les messages flash sont déjà affichés par le layout (layouts/app.blade.php) --}}

    <div class="card">
        <div class="card-body">
            {{-- TODO: Afficher les informations de la ressource --}}
            <div class="mb-3">
                <label class="fw-bold">Champ 1 :</label>
                <p>Valeur à afficher</p>
            </div>

            <div class="mb-3">
                <label class="fw-bold">Champ 2 :</label>
                <p>Valeur à afficher</p>
            </div>

            {{-- TODO: Ajouter les autres champs --}}
        </div>
        <div class="card-footer">
            {{-- TODO: Ajouter les boutons d'action (Modifier, Supprimer, Retour) --}}
        </div>
    </div>
</div>
@endsection
