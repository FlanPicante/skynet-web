@extends('layouts.app')

@section('content')
<h1 class="mb-4">Mis visitas de hoy</h1>

@if($errors->has('general'))
    <div class="alert alert-danger">{{ $errors->first('general') }}</div>
@endif

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<div class="card">
    <div class="table-responsive">
        <table class="table table-sm table-hover mb-0 align-middle">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Cliente</th>
                    <th>Programada</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
            @forelse($visits as $v)
                <tr>
                    <td>{{ $v['id'] }}</td>
                    <td>{{ $v['client']['name'] ?? '—' }}</td>
                    <td>
                        @if(!empty($v['scheduled_at']))
                            {{ \Carbon\Carbon::parse($v['scheduled_at'])->format('d/m/Y H:i') }}
                        @else
                            —
                        @endif
                    </td>
                    <td>{{ $v['statuss']['name'] ?? '—' }}</td>
                    <td>
                        
                        <a href="{{ route('visits.show', $v['id']) }}"
                           class="btn btn-sm btn-outline-secondary">
                            Ver detalle
                        </a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center text-muted py-3">
                        No tienes visitas programadas para hoy.
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
