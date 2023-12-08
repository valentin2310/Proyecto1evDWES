<?php
/**
 * Autor: Valentin Andrei Culea
 * Fecha: 07/12/2023
 * Versión 1
 */
?>
@extends('layouts.plantilla')

@section('titulo', 'Editar la tarea'. $tarea->id)

@section('contenido')

<div class="d-flex flex-column align-items-center gap-3">

    <form action="{{ route('tareas.update', $tarea->id) }}" method="POST" class="form bg-dark text-white p-4 rounded">
        @method("put")
    
        <fieldset>
            <legend class="text-azul">Datos de la tarea</legend>
            <div class="row m-0 p-2">
                <div class="col-md-5 mb-3">
                    <label class="form-label">Estado:</label>
                    <select class="form-select" disabled>
                        @foreach ($optionsEstado as $key => $value)
                        <option value="{{ $key }}"
                            @if ($key == $tarea->estado)
                                selected
                            @endif
                        >
                            {{ $value }}
                        </option>
                        @endforeach
                    </select>
                    <!-- Campo oculto con el valor del estado -->
                    <input type="hidden" name="estado" value="B">
                </div>
                <div class="col-md-7 mb-3">
                    <label class="form-label">Fecha realización:</label>
                    <input type="text" name="fecha_realizacion" class="form-control" 
                        value="{{ isset($request) ? $request['fecha_realizacion'] : $tarea->fecha_realizacion }}"
                    >
                    @if (isset($gestor_err) && $gestor_err->hayError('fecha_realizacion'))
                        <small class='text-danger float-end'><i class='fa-solid fa-circle-exclamation'></i> {{ $gestor_err->getMensajeError('fecha_realizacion') }}</small>
                    @endif
                </div>
                <div class="col-md-5 mb-3">
                    <label class="form-label">Operario:</label>
                    <select class="form-select" name="operario">
                        @foreach ($listaOperarios as $item)
                            <option value="{{ $item->id }}" @selected((isset($request) && $item->id == $request['operario']) || $item->id == $tarea->operario)>
                                {{ $item->usuario }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-7 mb-3">
                    <label class="form-label">NIF facturador</label>
                    <input type="text" name="nif" class="form-control"
                        value="{{ isset($request) ? $request['nif'] : $tarea->nif }}"
                    >
                    @if (isset($gestor_err) && $gestor_err->hayError('nif'))
                        <small class='text-danger float-end'><i class='fa-solid fa-circle-exclamation'></i> {{ $gestor_err->getMensajeError('nif') }}</small>
                    @endif
                </div>
                <div class="col-md-12 mb-3">
                    <label class="form-label">Descripción</label>
                    <textarea class="form-control" name="descripcion" cols="30" rows="5" placeholder="Una descripcion sobre la tarea...">{{ isset($request) ? $request['descripcion'] : $tarea->descripcion }}</textarea>
                    @if (isset($gestor_err) && $gestor_err->hayError('descripcion'))
                        <small class='text-danger float-end'><i class='fa-solid fa-circle-exclamation'></i> {{ $gestor_err->getMensajeError('descripcion') }}</small>
                    @endif
                </div>
            </div>
        </fieldset>
    
        <fieldset>
            <legend class="text-azul">Datos contacto</legend>
            <div class="row m-0 p-2">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Persona de contacto:</label>
                    <input type="text" name="contacto" class="form-control"
                        value="{{ isset($request) ? $request['contacto'] : $tarea->contacto }}"
                    >
                    @if (isset($gestor_err) && $gestor_err->hayError('contacto'))
                        <small class='text-danger float-end'><i class='fa-solid fa-circle-exclamation'></i> {{ $gestor_err->getMensajeError('contacto') }}</small>
                    @endif
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Teléfono de contacto:</label>
                    <input type="text" name="telefono" class="form-control"
                        value="{{ isset($request) ? $request['telefono'] : $tarea->telefono }}"
                    >
                    @if (isset($gestor_err) && $gestor_err->hayError('fecha_telefono'))
                        <small class='text-danger float-end'><i class='fa-solid fa-circle-exclamation'></i> {{ $gestor_err->getMensajeError('fecha_telefono') }}</small>
                    @endif
                </div>
                <div class="col-md-12 mb-3">
                    <label class="form-label">Correo de contacto:</label>
                    <input type="email" name="correo" class="form-control"
                        value="{{ isset($request) ? $request['correo'] : $tarea->correo }}"
                    >
                    @if (isset($gestor_err) && $gestor_err->hayError('correo'))
                        <small class='text-danger float-end'><i class='fa-solid fa-circle-exclamation'></i> {{ $gestor_err->getMensajeError('correo') }}</small>
                    @endif
                </div>
            </div>
        </fieldset>
    
        <fieldset>
            <legend class="text-azul">Datos de ubicación</legend>
            <div class="row m-0 p-2">
                <div class="col-md-12 mb-3">
                    <label class="form-label">Dirección:</label>
                    <input type="text" name="direccion" class="form-control" 
                        value="{{ isset($request) ? $request['direccion'] : $tarea->direccion }}"
                    >
                    @if (isset($gestor_err) && $gestor_err->hayError('direccion'))
                        <small class='text-danger float-end'><i class='fa-solid fa-circle-exclamation'></i> {{ $gestor_err->getMensajeError('direccion') }}</small>
                    @endif
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Provincia:</label>
                    <select name="provincia" class="form-select">
                        @foreach ($listaProvincias as $item)
                            <option value="{{ $item->id }}"
                                @if ($item->id == $tarea->provincia)
                                    selected
                                @endif    
                            >
                                {{ $item->provincia }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Población:</label>
                    <input type="text" name="poblacion" id="poblacion" class="form-control"
                        value="{{ isset($request) ? $request['poblacion'] : $tarea->poblacion }}"
                    >
                    @if (isset($gestor_err) && $gestor_err->hayError('poblacion'))
                        <small class='text-danger float-end'><i class='fa-solid fa-circle-exclamation'></i> {{ $gestor_err->getMensajeError('poblacion') }}</small>
                    @endif
                </div>
                <div class="col-md-12 mb-3">
                    <label class="form-label">Código postal:</label>
                    <input type="text" name="cod_postal" class="form-control"
                        value="{{ isset($request) ? $request['cod_postal'] : $tarea->cod_postal }}"
                    >
                    @if (isset($gestor_err) && $gestor_err->hayError('cod_postal'))
                        <small class='text-danger float-end'><i class='fa-solid fa-circle-exclamation'></i> {{ $gestor_err->getMensajeError('cod_postal') }}</small>
                    @endif
                </div>
            </div>
        </fieldset>
    
        <fieldset>
            <legend class="text-azul">Anotaciones</legend>
            <div class="row m-0 p-2">
                <div class="col-md-12 mb-3">
                    <label class="form-label">Anotaciones anteriores:</label>
                    <textarea name="anotaciones_anteriores" cols="30" rows="5" class="form-control">{{ isset($request) ? $request['anotaciones_anteriores'] : $tarea->anotaciones_a }}</textarea>
                </div>
                <div class="col-md-12 mb-3">
                    <label class="form-label">Anotaciones posteriores:</label>
                    <textarea name="anotaciones_posteriores" cols="30" disabled rows="5" class="form-control">{{ isset($request) ? $request['anotaciones_posteriores'] : $tarea->anotaciones_p }}</textarea>
                </div>
            </div>
        </fieldset>
    
        {{-- <fieldset>
            <legend>Fichero resumen</legend>
            <label class="form-label">Fichero:</label>
            <input type="file" name="fichero" disabled class="form-control">
        </fieldset>
    
        <fieldset>
            <legend>Fotos del trabajo realizado</legend>
            <label class="form-label">Subir foto:</label>
            <input type="file" name="foto" disabled class="form-control">
        </fieldset> --}}
        
        <div class="text-center">
            <button type="submit" class="btn btn-primary my-3"><i class="fa-solid fa-floppy-disk me-2"></i>Actualizar tarea</button>
        </div>
    </form>
</div>

    
@endsection