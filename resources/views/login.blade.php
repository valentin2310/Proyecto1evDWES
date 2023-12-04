@extends('layouts.simple')

@section('titulo', 'Login')

@section('contenido')
    
    <div style="height: 50vh" class="login d-flex justify-content-center align-items-center flex-direction-column">
        <div class="login-container p-5 rounded">
            <h2 class="login-titulo mb-5 text-center">Iniciar sesión</h2>
            @if (isset($gestor_err) && $gestor_err->hayError('login'))
                <small class='text-danger'><i class='fa-solid fa-circle-exclamation'></i> {{ $gestor_err->getMensajeError('login') }}</small>
            @endif
            <form action="{{ route('login.login') }}" method="POST" class="login-cuerpo">
                <div class="mb-3">
                    <label class="form-label">Usuario</label>
                    @if (isset($gestor_err) && $gestor_err->hayError('usuario'))
                        <small class='text-danger float-end'><i class='fa-solid fa-circle-exclamation'></i> {{ $gestor_err->getMensajeError('usuario') }}</small>
                    @endif
                    <input type="text" name="usuario" class="form-control"
                        @isset ($request)
                            value="{{ $request['usuario'] }}"usuario
                        @endisset
                    >
                </div>
                <div class="mb-3">
                    <label class="form-label">Contraseña</label>
                    @if (isset($gestor_err) && $gestor_err->hayError('password'))
                        <small class='text-danger float-end'><i class='fa-solid fa-circle-exclamation'></i> {{ $gestor_err->getMensajeError('password') }}</small>
                    @endif
                    <input type="password" name="password" class="form-control"
                        @isset ($request)
                            value="{{ $request['password'] }}"usuario
                        @endisset
                    >
                </div>
                <div class="text-center pt-3">
                    <button type="submit" class="btn btn-primary fw-bold">Iniciar Sesión</button>
                </div>
            </form>
        </div>
    </div>

@endsection