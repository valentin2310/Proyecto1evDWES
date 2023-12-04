<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function __invoke(){
         // Obtiene las cookies del usuario y token, comprueba que son validas y en caso de que no lo sean devulve la vista login
         if(!Usuario::validarUsuario($_COOKIE["id_usuario"] ?? null, $_COOKIE["token_usuario"] ?? null)){
            return redirect()->route('login.index');
        }

        $usuario = Usuario::getUsuario($_COOKIE["id_usuario"]);
        return view('home', compact('usuario'));
    }
}
