<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Reporte de visitas</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 10px; }
        h1 { font-size: 16px; margin-bottom: 5px; }
        p  { margin: 0 0 4px 0; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #000; padding: 3px; }
        th { background: #f0f0f0; }
    </style>
</head>
<body>
    <h1>Reporte de visitas</h1>

    @if(!empty($filters))
        <p>
            <strong>Filtros:</strong>
            @if($filters['from']) Desde {{ $filters['from'] }} @endif
            @if($filters['to']) &nbsp;Hasta {{ $filters['to'] }} @endif
            @if($filters['status']) &nbsp;Estado: {{ $filters['status'] }} @endif
            @if($filters['search']) &nbsp;Búsqueda: "{{ $filters['search'] }}" @endif
        </p>
    @endif

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Cliente</th>
                <th>Técnico</th>
                <th>Supervisor</th>
                <th>Programada</th>
                <th>Estado</th>
            </tr>
        </thead>
        <tbody>
        @foreach($visits as $v)
            <tr>
                <td>{{ $v['id'] }}</td>
                <td>{{ $v['client']['name'] ?? '' }}</td>
                <td>{{ $v['user']['name'] ?? '' }}</td>
                <td>{{ $v['supervisor']['name'] ?? '' }}</td>
                <td>
                    @if(!empty($v['scheduled_at']))
                        {{ \Carbon\Carbon::parse($v['scheduled_at'])->format('d/m/Y H:i') }}
                    @endif
                </td>
                <td>{{ $v['statuss']['name'] }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
</body>
</html>
