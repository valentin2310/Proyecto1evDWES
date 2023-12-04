<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use App\Models\ValidarErrores;
use Illuminate\Http\Request;

class UsuarioController extends Controller
{
    public function index(){
        return view('login');
    }

    public function login(Request $request){

        // Comprueba los campos del login
        $validador_err = new ValidarErrores();
        $gestor_err = $validador_err->validarLogin($request);

        // Si hay errores devuelve los valores del formulario y el gestor de errores
        if($gestor_err->hayErrores()){
            return view('login', [
                "request"=>$request,
                "gestor_err"=>$gestor_err
            ]);
        }

        // Crea un usuario con los datos del formulario
        $usuario = Usuario::registroToUsuario($request);
        
        // Comprueba que existe un usuario con esa contraseña, en caso afirmativo obtiene el id
        $id = $usuario->validarLogin();

        // Si el usuario no existe devuelve la vista login con el gestor de errores
        if(!$id){
            $gestor_err->addError('login', 'El usuario o la contraseña no son correctos');
            return view('login', [
                "request"=>$request,
                "gestor_err"=>$gestor_err
            ]);
        }

        // Actualizar el token de acceso en la bd
        $tokenGenerado = Usuario::updateToken($id);

        // Crear las cookies para guardar el usuario que se ha logueado
        setcookie('id_usuario', $id, time() + 60 * 60 * 24 * 3); // El tiempo de expiración es de 3 días
        setcookie('token_usuario', $tokenGenerado, time() + 60 * 60 * 24 * 3);

        // Redirige al usuario a la página de inicio
        return redirect()->route('home');
        
    }

    public function logout(){
        // Sobreescribe las cookies anteriores con unas nuevas cookies con la fecha ya expirada
        setcookie('id_usuario', 0, time() - 60);
        setcookie('token_usuario', 0, time() - 60);

        // Redirige al usuario a la página de inicio
        return redirect()->route('home');
    }
}
