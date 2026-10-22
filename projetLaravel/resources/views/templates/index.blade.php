{{-- Template de démarrage pour la vue INDEX --}}
{{-- À utiliser pendant le TP pour gagner du temps --}}

@extends('layouts.app', ['title' => 'Liste des ressources'])


@section('content')
<div class="container mt-4">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1>Liste des ressources</h1>
        </div>
        <div class="col-md-4 text-end">
            {{-- TODO: Ajouter le bouton "Ajouter" avec route vers create --}}
        </div>
    </div>

    {{-- Les messages flash sont déjà affichés par le layout (layouts/app.blade.php) --}}

    {{-- TODO: Afficher le tableau avec les données --}}
    <div class="table-responsive">
        <table class="table table-striped table-hover">
            <thead>
                <tr>
                    <th>ID</th>
                    {{-- TODO: Ajouter les colonnes nécessaires --}}
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                {{-- TODO: Boucle @foreach pour afficher les données --}}
                <tr>
                    <td colspan="3" class="text-center text-muted">
                        Aucune donnée à afficher
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    {{-- TODO: Ajouter la pagination --}}
</div>
@endsection
