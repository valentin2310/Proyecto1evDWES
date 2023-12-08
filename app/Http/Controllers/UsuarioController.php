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
    /**
     * Devuelve la página de login
     * @return mixed
     */
    public function index(){
        // Si tiene iniciada la sesión redirige al usuario al inicio.
        if(SeguridadUsuario::validarUsuario()) return redirect()->route('home');

        // Obtiene el último usuario que ha iniciado sesión
        $ultimo_login = Usuario::getUltimoLogin() ?? '';

        // Devuelve la vista con le último usuario que ha iniciado sesión
        return view('login', [
            'ultimo_login'=>$ultimo_login->usuario
        ]);
    }
    /**
     * Comprueba los datos del login y la sesión del usuario
     * @param Request $request
     * @return mixed
     */
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
    /**
     * Cierra la sesión del usuario y lo redirige al inicio
     * @return mixed
     */
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
    /**
     * Muesta la página con la lista de usuarios en formato tabla
     * Solo para administradores
     * @return mixed
     */
    public function show(){
        // Obtiene las cookies del usuario y token, comprueba que son validas y en caso de que no lo sean devulve la vista login
        if(!SeguridadUsuario::validarUsuario()) return redirect()->route('login.index');
        // Obtiene el usuario que está logueado
        $usuario = Usuario::getUsuario(SeguridadUsuario::getIdUsuario());

        // Si el usuario no es admin redirigir al inicio
        if(!$usuario->esAdmin()) return redirect()->route('home');

        // Obtiene la página actual usando los query params
        $page = request()->query('page') ?? 1;
        // Obtiene el resultado de los usuarios de la bd
        $resultado = Usuario::getUsuarios($page);

        // Devuelve la vista con los resultados de la bd
        return view('usuarios/show', [
            'usuario' => $usuario,
            'listaUsuarios'=>$resultado["usuarios"],
            'resultados'=>$resultado["registros"],
            'page'=>$page,
            'paginas'=>$resultado["paginas"]
        ]);
    }
    /**
     * Devuelve la página para crear un usuario. 
     * Solo para administradores
     * @return mixed
     */
    public function create(){
        // Obtiene las cookies del usuario y token, comprueba que son validas y en caso de que no lo sean devulve la vista login
        if(!SeguridadUsuario::validarUsuario()) return redirect()->route('login.index');
        // Obtiene el usuario que está logueado
        $usuario = Usuario::getUsuario(SeguridadUsuario::getIdUsuario());

        // Si el usuario no es admin redirigir al inicio
        if(!$usuario->esAdmin()) return redirect()->route('home');

        // Devuelve la vista
        return view('usuarios/create', compact('usuario'));
    }
    /**
     * Comprueba los datos del formulario y crea un nuevo usuario en la bd.
     * Solo para administradores
     * @param Request $request
     * @return mixed
     */
    public function store(Request $request){
        // Obtiene las cookies del usuario y token, comprueba que son validas y en caso de que no lo sean devulve la vista login
        if(!SeguridadUsuario::validarUsuario()) return redirect()->route('login.index');
        // Obtiene el usuario que está logueado
        $usuario = Usuario::getUsuario(SeguridadUsuario::getIdUsuario());

        // Si el usuario no es admin redirigir al inicio
        if(!$usuario->esAdmin()) return redirect()->route('home');

        // Comprueba los datos enviados
        $validador_err = new ValidarErrores();
        $gestor_err = $validador_err->validarCamposUsuario($request);

        // Si hay errores devolver la vista create con los datos recibidos y los errores
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
    /**
     * Devuelve la página para editar un usuario.
     * Solo para administradores
     * @param int $idUsuairo
     * @return mixed
     */
    public function edit($idUsuario){
        // Obtiene las cookies del usuario y token, comprueba que son validas y en caso de que no lo sean devulve la vista login
        if(!SeguridadUsuario::validarUsuario()) return redirect()->route('login.index');
        $usuario = Usuario::getUsuario(SeguridadUsuario::getIdUsuario());

        // Si el usuario no es admin redirigir al inicio
        if(!$usuario->esAdmin()) return redirect()->route('home');

        // Obtiene el usuario que queremos modificar
        $editUsuario = Usuario::getUsuario($idUsuario);
        // Devuelve la vista con lso datos del usuario a modificar
        return view('usuarios/edit', compact(['usuario', 'editUsuario']));
    }
    /**
     * Comprueba los datos del formulario y actualiza el usuario en la bd
     * Solo para administradores
     * @param Request $request
     * @param int $idUsuario
     * @return mixed
     */
    public function update(Request $request, $idUsuario){
        // Obtiene las cookies del usuario y token, comprueba que son validas y en caso de que no lo sean devulve la vista login
        if(!SeguridadUsuario::validarUsuario()) return redirect()->route('login.index');
        $usuario = Usuario::getUsuario(SeguridadUsuario::getIdUsuario());

        // Si el usuario no es admin redirigir al inicio
        if(!$usuario->esAdmin()) return redirect()->route('home');

        // Comprueba los errores
        $validador_err = new ValidarErrores();
        $gestor_err = $validador_err->validarCamposUsuario($request);

        // Obtiene el usuario a modificar
        $editUsuario = Usuario::getUsuario($idUsuario);

        // Si hay errores devuelve la vista edit con lso datos recibidos y los errores
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

        // Redirige al usuario a la página con todos los usuarios
        return redirect()->route('usuarios.show');
    }
    /**
     * Devuelve la página de confimación para eliminar un usuario
     * Solo para administradores
     * @param int $idUsuario
     * @return mixed
     */
    public function confirmacion($idUsuario){
        // Obtiene las cookies del usuario y token, comprueba que son validas y en caso de que no lo sean devulve la vista login
        if(!SeguridadUsuario::validarUsuario()) return redirect()->route('login.index');
        $usuario = Usuario::getUsuario(SeguridadUsuario::getIdUsuario());

        // Si el usuario no es admin redirigir al inicio
        if(!$usuario->esAdmin()) return redirect()->route('home');

        // Obtiene el usuario que queremos eliminar
        $delUsuario = Usuario::getUsuario($idUsuario);
        // Devuelve la vista con el usuario a eliminar
        return view('usuarios/confirmacion', compact(['usuario', 'delUsuario']));
    }
    /**
     * Elimina el usuario y devuelve la página con el resultado de la bd al eliminarlo
     * Solo para administradores
     * @param int $idUsuario
     * @return mixed
     */
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

        // Devuelve la vista con el resultado
        return view('usuarios/resultado', [
            'usuario'=>$usuario,
            'delUsuario'=>$delUsuario,
            'resultado'=>$resultado
        ]); 
    }

}
