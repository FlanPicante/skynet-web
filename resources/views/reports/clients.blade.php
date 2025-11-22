@extends('layouts.app')

@section('content')
<h1 class="mb-4">
    Reporte de clientes
    <a href="{{ route('reports.clients', ['export' => 'pdf']) }}"
       class="btn btn-sm btn-outline-danger ms-3">
        Descargar PDF
    </a>
</h1>

<div class="card">
    <div class="table-responsive">
        <table class="table table-sm table-striped mb-0 align-middle">
            <thead class="table-light">
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
                    <td>{{ $c['email'] ?? '—' }}</td>
                    <td>{{ $c['phone'] ?? '—' }}</td>
                    <td>{{ $c['lat'] ?? '—' }}</td>
                    <td>{{ $c['lng'] ?? '—' }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
