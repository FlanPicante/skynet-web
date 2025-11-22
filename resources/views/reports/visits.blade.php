@extends('layouts.app')

@section('content')
<h1 class="mb-4">
    Reporte de visitas

    <a href="{{ request()->fullUrlWithQuery(['export' => 'pdf']) }}"
       class="btn btn-sm btn-outline-danger ms-3">
        Descargar PDF
    </a>
</h1>


<div class="card mb-4">
    <div class="card-body">
        <form class="row g-3 align-items-end" method="GET" action="{{ route('reports.visits') }}">
            <div class="col-md-3">
                <label class="form-label">Buscar (cliente/técnico)</label>
                <input type="text"
                       name="search"
                       value="{{ request('search') }}"
                       class="form-control form-control-sm"
                       placeholder="Nombre de cliente o técnico">
            </div>

            <div class="col-md-3">
                <label class="form-label">Estado</label>
                <select name="status" class="form-select form-select-sm">
                    <option value="">-- Todos --</option>
                    @foreach($statusList as $st)
                        <option value="{{ $st['id'] }}"
                            {{ request('status') == ($st['id']) ? 'selected' : '' }}>
                            {{ $st['name'] ?? 'Estado' }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-2">
                <label class="form-label">Desde</label>
                <input type="date"
                       name="from"
                       value="{{ request('from') }}"
                       class="form-control form-control-sm">
            </div>

            <div class="col-md-2">
                <label class="form-label">Hasta</label>
                <input type="date"
                       name="to"
                       value="{{ request('to') }}"
                       class="form-control form-control-sm">
            </div>

            <div class="col-md-2 d-flex gap-2">
                <button class="btn btn-sm btn-primary w-100">
                    Filtrar
                </button>
                <a href="{{ route('reports.visits') }}"
                   class="btn btn-sm btn-outline-secondary w-100">
                    Limpiar
                </a>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table table-sm table-striped mb-0 align-middle">
            <thead class="table-light">
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
            @forelse($visits as $v)
                <tr>
                    <td>{{ $v['id'] }}</td>
                    <td>{{ $v['client']['name'] ?? '—' }}</td>
                    <td>{{ $v['technician']['name'] ?? $v['user']['name'] ?? '—' }}</td>
                    <td>{{ $v['supervisor']['name'] ?? '—' }}</td>
                    <td>
                        @if(!empty($v['scheduled_at']))
                            {{ \Carbon\Carbon::parse($v['scheduled_at'])->format('d/m/Y H:i') }}
                        @else
                            —
                        @endif
                    </td>
                    <td>{{ $v['statuss']['name'] }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center text-muted py-3">
                        No se encontraron visitas con los filtros aplicados.
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
