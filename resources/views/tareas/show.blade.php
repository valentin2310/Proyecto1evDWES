@extends('layouts.plantilla')

@section('titulo', 'Tarea '.$tarea->id)

@section('contenido')
    
    <section>
        <h1>Datos de la tarea</h1>
        <div class="row m-0">
            <div class="col-md-6">
                <p><span class="fw-bold">Id factura: </span>{{ $tarea->id }}</p>
            </div>
            <div class="col-md-6">
                <p><span class="fw-bold">Estado: </span>{{ $tarea->estado }}</p>
            </div>
            <div class="col-md-12">
                <p><span class="fw-bold">NIF facturador: </span>{{ $tarea->nif }}</p>
            </div>
            <div class="col-md-12">
                <p><span class="fw-bold">Operario: </span>{{ $tarea->operario ?? 'Ninguno' }}</p>
            </div>
            <div class="col-md-12">
                <p><span class="fw-bold">Descripción: </span>{{ $tarea->descripcion }}</p>
            </div>
            <div class="col-md-6">
                <p><span class="fw-bold">Fecha creación: </span>{{ $tarea->fecha_creacion }}</p>
            </div>
            <div class="col-md-6">
                <p><span class="fw-bold">Fecha realización: </span>{{ $tarea->fecha_realizacion ?? 'Ninguna' }}</p>
            </div>
        </div>
    </section>

    <section>
        <h1>Acciones</h1>
        <button class="btn btn-dark fw-bold">
            <a href="{{ route('tareas.completar', $tarea->id) }}" class="text-decoration-none text-success">
                <i class="fa-solid fa-circle-check"></i>
                Completar tarea
            </a>
        </button>
        <button class="btn btn-dark fw-bold text-white">
            <i class="fa-solid fa-pen-to-square"></i>
            Cambiar estado
        </button>
        <button class="btn btn-dark fw-bold">
            <a href="{{ route('tareas.edit', $tarea->id) }}" class="text-decoration-none text-warning">
                <i class="fa-solid fa-pen"></i>
                Modificar
            </a>
        </button>
        <button class="btn btn-danger fw-bold">
            <a href="{{ route('tareas.confirmacion', $tarea->id) }}" class="text-decoration-none text-white">
                <i class="fa-solid fa-trash-can"></i>
                Eliminar
            </a>
        </button>
    </section>

    <section>
        <h1>Datos contacto</h1>
        <div class="row">
            <div class="col-md-6">
                <p><span class="fw-bold">Persona de contacto: </span>{{ $tarea->contacto }}</p>
            </div>
            <div class="col-md-6">
                <p><span class="fw-bold">Teléfono de contacto: </span>{{ $tarea->telefono }}</p>
            </div>
            <div class="col-md-12">
                <p><span class="fw-bold">Correo de contacto: </span>{{ $tarea->correo }}</p>
            </div>
        </div>
    </section>

    <section>
        <h1>Datos de ubicación</h1>
        <div class="row">
            <div class="col-md-12">
                <p><span class="fw-bold">Dirección: </span>{{ $tarea->direccion }}</p>
            </div>
            <div class="col-md-6">
                <p><span class="fw-bold">Provincia: </span>{{ $tarea->provincia }}</p>
            </div>
            <div class="col-md-6">
                <p><span class="fw-bold">Población: </span>{{ $tarea->poblacion }}</p>
            </div>
            <div class="col-md-12">
                <p><span class="fw-bold">Código postal: </span>{{ $tarea->cod_postal }}</p>
            </div>
        </div>
    </section>

    <section>
        <h1>Anotaciones</h1>
        <div class="row">
            <div class="col-md-12">
                <p><span class="fw-bold">Anotaciones anteriores: </span>{{ $tarea->anotaciones_a }}</p>
            </div>
            <div class="col-md-12">
                <p><span class="fw-bold">Anotaciones posteriores: </span>{{ $tarea->anotaciones_p }}</p>
            </div>
        </div>
    </section>

    <section>
        <h1>Fichero resumen</h1>
        El fichero resumen..
    </section>

    <section>
        <h1>Fotos del trabajo realizado</h1>
        <div class="fotos d-flex flex-wrap gap-3">
            <img src="https://picsum.photos/id/{{$tarea->id}}/200" alt="img random">
            <img src="https://picsum.photos/id/{{$tarea->id + 1}}/200" alt="img random">
            <img src="https://picsum.photos/id/{{$tarea->id + 2}}/200" alt="img random">
            <img src="https://picsum.photos/id/{{$tarea->id + 3}}/200" alt="img random">
            <img src="https://picsum.photos/id/{{$tarea->id + 4}}/200" alt="img random">
            <img src="https://picsum.photos/id/{{$tarea->id + 5}}/200" alt="img random">
            <img src="https://picsum.photos/id/{{$tarea->id + 6}}/200" alt="img random">
            <img src="https://picsum.photos/id/{{$tarea->id + 7}}/200" alt="img random">
            <img src="https://picsum.photos/id/{{$tarea->id + 8}}/200" alt="img random">
        </div>
    </section>
    
@endsection