<?php
/**
 * Autor: Valentin Andrei Culea
 * Fecha: 07/12/2023
 * Versión 1
 */
?>
@extends('layouts/plantilla')

@section('titulo', 'Lista de usuarios')

@section('contenido')
    <h1>Aquí se muestran todas los usuarios operadores</h1>

    <div class="info-busqueda pt-3">
        <p class="fw-bold">Hay {{ $resultados }} resultados:</p>
    </div>

    <div class="table-responsive">
        <table class="tabla-tareas table table-striped table-hover table-bordered">
            <thead class="table-dark">
                <th>ID</th>
                <th>Usuario</th>
                <th>Ultimo inicio de sesión</th>
                <th>Acciones</th>
            </thead>
            <tbody>
                @foreach ($listaUsuarios as $item)
                    <tr>
                        <td>#{{ $item->id }}</td>
                        <td>{{ $item->usuario }}</td>
                        <td>{{ $item->ultimo_login ?? 'Nunca ha iniciado sesión' }}</td>
                        <td class="text-center">
                            <button class="btn btn-dark" title="Ver tareas asignadas del operario">
                                <a href="{{ route('tareas.search', [
                                        'page'=>1,
                                        'campo1'=>'operario',
                                        'criterio1'=>'like',
                                        'valor1'=>$item->id
                                    ]) }}" 
                                    class="text-decoration-none text-white">
                                    <i class="fa-solid fa-list-check"></i>
                                </a>
                            </button>
                            <button class="btn btn-dark" title="Modificar el usuario">
                                <a href="{{ route('usuarios.edit', $item->id) }}" class="text-decoration-none text-warning">
                                    <i class="fa-solid fa-pen"></i>
                                </a>
                            </button>
                            <button class="btn btn-dark" title="Eliminar el usuario">
                                <a href="{{ route('usuarios.confirmacion', $item->id) }}" class="text-decoration-none text-danger">
                                    <i class="fa-solid fa-trash-can"></i>
                                </a>
                            </button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="paginacion my-3 d-flex gap-3 align-items-center justify-content-center">
        @if ($page > 1)
            <button class="btn btn-dark"><a href="{{ route('usuarios.show', ['page'=>$page-1]) }}" class="text-decoration-none text-white">Anterior</a></button>
        @else
            <button class="btn btn-dark" disabled>Anterior</button>
        @endif

        <p class="m-0">Página actual: <span class="fw-bold">{{ $page }}</span></p>

        @if ($page < $paginas)
            <button class="btn btn-dark"><a href="{{ route('usuarios.show', ['page'=>$page+1]) }}" class="text-decoration-none text-white">Siguiente</a></button>
        @else
            <button class="btn btn-dark" disabled>Siguiente</button>
        @endif
    </div>
@endsection