@extends('layouts.plantilla')

@section('titulo', 'Inicio')

@section('contenido')
    <h1 class="mb-5">Página de inicio</h1>

    <div class="inicio-opciones">
        <p class="py-0 px-4 fs-5">Selecciona una opción: </p>
        <ul>
            <li class="bg-secondary"><a href="/"><i class="fa-solid fa-house text-dark p-2"></i><br>Inicio</a></li>
            <li class="bg-dark"><a href="{{route('tareas.index')}}"><i class="fa-solid fa-eye text-white p-2"></i><br>Ver lista tareas</a></li>
            @if (isset($usuario) && $usuario->esAdmin())
                <li class="bg-dark"><a href="{{route('tareas.create')}}"><i class="fa-solid fa-square-plus text-success p-2"></i><br>Añadir tarea</a></li>
            @endif
            <li class="bg-dark"><a href="{{route('tareas.search')}}"><i class="fa-solid fa-magnifying-glass text-info p-2"></i><br>Buscar o filtrar tareas</a></li>
        </ul>
    </div>
@endsection