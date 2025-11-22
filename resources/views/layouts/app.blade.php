@php
    $me = session('user');
    $role = $me['role']?? null; 
@endphp
<!doctype html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <title>SkyNet Web</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">


    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
        integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />

    @stack('styles')
</head>

<body class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
        <div class="container-fluid">
            <a class="navbar-brand" href="{{ route('dashboard') }}">SkyNet Web</a>

            <div class="collapse navbar-collapse">
            <ul class="navbar-nav me-auto">

                
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('dashboard') }}">Dashboard</a>
                </li>

                
                @if(in_array($role, ['Admin', 'Supervisor']))
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('clients.index') }}">Clientes</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('visits.index') }}">Visitas</a>
                    </li>
                    
                @endif

               
                @if($role === 'Admin')
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('users.index') }}">Usuarios</a>
                    </li>
                @endif

                
                @if($role === 'Tecnico')
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('visits.today') }}">Mis visitas de hoy</a>
                    </li>
                @endif

            </ul>

            <span class="navbar-text me-3">
                {{ $me['name'] ?? '' }} ({{ $role ?? '—' }})
            </span>




                    <form method="POST" action="{{ route('logout') }}" class="d-flex">
                        @csrf
                        <button class="btn btn-outline-light btn-sm">Salir</button>
                    </form>

            </div>
        </div>
    </nav>

    <main class="container mb-5">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @yield('content')
    </main>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>


    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

    @stack('scripts')
</body>

</html>