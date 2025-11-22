@extends('layouts.app')




@section('content')

    @if($errors->has('general'))
        <div class="alert alert-danger">{{ $errors->first('general') }}</div>
    @endif


    <h1>Visitas Programadas</h1>

    <div class="card mb-4 mt-3">
        <div class="card-body">
            <form class="row g-3 align-items-end" method="GET" action="{{ route('visits.index') }}">
                <div class="col-md-3">
                    <label class="form-label">Buscar (cliente/técnico)</label>
                    <input type="text" name="search" value="{{ request('search') }}" class="form-control form-control-sm"
                        placeholder="Nombre de cliente o técnico">
                </div>

                <div class="col-md-3">
                    <label class="form-label">Estado</label>
                    <select name="status" class="form-select form-select-sm">

                        @foreach($status as $st)
                            <option value="{{ $st['id'] }}" {{ request('status') === $st['id'] ? 'selected' : '' }}>
                                {{ $st['name'] }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2">
                    <label class="form-label">Desde</label>
                    <input type="date" name="from" value="{{ request('from') }}" class="form-control form-control-sm">
                </div>

                <div class="col-md-2">
                    <label class="form-label">Hasta</label>
                    <input type="date" name="to" value="{{ request('to') }}" class="form-control form-control-sm">
                </div>

                <div class="col-md-2 d-flex gap-2">
                    <button class="btn btn-sm btn-primary w-100">Filtrar</button>
                    <a href="{{ route('visits.index') }}" class="btn btn-sm btn-outline-secondary w-100">
                        Limpiar
                    </a>
                </div>
            </form>
        </div>
    </div>

    <div class="d-flex justify-content-between mb-3">
        <div>
            <a href="{{ route('visits.create') }}" class="btn btn-sm btn-success">
                Nueva visita
            </a>
        </div>
        <div>
            <a class="btn btn-sm btn-secondary" href="{{ route('reports.visits') }}">
                Reporte de Visitas
            </a>
        </div>
        @if(isset($meta))
            <div class="text-muted small">
                Página {{ $meta['current_page'] ?? '' }} de {{ $meta['last_page'] ?? '' }}
                ({{ $meta['total'] ?? 0 }} registros)
            </div>
        @endif
    </div>

    <div class="card">
        <div class="table-responsive">
            <table class="table table-sm table-hover mb-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Cliente</th>
                        <th>Técnico</th>
                        <th>Programada</th>
                        <th>Estado</th>
                        <th>Notas</th>
                        <th style="width: 140px;">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($visits as $v)
                        <tr>
                            <td>{{ $v['id'] }}</td>
                            <td>{{ $v['client']['name'] ?? '—' }}</td>
                            <td>{{ $v['user']['name'] ?? '—' }}</td>
                            <td>
                                @if(!empty($v['scheduled_at']))
                                    {{ \Carbon\Carbon::parse($v['scheduled_at'])->format('d/m/Y H:i') }}
                                @else
                                    —
                                @endif
                            </td>
                            <td>
                                @php
                                    $status = $v['statuss']['id'] ?? '-';
                                    $badgeClass = match ($status) {
                                        '1' => 'bg-secondary',
                                        '2' => 'bg-info',
                                        '3' => 'bg-info',
                                        '4' => 'bg-success',
                                        '5' => 'bg-danger',
                                        default => 'bg-secondary',
                                    };
                                    
                                @endphp
                                <span class="badge {{ $badgeClass }}">{{ $v['statuss']['name']}}</span>
                            </td>
                            <td>
                                <span class="text-truncate d-inline-block" style="max-width: 200px;">
                                    {{ $v['notes'] ?? '' }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('visits.edit', $v['id']) }}" class="btn btn-sm btn-outline-primary mb-1">
                                    Editar
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-3">
                                No hay visitas que coincidan con los filtros.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if(isset($meta) && ($meta['last_page'] ?? 1) > 1)
            <div class="card-footer">
                <nav>
                    <ul class="pagination pagination-sm mb-0">
                        @for($page = 1; $page <= $meta['last_page']; $page++)
                            <li class="page-item {{ $page == $meta['current_page'] ? 'active' : '' }}">
                                <a class="page-link"
                                    href="{{ route('visits.index', array_merge(request()->query(), ['page' => $page])) }}">
                                    {{ $page }}
                                </a>
                            </li>
                        @endfor
                    </ul>
                </nav>
            </div>
        @endif
    </div>
@endsection