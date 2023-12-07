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
    
    <h1>¿Estás seguro que quieres eliminar el usuario {{ $delUsuario->id }}?</h1>

    <p>Se eliminará el usuario permanentemente, no habrá forma de recuperarlo más tarde.</p>

    <button class="btn btn-danger fw-bold">
        <a href="{{ route('usuarios.delete', $delUsuario->id) }}" class="text-decoration-none text-white">
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