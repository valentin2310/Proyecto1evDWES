@extends('layouts.plantilla')

@section('titulo', 'Crear nueva tarea')

@section('contenido')

<h1>Añadir una tarea</h1>
    
<form action="{{ route('tareas.store') }}" method="POST" class="form-create border border-2 p-4 rounded">

    <fieldset>
        <legend>Datos de la tarea</legend>
        <div class="row m-0">
            <div class="col-md-5 mb-3">
                <label class="form-label">Estado:</label>
                <select class="form-select" disabled>
                    @foreach ($optionsEstado as $key => $value)
                    <option value="{{ $key }}" @selected($key == 'B')>{{ $value }}</option>
                    @endforeach
                </select>
                <!-- Campo oculto con el valor del estado -->
                <input type="hidden" name="estado" value="B">
            </div>
            <div class="col-md-7 mb-3">
                <label class="form-label">Fecha realización:</label>
                @if (isset($gestor_err) && $gestor_err->hayError('fecha_realizacion'))
                    <small class='text-danger float-end'><i class='fa-solid fa-circle-exclamation'></i> {{ $gestor_err->getMensajeError('fecha_realizacion') }}</small>
                @endif
                <input type="text" name="fecha_realizacion" class="form-control"
                    @isset ($request)
                        value="{{ $request['fecha_realizacion'] }}"
                    @endisset
                >
            </div>
            <div class="col-md-5 mb-3">
                <label class="form-label">Operario:</label>
                <select class="form-select" name="operario">
                    @foreach ($listaOperarios as $item)
                    <option value="{{ $item->id }}" @selected(isset($request) && $item->id == $request['operario'])>
                        {{ $item->usuario }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-7 mb-3">
                <label class="form-label">NIF facturador</label>
                @if (isset($gestor_err) && $gestor_err->hayError('nif'))
                    <small class='text-danger float-end'><i class='fa-solid fa-circle-exclamation'></i> {{ $gestor_err->getMensajeError('nif') }}</small>
                @endif
                <input type="text" name="nif" class="form-control"
                    @if (isset($request))
                        value="{{ $request['nif'] }}"
                    @endif
                >
            </div>
            <div class="col-md-12 mb-3">
                <label class="form-label">Descripción</label>
                @if (isset($gestor_err) && $gestor_err->hayError('descripcion'))
                    <small class='text-danger float-end'><i class='fa-solid fa-circle-exclamation'></i> {{ $gestor_err->getMensajeError('descripcion') }}</small>
                @endif
                <textarea class="form-control" name="descripcion" cols="30" rows="5" placeholder="Una descripcion sobre la tarea...">@if (isset($request)){{ $request['descripcion'] }}@endif</textarea>
            </div>
        </div>
    </fieldset>

    <fieldset>
        <legend>Datos contacto</legend>
        <div class="row m-0">
            <div class="col-md-6 mb-3">
                <label class="form-label">Persona de contacto:</label>
                @if (isset($gestor_err) && $gestor_err->hayError('contacto'))
                    <small class='text-danger float-end'><i class='fa-solid fa-circle-exclamation'></i> {{ $gestor_err->getMensajeError('contacto') }}</small>
                @endif
                <input type="text" name="contacto" class="form-control"
                    @isset ($request)
                        value="{{ $request['contacto'] }}"
                    @endisset
                >
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Teléfono de contacto:</label>
                @if (isset($gestor_err) && $gestor_err->hayError('fecha_telefono'))
                    <small class='text-danger float-end'><i class='fa-solid fa-circle-exclamation'></i> {{ $gestor_err->getMensajeError('fecha_telefono') }}</small>
                @endif
                <input type="text" name="telefono" class="form-control"
                    @isset($request)
                        value="{{ $request['telefono'] }}"
                    @endisset
                >
            </div>
            <div class="col-md-12 mb-3">
                <label class="form-label">Correo de contacto:</label>
                @if (isset($gestor_err) && $gestor_err->hayError('correo'))
                    <small class='text-danger float-end'><i class='fa-solid fa-circle-exclamation'></i> {{ $gestor_err->getMensajeError('correo') }}</small>
                @endif
                <input type="text" name="correo" class="form-control"
                    @isset($request)
                        value="{{ $request['correo'] }}"
                    @endisset
                >
            </div>
        </div>
    </fieldset>

    <fieldset>
        <legend>Datos de ubicación</legend>
        <div class="row m-0">
            <div class="col-md-12 mb-3">
                <label class="form-label">Dirección:</label>
                @if (isset($gestor_err) && $gestor_err->hayError('direccion'))
                    <small class='text-danger float-end'><i class='fa-solid fa-circle-exclamation'></i> {{ $gestor_err->getMensajeError('direccion') }}</small>
                @endif
                <input type="text" name="direccion" class="form-control" 
                    @isset($request)
                        value="{{ $request['direccion'] }}"
                    @endisset
                >
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Provincia:</label>
                <select name="provincia" class="form-select">
                    @foreach ($listaProvincias as $item)
                        <option value="{{ $item->id }}">{{ $item->provincia }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Población:</label>
                @if (isset($gestor_err) && $gestor_err->hayError('poblacion'))
                    <small class='text-danger float-end'><i class='fa-solid fa-circle-exclamation'></i> {{ $gestor_err->getMensajeError('poblacion') }}</small>
                @endif
                <input type="text" name="poblacion" id="poblacion" class="form-control"
                    @isset($request)
                        value="{{ $request['poblacion'] }}"
                    @endisset
                >
            </div>
            <div class="col-md-12 mb-3">
                <label class="form-label">Código postal:</label>
                @if (isset($gestor_err) && $gestor_err->hayError('cod_postal'))
                    <small class='text-danger float-end'><i class='fa-solid fa-circle-exclamation'></i> {{ $gestor_err->getMensajeError('cod_postal') }}</small>
                @endif
                <input type="text" name="cod_postal" class="form-control"
                    @isset($request)
                        value="{{ $request['cod_postal'] }}"
                    @endisset
                >
            </div>
        </div>
    </fieldset>

    <fieldset>
        <legend>Anotaciones</legend>
        <div class="row m-0">
            <div class="col-md-12 mb-3">
                <label class="form-label">Anotaciones anteriores:</label>
                <textarea name="anotaciones_anteriores" cols="30" rows="5" class="form-control"></textarea>
            </div>
            {{-- <div class="col-md-12 mb-3">
                <label class="form-label">Anotaciones posteriores:</label>
                <textarea name="anotaciones_posteriores" cols="30" rows="10" class="form-control"></textarea>
            </div> --}}
        </div>
    </fieldset>

    {{-- <fieldset>
        <legend>Fichero resumen</legend>
        <label class="form-label">Fichero:</label>
        <input type="file" name="fichero" class="form-control">
    </fieldset>

    <fieldset>
        <legend>Fotos del trabajo realizado</legend>
        <label class="form-label">Subir foto:</label>
        <input type="file" name="foto" class="form-control">
    </fieldset> --}}
    
    <button type="submit" class="btn btn-primary my-3">Añadir tarea</button>
</form>
    
@endsection