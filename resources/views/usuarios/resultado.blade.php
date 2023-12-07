@extends('layouts.plantilla')

@section('titulo', 'Resultado acción')

@section('contenido')
    
<h1>Eliminar el Usuario {{ $delUsuario->id }}</h1>

    @if ($resultado)
        <p>Se ha eliminado exitosamente el usuario!!</p>
    @else
        <p>Hubo un error en la eliminación del usuario</p>
    @endif

    <button class="btn btn-dark fw-bold">
        <a href="{{ route('home') }}" class="text-decoration-none text-white">
            <i class="fa-solid fa-house"></i>
            Volver a inicio
        </a>
    </button>

@endsection