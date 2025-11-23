@extends('layouts.app')

@section('content')
<h1 class="mb-4">Nuevo usuario</h1>

@if($errors->has('general'))
    <div class="alert alert-danger">{{ $errors->first('general') }}</div>
@endif

<form method="POST" action="{{ route('users.store') }}">
    @csrf

    <div class="mb-3">
        <label class="form-label">Nombre</label>
        <input name="name"
               class="form-control"
               value="{{ old('name') }}"
               required>
    </div>

    <div class="mb-3">
        <label class="form-label">Correo</label>
        <input type="email"
               name="email"
               class="form-control @error('email') is-invalid @enderror"
               value="{{ old('email') }}"
               required>
        @error('email')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="mb-3">
        <label class="form-label">Contraseña</label>
        <input type="password"
               name="password"
               class="form-control @error('password') is-invalid @enderror"
               required>
        @error('password')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
        <div class="form-text">Mínimo 6 caracteres.</div>
    </div>

   <div class="mb-3">
            <label class="form-label">Rol</label>
            <select name="role_id" class="form-select" required>
                <option value="">-- Selecciona un rol --</option>

                <option value="1" >Admin</option>
                <option value="2" >Supervisor</option>
                <option value="3" >Tecnico</option>
            </select>

            @error('role_id')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

    <div class="mb-3">
        <label class="form-label">Teléfono</label>
        <input name="phone"
               class="form-control @error('phone') is-invalid @enderror"
               value="{{ old('phone') }}">
        @error('phone')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="form-check mb-3">
        <input class="form-check-input"
               type="checkbox"
               name="active"
               id="active"
               {{ old('active', true) ? 'checked' : '' }}>
        <label class="form-check-label" for="active">
            Usuario activo
        </label>
    </div>

    <button class="btn btn-primary">Guardar</button>
    <a href="{{ route('users.index') }}" class="btn btn-secondary">Cancelar</a>
</form>
@endsection
