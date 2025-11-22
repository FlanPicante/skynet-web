@extends('layouts.app')

@section('content')
    <h1 class="mb-3">Usuarios</h1>

    <div class="d-flex justify-content-between mb-3">
        <form class="d-flex" method="GET" action="{{ route('users.index') }}">
            <input type="text" name="search" value="{{ request('search') }}" class="form-control form-control-sm me-2"
                placeholder="Buscar por nombre o correo">

            <button class="btn btn-sm btn-outline-secondary">Buscar</button>
        </form>

        <a href="{{ route('users.create') }}" class="btn btn-sm btn-primary">
            Nuevo usuario
        </a>
    </div>

    @if($errors->has('general'))
        <div class="alert alert-danger">{{ $errors->first('general') }}</div>
    @endif

    <table class="table table-striped table-sm align-middle">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Correo</th>
                <th>Rol</th>
                <th>Teléfono</th>
                <th>Activo</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse($users as $u)
                <tr>
                    <td>{{ $u['id'] ?? '' }}</td>
                    <td>{{ $u['name'] ?? '' }}</td>
                    <td>{{ $u['email'] ?? '' }}</td>
                    <td>
                        @if(isset($u['role']['name']))
                            {{ $u['role']['name'] }}
                        @else
                            {{ $u['role_name'] ?? '' }}
                        @endif
                    </td>
                    <td>{{ $u['phone'] ?? '' }}</td>
                    <td>
                        <form method="POST" action="{{ route('users.status', $u['id']) }}" class="status-form d-inline">
                            @csrf
                            @method('PATCH')

                            <div class="form-check form-switch">
                                <input class="form-check-input status-toggle" type="checkbox" {{ ($u['active'] ?? 0) ? 'checked' : '' }}>
                            </div>

                            <input type="hidden" name="active" value="{{ $u['active'] ?? 0 }}">
                        </form>
                    </td>
                    <td>
                        <a href="{{ route('users.edit', $u['id']) }}" class="btn btn-sm btn-outline-primary">
                            Editar
                        </a>

                        <a href="{{ route('users.password.edit', $u['id']) }}" class="btn btn-sm btn-outline-warning mb-1">
                            Contraseña
                        </a>

                        @if(isset($u['role']['name']) && $u['role']['name'] === 'Supervisor' || $u['role']['name'] === 'Admin' )
                            <a href="{{ route('supervisors.technicians.edit', $u['id']) }}" class="btn btn-sm btn-outline-info mb-1">
                                Técnicos
                            </a>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6">No hay usuarios registrados.</td>
                </tr>
            @endforelse
        </tbody>
    </table>


    @if(!empty($meta))
        <div class="small text-muted">
            Página {{ $meta['current_page'] ?? '?' }} de {{ $meta['last_page'] ?? '?' }}
        </div>
    @endif
@endsection

@push('scripts')
<script>
document.querySelectorAll('.status-form').forEach(function (form) {
    const toggle = form.querySelector('.status-toggle');
    const hidden = form.querySelector('input[name="active"]');

    if (!toggle || !hidden) return;

    toggle.addEventListener('change', function () {
        hidden.value = this.checked ? 1 : 0;
        form.submit();
    });
});
</script>
@endpush
