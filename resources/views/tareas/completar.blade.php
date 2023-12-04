@extends('layouts.plantilla')

@section('titulo', 'Completar la tarea '.$tarea->id)

@section('contenido')

    <h1>Completar la tarea {{ $tarea->id }}</h1>

    <form action="{{ route('tareas.completarUpdate', $tarea->id) }}" method="POST">

        @method("put")

        <fieldset>
            <legend>Modificar datos</legend>
            <div class="row m-0">
                <div class="col-md-12 mb-3">
                    @foreach ($optionsEstado as $key => $value)
                        <div class="form-chek">
                            <input type="radio" name="estado" value="{{ $key }}" class="form-check-input"
                                @if ($key == 'R') 
                                    checked 
                                @endif
                            >
                            <label class="form-check-label">{{ $value }}</label>
                        </div>
                    @endforeach
                </div>
                <div class="col-md-12 mb-3">
                    <label class="form-label">Fecha realización:</label>
                    @if (isset($gestor_err) && $gestor_err->hayError('fecha_realizacion'))
                        <small class='text-danger float-end'><i class='fa-solid fa-circle-exclamation'></i> {{ $gestor_err->getMensajeError('fecha_realizacion') }}</small>
                    @endif
                    <input type="text" name="fecha_realizacion" class="form-control"
                        value="{{ isset($request) ? $request["fecha_realizacion"] : $tarea->fecha_realizacion }}"
                    >
                </div>
                <div class="col-md-12 mb-3">
                    <label class="form-label">Anotaciones posteriores:</label>
                    <textarea name="anotaciones_posteriores" cols="30" rows="10" class="form-control">{{ isset($request) ? $request['anotaciones_posteriores'] : $tarea->anotaciones_p }}</textarea>
                </div>
            </div>
            <!-- Campos ocultos -->
            <input type="text" value="{{ $tarea->fecha_creacion }}" hidden>
        </fieldset>
        <button type="submit" class="btn btn-success text-white fw-bold">
            <i class="fa-solid fa-floppy-disk"></i>
            Guardar cambios
        </button>
    </form>

@endsection