<?php
/**
 * Autor: Valentin Andrei Culea
 * Fecha: 07/12/2023
 * Versión 1
 */

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

/**
 * Crea las rutas de la aplicación asignando un controlador y una función de dicho controlador
 * El controlador se encargará de gestionar los datos haciendo uso de los modelos creados y devolver las vistas necesarias.
 */

 // Crea una ruta con el método get, asignando el valor de la ruta y el nombre del controlador y una función de dicho controlador, 
 // en caso de no indicar la función se usará por defecto la función invoke.
 // Posteriormente se le asigna un nombre a dicha ruta para mejorar la accesibilidad y hacer que cambiar la ruta no suponga ningún inconveniente.
Route::get('/', HomeController::class)->name('home');

// Con el método controller y haciendo uso de la función group, crea un grupo de rutas gestionadas por el mismo controlador, 
// esto para mejorar la logibilidad del código.
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



