@extends('layouts.plantilla')

@section('titulo', 'Listado de Tareas')

@section('contenido')
    <h1>Busca y filtra las tareas</h1>

    <form action="{{ route('tareas.search') }}" method="GET" class="busqueda">
        
        <div class="row m-0 mb-1">
            <div class="col-2">
                Campo
            </div>
            <div class="col-2">
                Criterio
            </div>
            <div class="col-6">
                Valor
            </div>
            <div class="col-2">
                <button type="submit" class="btn btn-success w-100">Buscar</button>
            </div>
        </div>
        <div class="row m-0 mb-1">
            <div class="col-2">
                <select name="campo1" id="camp1o" class="form-select">
                   @foreach ($OPTIONS_CAMPOS as $key => $value)
                        <option value="{{ $key }}"
                            @if (!empty($filtros["campo1"]))
                                {{ ($filtros["campo1"] == $key ? "selected":"") }}
                            @endif
                        >
                            {{ $value }}
                        </option>
                   @endforeach
                </select>
            </div>
            <div class="col-2">
                <select name="criterio1" id="criterio1" class="form-select">
                    @foreach ($OPTIONS_CRITERIOS as $key => $value)
                        <option value="{{ $key }}" 
                            @if (!empty($filtros["criterio1"]))
                                {{ ($filtros["criterio1"] == $key ? "selected":"") }}
                            @endif    
                        >
                            {{ $value }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-8">
                <input type="text" name="valor1" id="valor1" class="form-control" value="{{ $filtros["valor1"]  ?? '' }}">
            </div>
        </div>
        <div class="row m-0 mb-1">
            <div class="col-2">
                <select name="campo2" id="campo2" class="form-select">
                    @foreach ($OPTIONS_CAMPOS as $key => $value)
                        <option value="{{ $key }}" 
                            @if (!empty($filtros["campo2"]))
                                {{ ($filtros["campo2"] == $key ? "selected":"") }}
                            @endif
                        >
                            {{ $value }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-2">
                <select name="criterio2" id="criterio2" class="form-select">
                    @foreach ($OPTIONS_CRITERIOS as $key => $value)
                        <option value="{{ $key }}" 
                            @if (!empty($filtros["criterio2"]))
                                {{ ($filtros["criterio2"] == $key ? "selected":"") }}
                            @endif
                        >
                            {{ $value }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-8">
                <input type="text" name="valor2" id="valor2" class="form-control" value="{{ $filtros["valor2"] ?? '' }}">
            </div>
        </div>
        <div class="row m-0 mb-1">
            <div class="col-2">
                <select name="campo3" id="campo3" class="form-select">
                    @foreach ($OPTIONS_CAMPOS as $key => $value)
                        <option value="{{ $key }}" 
                            @if (!empty($filtros["campo3"]))
                                {{ ($filtros["campo3"] == $key ? "selected":"") }}
                            @endif
                        >
                            {{ $value }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-2">
                <select name="criterio3" id="criterio3" class="form-select">
                    @foreach ($OPTIONS_CRITERIOS as $key => $value)
                        <option value="{{ $key }}" 
                            @if (!empty($filtros["criterio3"]))
                                {{ ($filtros["criterio3"] == $key ? "selected":"") }}
                            @endif
                        >
                            {{ $value }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-8">
                <input type="text" name="valor3" id="valor3" class="form-control" value="{{ $filtros["valor3"] ?? '' }}">
            </div>
        </div>
        <div class="row mx-0 my-3 mb-1">
            <div class="col-12">Ordenar por:</div>
            <div class="col-9">
                <select name="order" class="form-select">
                    @foreach ($OPTIONS_CAMPOS as $key => $value)
                        <option value="{{ $key }}" 
                            @if (!empty($filtros["order"]))
                                {{ ($filtros["order"] == $key ? "selected":"") }}
                            @endif
                        >
                            {{ $value }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-3">
                <select name="order-mode" class="form-select">
                    <option value="ASC"
                        @if (!empty($filtros['order-mode']))
                            {{ ($filtros["order-mode"] == 'ASC' ? "selected":"") }}
                        @endif
                    >
                        Ascendente
                    </option>
                    <option value="DESC"
                        @if (!empty($filtros['order-mode']))
                            {{ ($filtros["order-mode"] == 'DESC' ? "selected":"") }}
                        @endif
                    >
                        Descendente
                    </option>
                </select>
            </div>
        </div>
        <div class="row mx-0 my-3 mb-1">
            <div class="col-12">
                <div class="form-check">
                    <input type="checkbox" name="pendientes" class="form-check-input" @checked(!empty($filtros['pendientes']))>
                    <label class="form-check-label">Mostrar solo las tareas pendientes</label>
                </div>
            </div>
        </div>
    </form>

    <div class="info-busqueda pt-3">
        <p class="fw-bold">Hay {{ $resultados }} resultados:</p>
    </div>

    @if (count($tareas) == 0)
        <p>No hay ningún resultado en su búsqueda, prueba a hacer otra búsqueda con otros filtros.</p>
    @else
        <div class="table-responsive">
            <table class="tabla-tareas table table-striped table-hover table-bordered">
                <thead class="table-dark">
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
    @endif

    <div class="paginacion my-3 d-flex gap-3 align-items-center justify-content-center">
        @if ($page > 1)
            <button class="btn btn-dark">
                <a href="{{ route('tareas.search', 
                    ['page'=>$page-1, 
                    'campo1'=>$filtros['campo1']??'',
                    'campo2'=>$filtros['campo2']??'',
                    'campo3'=>$filtros['campo3']??'',
                    'criterio1'=>$filtros['criterio1']??'',
                    'criterio2'=>$filtros['criterio2']??'',
                    'criterio3'=>$filtros['criterio3']??'',
                    'valor1'=>$filtros['valor1']??'',
                    'valor2'=>$filtros['valor2']??'',
                    'valor3'=>$filtros['valor3']??'',
                    'order'=>$filtros['order']??'',
                    'order-mode'=>$filtros['order-mode']??'',
                    ]) }}" 
                    class="text-decoration-none text-white">Anterior</a>
                </button>
        @else
            <button class="btn btn-dark" disabled>Anterior</button>
        @endif

        <p class="m-0">Página actual: <span class="fw-bold">{{ $page }}</span></p>

        @if ($page < $paginas)
            <button class="btn btn-dark">
                <a href="{{ route('tareas.search', 
                    ['page'=>$page+1, 
                    'campo1'=>$filtros['campo1']??'',
                    'campo2'=>$filtros['campo2']??'',
                    'campo3'=>$filtros['campo3']??'',
                    'criterio1'=>$filtros['criterio1']??'',
                    'criterio2'=>$filtros['criterio2']??'',
                    'criterio3'=>$filtros['criterio3']??'',
                    'valor1'=>$filtros['valor1']??'',
                    'valor2'=>$filtros['valor2']??'',
                    'valor3'=>$filtros['valor3']??'',
                    'order'=>$filtros['order']??'',
                    'order-mode'=>$filtros['order-mode']??'',
                    ]) }}" 
                    class="text-decoration-none text-white">Siguiente</a>
            </button>
        @else
            <button class="btn btn-dark" disabled>Siguiente</button>
        @endif
    </div>

@endsection