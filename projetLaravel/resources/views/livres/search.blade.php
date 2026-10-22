@extends('layouts.app', [
    'title' => 'Recherche',
    'breadcrumbs' => [
        ['label' => 'Catalogue', 'url' => route('livres.index')],
        ['label' => 'Recherche', 'url' => null],
    ],
])

@section('content')
<div class="container">
    <div class="card mb-4">
        <div class="card-body">
            <h1 class="h3"><i class="fas fa-search"></i> Rechercher un livre</h1>
            <form action="{{ route('livres.search') }}" method="GET" class="mt-3">
                <div class="input-group">
                    <input type="text" name="q" class="form-control form-control-lg" aria-label="Titre ou auteur"
                           placeholder="Titre ou auteur…" value="{{ $query }}">
                    <button class="btn btn-primary" type="submit">Rechercher</button>
                </div>
            </form>
        </div>
    </div>

    @if ($query !== '')
        <h2 class="h4 mb-3">
            Résultats pour « {{ $query }} »
            <span class="badge bg-secondary">{{ $total }}</span>
        </h2>
    @endif

    <div class="row">
        @forelse ($livres as $livre)
            <div class="col-md-6 col-lg-4 mb-4"><x-livre-card :livre="$livre" /></div>
        @empty
            <div class="col-12">
                <div class="alert alert-warning">Aucun livre ne correspond à « {{ $query }} ».</div>
            </div>
        @endforelse
    </div>
</div>
@endsection
