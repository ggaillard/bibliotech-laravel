@extends('layouts.app', [
    'title' => 'À propos',
    'breadcrumbs' => [['label' => 'À propos', 'url' => null]],
])

@section('content')
<div class="container">
    <div class="col-lg-8 mx-auto">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h1 class="h3 mb-0"><i class="fas fa-info-circle"></i> À propos de BiblioTech</h1>
            </div>
            <div class="card-body">
                <p>
                    BiblioTech est l'application de la médiathèque : catalogue public, gestion des collections
                    par les bibliothécaires, emprunts des adhérents.
                </p>
                <p>
                    Elle sert de <strong>situation professionnelle</strong> aux étudiants de BTS SIO option SLAM :
                    chacun la fait évoluer séance après séance, comme on reprend le code d'une application en service.
                </p>
                <h2 class="h5">Sous le capot</h2>
                <ul>
                    <li>Architecture MVC avec Laravel {{ app()->version() }}</li>
                    <li>Base de données MySQL, gérée par migrations</li>
                    <li>Vues Blade et Bootstrap 5</li>
                    <li>Environnement GitHub Codespaces identique pour tous</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
