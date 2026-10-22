@extends('layouts.app', [
    'title' => 'Catalogue',
    'breadcrumbs' => [['label' => 'Catalogue', 'url' => null]],
])

@section('content')
<div class="container">
    <div class="d-flex flex-wrap justify-content-between align-items-end mb-4 gap-2">
        <div>
            <h1 class="fw-bold mb-1">Catalogue</h1>
            <p class="text-muted mb-0">
                {{ $stats['totalLivres'] }} livres · {{ $stats['livresDisponibles'] }} disponibles ·
                {{ $stats['totalCategories'] }} catégories
            </p>
        </div>
        <a href="{{ route('livres.search') }}" class="btn btn-outline-primary">
            <i class="fas fa-search"></i> Rechercher
        </a>
    </div>

    <div class="row">
        @forelse($livres as $livre)
            <div class="col-md-6 col-lg-4 mb-4"><x-livre-card :livre="$livre" /></div>
        @empty
            <div class="col-12">
                <div class="alert alert-info text-center">Aucun livre pour le moment.</div>
            </div>
        @endforelse
    </div>
</div>
@endsection
