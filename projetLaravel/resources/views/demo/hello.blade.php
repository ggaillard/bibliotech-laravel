@extends('layouts.app', [
    'title' => 'Démonstration Paramètres',
    'breadcrumbs' => [
        ['label' => 'Démo', 'url' => null],
        ['label' => 'Hello ' . $nom, 'url' => null]
    ]
])

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-info text-white">
                    <h2><i class="fas fa-code"></i> Démonstration : Paramètres de Route</h2>
                </div>
                <div class="card-body">
                    <div class="alert alert-success">
                        <h4><i class="fas fa-hand-wave"></i> Bonjour {{ $nom }} !</h4>
                        <p>Cette page démontre l'utilisation des <strong>paramètres de route</strong> dans Laravel.</p>
                    </div>

                    <h5><i class="fas fa-info-circle"></i> Comment ça fonctionne ?</h5>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <h6>1. Route définie dans <code>routes/web.php</code></h6>
                            <pre class="bg-light p-3 rounded"><code>Route::get('/demo/hello/{nom?}', function ($nom = 'Étudiant') {
    return view('demo.hello', ['nom' => $nom]);
})->name('demo.hello');</code></pre>
                        </div>
                        
                        <div class="col-md-6">
                            <h6>2. Paramètre récupéré dans la vue</h6>
                            <pre class="bg-light p-3 rounded"><code>@verbatim&lt;h4&gt;Bonjour {{ $nom }} !&lt;/h4&gt;@endverbatim</code></pre>
                        </div>
                    </div>

                    <hr>

                    <h5><i class="fas fa-flask"></i> Testez d'autres valeurs :</h5>
                    <div class="row">
                        <div class="col-md-6">
                            <ul class="list-group">
                                <li class="list-group-item">
                                    <a href="{{ route('demo.hello', 'Alice') }}">
                                        /demo/hello/Alice
                                    </a>
                                </li>
                                <li class="list-group-item">
                                    <a href="{{ route('demo.hello', 'Bob') }}">
                                        /demo/hello/Bob
                                    </a>
                                </li>
                                <li class="list-group-item">
                                    <a href="{{ route('demo.hello', 'Charlie') }}">
                                        /demo/hello/Charlie
                                    </a>
                                </li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <div class="alert alert-light">
                                <strong>Paramètre optionnel :</strong><br>
                                Si vous visitez <code>/demo/hello</code> sans paramètre,
                                la valeur par défaut "Étudiant" sera utilisée.
                                <br><br>
                                <a href="{{ route('demo.hello') }}" class="btn btn-sm btn-outline-primary">
                                    Tester sans paramètre
                                </a>
                            </div>
                        </div>
                    </div>

                    <hr>

                    <div class="alert alert-info">
                        <h6><i class="fas fa-graduation-cap"></i> Concept pédagogique :</h6>
                        <p class="mb-0">
                            Cette démonstration illustre comment Laravel capture les segments d'URL
                            et les transmet aux contrôleurs ou closures. C'est la base du routage dynamique !
                        </p>
                    </div>
                </div>
                <div class="card-footer">
                    <a href="{{ route('home') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Retour à l'accueil
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection