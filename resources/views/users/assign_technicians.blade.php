@extends('layouts.app')

@section('content')
<h1 class="mb-4">Asignar técnicos a supervisor</h1>

<p class="mb-2">
    Supervisor: <strong>{{ $supervisor['name'] ?? '' }}</strong>
    ({{ $supervisor['email'] ?? '' }})
</p>

@if($errors->has('general'))
    <div class="alert alert-danger">{{ $errors->first('general') }}</div>
@endif

<form method="POST" action="{{ route('supervisors.technicians.update', $supervisor['id']) }}">
    @csrf

    <div class="mb-3">
        <label class="form-label">Técnicos disponibles</label>

        <div class="border rounded p-3" style="max-height: 300px; overflow-y: auto;">
            @forelse($technicians as $t)
                @php
                    $checked = in_array($t['id'], $assignedIds ?? []);
                @endphp
                <div class="form-check">
                    <input class="form-check-input"
                           type="checkbox"
                           name="technician_ids[]"
                           value="{{ $t['id'] }}"
                           id="tech{{ $t['id'] }}"
                           {{ $checked ? 'checked' : '' }}>
                    <label class="form-check-label" for="tech{{ $t['id'] }}">
                        {{ $t['name'] ?? '' }} ({{ $t['email'] ?? '' }})
                        @if(isset($t['role']['name']))
                            - {{ $t['role']['name'] }}
                        @endif
                    </label>
                </div>
            @empty
                <p class="text-muted mb-0">No hay técnicos activos.</p>
            @endforelse
        </div>
    </div>

    <button class="btn btn-primary">Guardar asignaciones</button>
    <a href="{{ route('users.index') }}" class="btn btn-secondary">Cancelar</a>
</form>
@endsection
