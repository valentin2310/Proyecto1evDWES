<?php

use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TareaController;
use App\Http\Controllers\UsuarioController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', HomeController::class)->name('home');

Route::controller(UsuarioController::class)->group(function(){
    Route::get('login', 'index')->name('login.index');
    Route::post('login', 'login')->name('login.login');
    Route::post('logout', 'logout')->name('login.logout');

    Route::get('usuarios', 'show')->name('usuarios.show');

    Route::get('usuarios/create', 'create')->name('usuarios.create');
    Route::post('usuarios/create', 'store')->name('usuarios.store');

    Route::get('usuarios/{usuario}/edit', 'edit')->name('usuarios.edit');
    Route::put('usuarios/{usuario}/edit', 'update')->name('usuarios.update');

    Route::get('usuarios/{usuario}/delete', 'confirmacion')->name('usuarios.confirmacion');
    Route::get('usuarios/{usuario}/resultado', 'delete')->name('usuarios.delete');
});

Route::controller(TareaController::class)->group(function () {
    Route::get('tareas', 'index')->name('tareas.index');

    Route::get('tareas/create', 'create')->name('tareas.create');
    Route::post('tareas/create', 'store')->name('tareas.store');

    Route::get('tareas/search', 'search')->name('tareas.search');

    Route::get('tareas/{tarea}', 'show')->name('tareas.show');

    Route::get('tareas/{tarea}/completar', 'completar')->name('tareas.completar');
    Route::put('tareas/{tarea}/completar', 'completarUpdate')->name('tareas.completarUpdate');

    Route::get('tareas/{tarea}/edit', 'edit')->name('tareas.edit');
    Route::put('tareas/{tarea}', 'update')->name('tareas.update');

    Route::get('tareas/{tarea}/delete', 'confirmacion')->name('tareas.confirmacion');
    Route::get('tareas/{tarea}/resultado', 'delete')->name('tareas.delete');
});



