@extends('layouts.app')

@section('content')
<h1 class="mb-4">Dashboard Administrativo</h1>

@if($errors->has('general'))
    <div class="alert alert-danger">{{ $errors->first('general') }}</div>
@endif

<div class="row mb-4">
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <div class="fw-bold small text-muted mb-1">Clientes</div>
                <div class="fs-4">
                    {{ $summary['clients_total'] ?? '–' }}
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <div class="fw-bold small text-muted mb-1">Técnicos</div>
                <div class="fs-4">
                    {{ $summary['technicians_total'] ?? '–' }}
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <div class="fw-bold small text-muted mb-1">Visitas hoy</div>
                <div class="fs-4">
                    {{ $summary['visits_today'] ?? '–' }}
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <div class="fw-bold small text-muted mb-1">Completadas hoy</div>
                <div class="fs-4">
                    {{ $summary['visits_today_done'] ?? '–' }}
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        Últimas visitas
    </div>
    <div class="table-responsive">
        <table class="table table-sm table-hover mb-0 align-middle">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Cliente</th>
                    <th>Técnico</th>
                    <th>Programada</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody>
            @forelse($lastVisits as $v)
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
                    <td>{{ $v['statuss']['name'] ?? '—' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center text-muted py-3">
                        No hay visitas recientes.
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
