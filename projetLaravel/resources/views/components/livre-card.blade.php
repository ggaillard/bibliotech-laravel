{{-- Composant « carte livre » : <x-livre-card :livre="$livre" /> --}}
@props(['livre'])

<div class="card h-100 shadow-sm">
    <div class="book-cover" style="--couleur: {{ $livre->categorie->couleur ?? '#6c757d' }}">
        <div class="book-title">{{ $livre->titre }}</div>
    </div>

    <div class="card-body d-flex flex-column">
        <h5 class="card-title">{{ $livre->titre }}</h5>
        <p class="card-text text-muted">{{ $livre->auteur }}</p>

        @if ($livre->categorie)
            <span class="badge mb-2" style="background-color: {{ $livre->categorie->couleur }}; width: fit-content;">
                <i class="{{ $livre->categorie->icone }}"></i> {{ $livre->categorie->nom }}
            </span>
        @endif

        <div class="mt-auto d-flex justify-content-between align-items-center">
            <a href="{{ route('livres.show', $livre->id) }}" class="btn btn-primary btn-sm">
                <i class="fas fa-eye"></i> Voir la fiche
            </a>
            @unless ($livre->disponible)
                <span class="badge bg-warning text-dark">Emprunté</span>
            @endunless
        </div>
    </div>
</div>
