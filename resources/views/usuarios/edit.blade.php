<?php
/**
 * Autor: Valentin Andrei Culea
 * Fecha: 07/12/2023
 * Versión 1
 */
?>
@extends('layouts/plantilla')

@section('titulo', 'Modificar usuario '.$editUsuario->id)

@section('contenido')
<h1>Modificar usuario</h1>

<form action="{{ route('usuarios.update', $editUsuario->id) }}" method="POST" class="form-edit border border-2 p-4 rounded">
    @method('put')

    <fieldset>
        <legend>Datos del usuario</legend>
        <div class="row m-0">
            <div class="col-md-12 mb-3">
                <label class="form-label">ID:</label>
                <input type="text" class="form-control" value="{{ $editUsuario->id }}" disabled>
            </div>
            <div class="col-md-12 mb-3">
                <label class="form-label">Usuario:</label>
                @if (isset($gestor_err) && $gestor_err->hayError('usuario'))
                    <small class='text-danger float-end'><i class='fa-solid fa-circle-exclamation'></i> {{ $gestor_err->getMensajeError('usuario') }}</small>
                @endif
                <input type="text" name="usuario" class="form-control"
                    value="{{ isset($request) ? $request['usuario'] : $editUsuario->usuario }}"
                >
            </div>
            <div class="col-md-12 mb-3">
                @if (isset($gestor_err) && $gestor_err->hayError('password'))
                    <small class='text-danger float-end'><i class='fa-solid fa-circle-exclamation'></i> {{ $gestor_err->getMensajeError('password') }}</small>
                @endif
                <label class="form-label">Contraseña:</label>
                <input type="password" name="password" class="form-control"
                    value="{{ isset($request) ? $request['password'] : $editUsuario->password }}"
                >
            </div>
            <div class="col-md-12 mb-3">
                <label class="form-label">Repite la contraseña:</label>
                @if (isset($gestor_err) && $gestor_err->hayError('password_2'))
                    <small class='text-danger float-end'><i class='fa-solid fa-circle-exclamation'></i> {{ $gestor_err->getMensajeError('password_2') }}</small>
                @endif
                <input type="password" name="password_2" class="form-control"
                    @isset($request)
                        value="{{ $request["password_2"] }}"
                    @endisset
                >
            </div>
            <div class="col-md-12 mb-3">
                <label class="form-label">Tipo de usuario:</label>
                <input type="text" value="OPERARIO" class="form-control" disabled>
            </div>
        </div>
    </fieldset>
    <button type="submit" class="btn btn-primary my-3">Guardar usuario</button>

</form>
@endsection