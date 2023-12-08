<?php
/**
 * Autor: Valentin Andrei Culea
 * Fecha: 07/12/2023
 * Versión 1
 */
?>
@extends('layouts.plantilla')

@section('titulo', 'Listado de Tareas')

@section('contenido')
    <h1>Aquí se muestran todas las tareas</h1>

    <div class="info-busqueda pt-3">
        <p class="fw-bold">Hay {{ $resultados }} resultados:</p>
    </div>

    <div class="table-responsive">
        <table class="tabla-tareas table table-striped table-hover table-bordered text-center">
            <thead class="table-dark text-azul">
                <th>ID</th>
                <th>NIF facturador</th>
                <th>Estado</th>
                <th>Operario</th>
                <th>Descripcion</th>
                <th>Persona contacto</th>
                <th>Fecha creación</th>
                <th>Fecha realizacón</th>
                <th>Acciones</th>
            </thead>
            <tbody>
                @foreach ($tareas as $item)
                    <tr>
                        <td>#{{ $item->id }}</td>
                        <td>{{ $item->nif }}</td>
                        <td>{{ $item->estado }}</td>
                        <td>{{ $item->getOperario() ?? 'Sin operario' }}</td>
                        <td>{{ $item->descripcion }}</td>
                        <td>{{ $item->contacto }}</td>
                        <td>{{ $item->fecha_creacion }}</td>
                        <td>{{ $item->fecha_realizacion ?? 'Sin fecha' }}</td>
                        <td class="text-center">
                            <button class="btn btn-dark" title="Ver toda la información de la tarea">
                                <a href="{{ route('tareas.show', $item->id) }}" class="text-decoration-none text-primary">
                                    <i class="fa-solid fa-eye"></i>
                                </a>
                            </button>
                            @if (isset($usuario) && $usuario->esAdmin())
                                <button class="btn btn-dark" title="Modificar la tarea">
                                    <a href="{{ route('tareas.edit', $item->id) }}" class="text-decoration-none text-warning">
                                        <i class="fa-solid fa-pen"></i>
                                    </a>
                                </button>
                                <button class="btn btn-dark" title="Eliminar la tarea">
                                    <a href="{{ route('tareas.confirmacion', $item->id) }}" class="text-decoration-none text-danger">
                                        <i class="fa-solid fa-trash-can"></i>
                                    </a>
                                </button>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="paginacion my-3 d-flex gap-3 align-items-center justify-content-center">
        @if ($page > 1)
            <button class="btn btn-dark"><a href="{{ route('tareas.index', ['page'=>$page-1]) }}" class="text-decoration-none text-white">Anterior</a></button>
        @else
            <button class="btn btn-dark" disabled>Anterior</button>
        @endif

        <p class="m-0">Página actual: <span class="fw-bold text-azul">{{ $page }}</span></p>

        @if ($page < $paginas)
            <button class="btn btn-dark"><a href="{{ route('tareas.index', ['page'=>$page+1]) }}" class="text-decoration-none text-white">Siguiente</a></button>
        @else
            <button class="btn btn-dark" disabled>Siguiente</button>
        @endif
    </div>

@endsection