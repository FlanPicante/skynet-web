@extends('layouts.app')

@section('content')
<h1 class="mb-4">Cambiar contraseña</h1>

<p class="mb-2">
    Usuario: <strong>{{ $user['name'] ?? '' }}</strong> ({{ $user['email'] ?? '' }})
</p>

@if($errors->has('general'))
    <div class="alert alert-danger">{{ $errors->first('general') }}</div>
@endif

<form method="POST" action="{{ route('users.password.update', $user['id']) }}">
    @csrf
    @method('PATCH')

    <div class="mb-3">
        <label class="form-label">Contraseña actual</label>
        <input type="password"
               name="current_password"
               class="form-control @error('current_password') is-invalid @enderror"
               required>
        @error('current_password')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="mb-3">
        <label class="form-label">Nueva contraseña</label>
        <input type="password"
               name="new_password"
               class="form-control @error('new_password') is-invalid @enderror"
               required>
        @error('new_password')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
        <div class="form-text">Mínimo 6 caracteres.</div>
    </div>

    <button class="btn btn-primary">Actualizar contraseña</button>
    <a href="{{ route('users.index') }}" class="btn btn-secondary">Cancelar</a>
</form>
@endsection
