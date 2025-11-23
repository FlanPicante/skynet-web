@extends('layouts.auth')

@section('content')
<div class="min-vh-100 d-flex align-items-center justify-content-center bg-light">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-5">

                <div class="text-center mb-4">
                    
    

                    <h1 class="h5 fw-semibold mt-2 mb-1">SkyNet | Iniciar sesion</h1>
                    <p class="text-muted small mb-0">
                        Accede para gestionar clientes, visitas y reportes.
                    </p>
                </div>

                <div class="card shadow-sm border-0">
                    <div class="card-body p-4">

                        
                        @if($errors->has('general'))
                            <div class="alert alert-danger py-2 small">
                                {{ $errors->first('general') }}
                            </div>
                        @endif

                        <form method="POST" action="{{ route('login') }}">
                            @csrf

                           
                            <div class="mb-3">
                                <label for="email" class="form-label small fw-semibold">
                                    Correo electrónico
                                </label>
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text bg-light">
                                        <i class="bi bi-envelope"></i>
                                    </span>
                                    <input
                                        id="email"
                                        type="email"
                                        name="email"
                                        value="{{ old('email') }}"
                                        class="form-control @error('email') is-invalid @enderror"
                                        placeholder="usuario@skynet.com"
                                        required
                                        autofocus
                                    >
                                    @error('email')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label small fw-semibold">
                                    Contraseña
                                </label>
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text bg-light">
                                        <i class="bi bi-lock"></i>
                                    </span>
                                    <input
                                        id="password"
                                        type="password"
                                        name="password"
                                        class="form-control @error('password') is-invalid @enderror"
                                        placeholder="••••••••"
                                        required
                                    >
                                    @error('password')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>

                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary btn-sm py-2 fw-semibold">
                                    Iniciar sesión
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
