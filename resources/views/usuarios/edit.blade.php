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
<div class="d-flex flex-column align-items-center gap-3">

    <h1>Modificar usuario</h1>
    
    <form action="{{ route('usuarios.update', $editUsuario->id) }}" method="POST" class="form bg-dark text-white p-4 rounded">
        @method('put')
    
        <fieldset>
            <legend class="text-azul">Datos del usuario</legend>
            <div class="row m-0 p-4">
                <div class="col-md-12 mb-3">
                    <div class="row align-items-center">
                        <div class="col-md-4">
                            <label class="form-label"><i class="fa-solid fa-user-tag me-2"></i>ID:</label>
                        </div>
                        <div class="col-auto">
                            <input type="text"  value="{{ $editUsuario->id }}" class="form-control" disabled>
                        </div>
                    </div>
                </div>
                <div class="col-md-12 mb-3">
                    <div class="row align-items-center">
                        <div class="col-md-4">
                            <label class="form-label"><i class="fa-solid fa-user-tag me-2"></i>Tipo de usuario:</label>
                        </div>
                        <div class="col-auto">
                            <input type="text" value="OPERARIO" class="form-control" disabled>
                        </div>
                    </div>
                </div>
                <div class="col-md-12 mb-3">
                    <label class="form-label"><i class="fa-solid fa-user me-2"></i>Usuario:</label>
                    <input type="text" name="usuario" class="form-control"
                        value="{{ isset($request) ? $request['usuario'] : $editUsuario->usuario }}"
                    >
                    @if (isset($gestor_err) && $gestor_err->hayError('usuario'))
                        <small class='text-danger float-end'><i class='fa-solid fa-circle-exclamation'></i> {{ $gestor_err->getMensajeError('usuario') }}</small>
                    @endif
                </div>
                <div class="col-md-12 mb-3">
                    <label class="form-label"><i class="fa-solid fa-key me-2"></i>Contraseña:</label>
                    <input type="password" name="password" class="form-control"
                        value="{{ isset($request) ? $request['password'] : $editUsuario->password }}"
                    >
                    @if (isset($gestor_err) && $gestor_err->hayError('password'))
                        <small class='text-danger float-end'><i class='fa-solid fa-circle-exclamation'></i> {{ $gestor_err->getMensajeError('password') }}</small>
                    @endif
                </div>
                <div class="col-md-12 mb-3">
                    <label class="form-label"><i class="fa-solid fa-key me-2"></i>Repite la contraseña:</label>
                    <input type="password" name="password_2" class="form-control"
                        @isset($request)
                            value="{{ $request["password_2"] }}"
                        @endisset
                    >
                    @if (isset($gestor_err) && $gestor_err->hayError('password_2'))
                        <small class='text-danger float-end'><i class='fa-solid fa-circle-exclamation'></i> {{ $gestor_err->getMensajeError('password_2') }}</small>
                    @endif
                </div>
            </div>
        </fieldset>
        <div class="text-center">
            <button type="submit" class="btn btn-primary my-3"><i class="fa-solid fa-floppy-disk me-2"></i>Guardar usuario</button>
        </div>
    
    </form>
</div>
@endsection