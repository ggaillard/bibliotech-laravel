{{-- Template de démarrage pour la vue CREATE --}}
{{-- À utiliser pendant le TP pour gagner du temps --}}

@extends('layouts.app', ['title' => 'Créer une nouvelle ressource'])


@section('content')
<div class="container mt-4">
    <div class="row mb-4">
        <div class="col-md-12">
            <h1>Créer une nouvelle ressource</h1>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            {{-- TODO: Formulaire avec méthode POST vers route store --}}
            <form action="#" method="POST">
                @csrf

                {{-- TODO: Champ 1 --}}
                <div class="mb-3">
                    <label for="champ1" class="form-label">Champ 1 :</label>
                    <input type="text" 
                           class="form-control @error('champ1') is-invalid @enderror" 
                           id="champ1" 
                           name="champ1" 
                           value="{{ old('champ1') }}"
                           required>
                    @error('champ1')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- TODO: Champ 2 --}}
                <div class="mb-3">
                    <label for="champ2" class="form-label">Champ 2 :</label>
                    <input type="text" 
                           class="form-control @error('champ2') is-invalid @enderror" 
                           id="champ2" 
                           name="champ2" 
                           value="{{ old('champ2') }}">
                    @error('champ2')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- TODO: Ajouter les autres champs nécessaires --}}

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        Enregistrer
                    </button>
                    <a href="#" class="btn btn-secondary">
                        Annuler
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
