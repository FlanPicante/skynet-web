@extends('layouts.app')

@section('content')
<h1 class="mb-3">Clientes</h1>

@if($errors->has('general'))
    <div class="alert alert-danger">{{ $errors->first('general') }}</div>
@endif

<div class="d-flex justify-content-between mb-3">
    <form class="d-flex" method="GET" action="{{ route('clients.index') }}">
        <input type="text"
               name="search"
               value="{{ request('search') }}"
               class="form-control form-control-sm me-2"
               placeholder="Buscar por nombre o correo">

        <button class="btn btn-sm btn-outline-secondary">Buscar</button>
    </form>

   
     <a class="btn btn-sm btn-secondary" href="{{ route('reports.clients') }}">
        Reporte de Clientes

     </a>
      <a href="{{ route('clients.create') }}" class="btn btn-sm btn-primary">
        Nuevo cliente
    </a>
</div>

<table class="table table-striped table-sm align-middle">
    <thead>
    <tr>
        <th>ID</th>
        <th>Nombre</th>
        <th>Correo</th>
        <th>Teléfono</th>
        <th>Activo</th>
        <th>Acciones</th>
    </tr>
    </thead>
    <tbody>
    @forelse($clients as $c)
        <tr>
            <td>{{ $c['id'] ?? '' }}</td>
            <td>{{ $c['name'] ?? '' }}</td>
            <td>{{ $c['email'] ?? '' }}</td>
            <td>{{ $c['phone'] ?? '' }}</td>
            <td>{{ $c['active']==1 ? 'Activado': 'Desactivado' }}</td>
            <td>
                <a href="{{ route('clients.edit', $c['id']) }}" class="btn btn-sm btn-outline-primary">
                    Editar
                </a>
            </td>
        </tr>
    @empty
        <tr><td colspan="6">No hay clientes registrados.</td></tr>
    @endforelse
    </tbody>
</table>
@endsection

