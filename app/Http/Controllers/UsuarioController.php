<?php
/**
 * Autor: Valentin Andrei Culea
 * Fecha: 07/12/2023
 * Versión 1
 */

namespace App\Http\Controllers;

use App\Models\SeguridadUsuario;
use App\Models\Usuario;
use App\Models\ValidarErrores;
use Illuminate\Http\Request;

class UsuarioController extends Controller
{
    public function index(){
        // Si tiene iniciada la sesión redirige al usuario al inicio.
        if(SeguridadUsuario::validarUsuario()) return redirect()->route('home');

        $ultimo_login = Usuario::getUltimoLogin() ?? '';

        return view('login', [
            'ultimo_login'=>$ultimo_login->usuario
        ]);
    }

    public function login(Request $request){
        // Comprueba los campos del login
        $validador_err = new ValidarErrores();
        $gestor_err = $validador_err->validarCamposUsuario($request);

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

        // Si el usuario ha seleccionado el checkbox
        if($request->recuerdame){
            // Crear las cookies para guardar el usuario que se ha logueado
            setcookie('id_usuario', $id, time() + 60 * 60 * 24 * 3); // El tiempo de expiración es de 3 días
            setcookie('token_usuario', $tokenGenerado, time() + 60 * 60 * 24 * 3);
        }
        else{
            // Crear las variables de sesión con el usuario que ha iniciado sesión
            session_start();
            $_SESSION["id_usuario"] = $id;
            $_SESSION["token_usuario"] = $tokenGenerado;
        }


        // Redirige al usuario a la página de inicio
        return redirect()->route('home');
        
    }

    public function logout(){
        // Sobreescribe las cookies anteriores con unas nuevas cookies con la fecha ya expirada
        setcookie('id_usuario', 0, time() - 60);
        setcookie('token_usuario', 0, time() - 60);
        
        // Elimina las variables de sesión
        session_start();
        session_destroy();

        // Redirige al usuario a la página de inicio
        return redirect()->route('home');
    }

    public function show(){
        // Obtiene las cookies del usuario y token, comprueba que son validas y en caso de que no lo sean devulve la vista login
        if(!SeguridadUsuario::validarUsuario()) return redirect()->route('login.index');
        $usuario = Usuario::getUsuario(SeguridadUsuario::getIdUsuario());

        // Si el usuario no es admin redirigir al inicio
        if(!$usuario->esAdmin()) return redirect()->route('home');

        $page = request()->query('page') ?? 1;
        $resultado = Usuario::getUsuarios($page);

        return view('usuarios/show', [
            'usuario' => $usuario,
            'listaUsuarios'=>$resultado["usuarios"],
            'resultados'=>$resultado["registros"],
            'page'=>$page,
            'paginas'=>$resultado["paginas"]
        ]);
    }

    public function create(){
        // Obtiene las cookies del usuario y token, comprueba que son validas y en caso de que no lo sean devulve la vista login
        if(!SeguridadUsuario::validarUsuario()) return redirect()->route('login.index');
        $usuario = Usuario::getUsuario(SeguridadUsuario::getIdUsuario());

        // Si el usuario no es admin redirigir al inicio
        if(!$usuario->esAdmin()) return redirect()->route('home');

        return view('usuarios/create', compact('usuario'));
    }
    
    public function store(Request $request){
        // Obtiene las cookies del usuario y token, comprueba que son validas y en caso de que no lo sean devulve la vista login
        if(!SeguridadUsuario::validarUsuario()) return redirect()->route('login.index');
        $usuario = Usuario::getUsuario(SeguridadUsuario::getIdUsuario());

        // Si el usuario no es admin redirigir al inicio
        if(!$usuario->esAdmin()) return redirect()->route('home');

        // Comprueba los datos enviados
        $validador_err = new ValidarErrores();
        $gestor_err = $validador_err->validarCamposUsuario($request);

        if($gestor_err->hayErrores()){
            return view('usuarios/create', [
                'usuario' => $usuario,
                'request' => $request,
                'gestor_err' => $gestor_err
            ]);
        }

        // Guarda el usuario en al bd
        $newUsuario = Usuario::registroToUsuario($request->all());
        $newUsuario->guardar();
        
        // Redirige a la lista de usuarios
        return redirect()->route('usuarios.show');
    }

    public function edit($idUsuario){
        // Obtiene las cookies del usuario y token, comprueba que son validas y en caso de que no lo sean devulve la vista login
        if(!SeguridadUsuario::validarUsuario()) return redirect()->route('login.index');
        $usuario = Usuario::getUsuario(SeguridadUsuario::getIdUsuario());

        // Si el usuario no es admin redirigir al inicio
        if(!$usuario->esAdmin()) return redirect()->route('home');

        $editUsuario = Usuario::getUsuario($idUsuario);
        return view('usuarios/edit', compact(['usuario', 'editUsuario']));
    }

    public function update(Request $request, $idUsuario){
        // Obtiene las cookies del usuario y token, comprueba que son validas y en caso de que no lo sean devulve la vista login
        if(!SeguridadUsuario::validarUsuario()) return redirect()->route('login.index');
        $usuario = Usuario::getUsuario(SeguridadUsuario::getIdUsuario());

        // Si el usuario no es admin redirigir al inicio
        if(!$usuario->esAdmin()) return redirect()->route('home');

        $validador_err = new ValidarErrores();
        $gestor_err = $validador_err->validarCamposUsuario($request);

        $editUsuario = Usuario::getUsuario($idUsuario);

        if($gestor_err->hayErrores()){
            return view('usuarios/edit', [
                'usuario' => $usuario,
                'editUsuario' => $editUsuario,
                'request' => $request,
                'gestor_err' => $gestor_err
            ]);
        }

        // Modifica los atributos del usuario
        $editUsuario->usuario = $request->input('usuario', $editUsuario->usuario);
        $editUsuario->password = $request->input('password', $editUsuario->password);

        // Modifica el usuario en la bd
        $editUsuario->update();

        return redirect()->route('usuarios.show');
    }

    public function confirmacion($idUsuario){
        // Obtiene las cookies del usuario y token, comprueba que son validas y en caso de que no lo sean devulve la vista login
        if(!SeguridadUsuario::validarUsuario()) return redirect()->route('login.index');
        $usuario = Usuario::getUsuario(SeguridadUsuario::getIdUsuario());

        // Si el usuario no es admin redirigir al inicio
        if(!$usuario->esAdmin()) return redirect()->route('home');

        $delUsuario = Usuario::getUsuario($idUsuario);
        return view('usuarios/confirmacion', compact(['usuario', 'delUsuario']));
    }

    public function delete($idUsuario){
        // Obtiene las cookies del usuario y token, comprueba que son validas y en caso de que no lo sean devulve la vista login
        if(!SeguridadUsuario::validarUsuario()) return redirect()->route('login.index');
        $usuario = Usuario::getUsuario(SeguridadUsuario::getIdUsuario());
        
        // Si el usuario no es admin redirigir al inicio
        if(!$usuario->esAdmin()) return redirect()->route('home');

        // Elimina la tarea de la bd
        $delUsuario = new Usuario();
        $delUsuario->id = $idUsuario;
        $resultado = $delUsuario->eliminar();

        return view('usuarios/resultado', [
            'usuario'=>$usuario,
            'delUsuario'=>$delUsuario,
            'resultado'=>$resultado
        ]); 
    }

}
