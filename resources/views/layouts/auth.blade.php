<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Login - SkyNet Web</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">


    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    @stack('styles')
</head>
<body class="bg-light d-flex align-items-center" style="min-height: 100vh;">

    <main class="container">
        @yield('content')
    </main>

  
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    @stack('scripts')
</body>
</html>
