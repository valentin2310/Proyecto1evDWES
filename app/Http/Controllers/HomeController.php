<?php
/**
 * Autor: Valentin Andrei Culea
 * Fecha: 07/12/2023
 * Versión 1
 */

namespace App\Http\Controllers;

use App\Models\SeguridadUsuario;
use App\Models\Usuario;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function __invoke(){
         // Obtiene las cookies del usuario y token, comprueba que son validas y en caso de que no lo sean devulve la vista login
         if(!SeguridadUsuario::validarUsuario()) return redirect()->route('login.index');
         $usuario = Usuario::getUsuario(SeguridadUsuario::getIdUsuario());

        return view('home', compact('usuario'));
    }
}
