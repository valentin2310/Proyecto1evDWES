<?php
/**
 * Autor: Valentin Andrei Culea
 * Fecha: 07/12/2023
 * Versión 1
 */
?>
@extends('layouts.plantilla')

@section('titulo', 'Confirmar la eliminación')

@section('contenido')
    
    <h1>¿Estas seguro que quieres eliminar la tarea {{ $tarea->id }}?</h1>

    <section>
        <h2>Datos de la tarea</h2>
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
                <p><span class="fw-bold">Fecha realización: </span>{{ $tarea->fecha_realizacion }}</p>
            </div>
        </div>
    </section>

    <button class="btn btn-danger fw-bold">
        <a href="{{ route('tareas.delete', $tarea->id) }}" class="text-decoration-none text-white">
            <i class="fa-solid fa-trash-can"></i>
            Eliminar
        </a>
    </button>
    <button class="btn btn-secondary fw-bold">
        <a href="{{ route('home') }}" class="text-decoration-none text-white">
            <i class="fa-solid fa-x"></i>
            Cancelar
        </a>
    </button>

@endsection