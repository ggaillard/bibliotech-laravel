@extends('layouts.app', [
    'title' => $livre->titre,
    'breadcrumbs' => [
        ['label' => 'Catalogue', 'url' => route('livres.index')],
        ['label' => $livre->titre, 'url' => null],
    ],
])

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-4 mb-4">
            <div class="card">
                <div class="book-cover" style="height: 360px; --couleur: {{ $livre->categorie->couleur ?? '#6c757d' }}">
                    <div class="book-title" style="font-size: 18px;">{{ $livre->titre }}</div>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h1 class="h3 mb-0">{{ $livre->titre }}</h1>
                    @if ($livre->disponible)
                        <span class="badge bg-success fs-6">Disponible</span>
                    @else
                        <span class="badge bg-warning text-dark fs-6">Emprunté</span>
                    @endif
                </div>

                <div class="card-body">
                    <dl class="row">
                        <dt class="col-sm-3"><i class="fas fa-user"></i> Auteur</dt>
                        <dd class="col-sm-9">{{ $livre->auteur }}</dd>

                        <dt class="col-sm-3"><i class="fas fa-tag"></i> Catégorie</dt>
                        <dd class="col-sm-9">{{ $livre->categorie->nom ?? 'Non classé' }}</dd>

                        <dt class="col-sm-3"><i class="fas fa-calendar"></i> Année</dt>
                        <dd class="col-sm-9">{{ $livre->annee }}</dd>

                        <dt class="col-sm-3"><i class="fas fa-file-alt"></i> Pages</dt>
                        <dd class="col-sm-9">{{ $livre->nb_pages }}</dd>

                        <dt class="col-sm-3"><i class="fas fa-barcode"></i> ISBN</dt>
                        <dd class="col-sm-9"><code>{{ $livre->isbn }}</code></dd>
                    </dl>

                    <h2 class="h5">Résumé</h2>
                    <p>{!! $livre->resume !!}</p>
                </div>
            </div>
        </div>
    </div>

    @if ($livresAssocies->isNotEmpty())
        <h2 class="h4 mt-5">Dans la même catégorie</h2>
        <div class="row">
            @foreach ($livresAssocies as $autre)
                <div class="col-md-4 mb-3"><x-livre-card :livre="$autre" /></div>
            @endforeach
        </div>
    @endif
</div>
@endsection
