<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Reporte de clientes</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; }
        h1 { font-size: 18px; margin-bottom: 10px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #000; padding: 4px; }
        th { background: #f0f0f0; }
    </style>
</head>
<body>
    <h1>Reporte de clientes</h1>
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Nombre</th>
                <th>Correo</th>
                <th>Teléfono</th>
                <th>Lat</th>
                <th>Lng</th>
            </tr>
        </thead>
        <tbody>
        @foreach($clients as $c)
            <tr>
                <td>{{ $c['id'] }}</td>
                <td>{{ $c['name'] }}</td>
                <td>{{ $c['email'] ?? '' }}</td>
                <td>{{ $c['phone'] ?? '' }}</td>
                <td>{{ $c['lat'] ?? '' }}</td>
                <td>{{ $c['lng'] ?? '' }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
</body>
</html>
