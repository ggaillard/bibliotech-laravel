<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ isset($title) ? $title . ' - ' : '' }}BiblioTech</title>

    {{-- Bootstrap 5 et Font Awesome depuis un CDN : pas de compilation front (npm) nécessaire --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">
    <style>
        .navbar-brand { font-weight: 600; }
        .card { transition: transform .2s; }
        .card:hover { transform: translateY(-3px); }

        /* Couverture dessinée en CSS : la couleur vient de la catégorie (base de données) */
        .book-cover {
            height: 220px;
            display: flex; align-items: center; justify-content: center;
            color: #fff; font-weight: bold; text-align: center; padding: 20px;
            background: linear-gradient(135deg, var(--couleur, #6c757d) 0%, #343a40 140%);
        }
        .book-title { font-size: 16px; line-height: 1.3; text-shadow: 1px 1px 2px rgba(0,0,0,.4); }
    </style>
</head>
<body class="bg-light">

    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="{{ route('home') }}">
                <i class="fas fa-book-open me-2"></i>BiblioTech
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                    aria-label="Ouvrir le menu">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">
                            <i class="fas fa-home"></i> Accueil
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('livres.*') ? 'active' : '' }}" href="{{ route('livres.index') }}">
                            <i class="fas fa-book"></i> Catalogue
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('about') ? 'active' : '' }}" href="{{ route('about') }}">
                            <i class="fas fa-info-circle"></i> À propos
                        </a>
                    </li>
                </ul>

                <form class="d-flex" action="{{ route('livres.search') }}" method="GET" role="search">
                    <input class="form-control me-2" type="search" name="q" aria-label="Rechercher"
                           placeholder="Titre ou auteur…" value="{{ request('q') }}">
                    <button class="btn btn-outline-light" type="submit" aria-label="Lancer la recherche">
                        <i class="fas fa-search"></i>
                    </button>
                </form>
            </div>
        </div>
    </nav>

    @if(isset($breadcrumbs) && count($breadcrumbs) > 0)
    <nav aria-label="Fil d'Ariane" class="bg-white shadow-sm">
        <div class="container">
            <ol class="breadcrumb py-3 mb-0">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Accueil</a></li>
                @foreach($breadcrumbs as $breadcrumb)
                    @if($loop->last || empty($breadcrumb['url']))
                        <li class="breadcrumb-item active" aria-current="page">{{ $breadcrumb['label'] }}</li>
                    @else
                        <li class="breadcrumb-item"><a href="{{ $breadcrumb['url'] }}">{{ $breadcrumb['label'] }}</a></li>
                    @endif
                @endforeach
            </ol>
        </div>
    </nav>
    @endif

    <main class="py-4">
        {{-- Messages « flash » : déposés en session par un contrôleur, affichés une seule fois --}}
        <div class="container">
            @if (session('success'))
                <div class="alert alert-success" role="status">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger" role="alert">{{ session('error') }}</div>
            @endif
        </div>

        @yield('content')
    </main>

    <footer class="bg-dark text-light mt-5">
        <div class="container py-4 d-flex flex-wrap justify-content-between gap-2">
            <span><i class="fas fa-book-open"></i> BiblioTech — médiathèque (projet pédagogique BTS SIO SLAM)</span>
            <small>Laravel {{ app()->version() }} · PHP {{ PHP_VERSION }}</small>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>
