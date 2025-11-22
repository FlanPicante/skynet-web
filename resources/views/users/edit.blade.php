@extends('layouts.app')
@php

    $rolesMap = [
        1 => 'Admin',
        2 => 'Supervisor',
        3 => 'Tecnico',
    ];

    $currentRoleId = $user['role_id'] ?? ($user['role']['id'] ?? null);
@endphp
@section('content')
    <h1 class="mb-4">Editar usuario</h1>

    @if($errors->has('general'))
        <div class="alert alert-danger">{{ $errors->first('general') }}</div>
    @endif

    <form method="POST" action="{{ route('users.update', $user['id']) }}">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label class="form-label">Nombre</label>
            <input name="name" class="form-control @error('name') is-invalid @enderror"
                value="{{ old('name', $user['name']) }}" required>
            @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>


        <div class="mb-3">
            <label class="form-label">Rol</label>
            <select name="role_id" class="form-select @error('role_id') is-invalid @enderror" required>
                <option value="">-- Selecciona un rol --</option>

                <option value="1" {{ $currentRoleId === 1 ? 'selected' : '' }}>Admin</option>
                <option value="2" {{ $currentRoleId === 2 ? 'selected' : '' }}>Supervisor</option>
                <option value="3" {{ $currentRoleId === 3 ? 'selected' : '' }}>Tecnico</option>
            </select>

            @error('role_id')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label class="form-label">Teléfono</label>
            <input name="phone" class="form-control @error('phone') is-invalid @enderror"
                value="{{ old('phone', $user['phone'] ?? '') }}">
            @error('phone')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-check mb-3">
            <input class="form-check-input" type="checkbox" name="active" id="active" {{ old('active', $user['active']) ? 'checked' : '' }}>
            <label class="form-check-label" for="active">Usuario activo</label>
        </div>

        <button class="btn btn-primary">Actualizar</button>
        <a href="{{ route('users.index') }}" class="btn btn-secondary">Cancelar</a>
    </form>
@endsection