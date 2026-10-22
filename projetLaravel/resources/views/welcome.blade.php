@extends('layouts.app', ['title' => 'Accueil'])

@section('content')
<div class="container">
    <div class="bg-primary text-white rounded-3 p-5 text-center mb-5">
        <h1 class="display-5 mb-3"><i class="fas fa-book-open"></i> Bienvenue sur BiblioTech</h1>
        <p class="lead mb-4">Le catalogue en ligne de la médiathèque</p>
        <a href="{{ route('livres.index') }}" class="btn btn-light btn-lg">
            <i class="fas fa-search"></i> Explorer le catalogue
        </a>
    </div>

    <div class="row mb-5 text-center">
        <div class="col-md-4 mb-3">
            <div class="card h-100 border-primary"><div class="card-body">
                <i class="fas fa-book fa-2x text-primary mb-2"></i>
                <h2 class="text-primary">{{ $stats['totalLivres'] }}</h2>
                <p class="card-text">Livres au catalogue</p>
            </div></div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card h-100 border-success"><div class="card-body">
                <i class="fas fa-check-circle fa-2x text-success mb-2"></i>
                <h2 class="text-success">{{ $stats['livresDisponibles'] }}</h2>
                <p class="card-text">Disponibles</p>
            </div></div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card h-100 border-info"><div class="card-body">
                <i class="fas fa-tags fa-2x text-info mb-2"></i>
                <h2 class="text-info">{{ $stats['totalCategories'] }}</h2>
                <p class="card-text">Catégories</p>
            </div></div>
        </div>
    </div>

    <h2 class="mb-4"><i class="fas fa-star text-warning"></i> Nouveautés</h2>
    <div class="row">
        @forelse($livresEnVedette as $livre)
            <div class="col-md-4 mb-4"><x-livre-card :livre="$livre" /></div>
        @empty
            <div class="col-12">
                <div class="alert alert-info">
                    Le catalogue est vide. Lancez <code>php artisan migrate:fresh --seed</code> pour le remplir.
                </div>
            </div>
        @endforelse
    </div>
</div>
@endsection
