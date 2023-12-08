<?php
/**
 * Autor: Valentin Andrei Culea
 * Fecha: 07/12/2023
 * Versión 1
 */

namespace App\Http\Controllers;

use App\Models\Provincia;
use App\Models\SeguridadUsuario;
use App\Models\Tarea;
use App\Models\Usuario;
use App\Models\ValidarErrores;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TareaController extends Controller
{
    /**
     * Devuelve la página con la lista de usuarios operarios en formato tabla
     * @return mixed
     */
    public function index(){
        // Obtiene las cookies del usuario y token, comprueba que son validas y en caso de que no lo sean devulve la vista login
        if(!SeguridadUsuario::validarUsuario()) return redirect()->route('login.index');
        // Obtiene el usuario que está logueado
        $usuario = Usuario::getUsuario(SeguridadUsuario::getIdUsuario());

        // Obtiene la página actual
        $page = request()->query('page') ?? 1;
        $resultado = Tarea::getTareas($page);

        // Devuelve la vista con los resultados de la bd
        return view('tareas/index', [
            'usuario'=>$usuario,
            'tareas'=> $resultado["tareas"],
            "resultados"=> $resultado["registros"],
            "paginas"=> $resultado["paginas"],
            'page'=> $page
        ]);
    }
    /**
     * Devuelve la página para crear un usuario
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

        // Obtiene las variables necesarias para mostrar en la vista
        $operarios = Usuario::getOperarios();
        $provincias = Provincia::getProvincias();
        // Devuelve la vista
        return view('tareas/create', [
            'usuario'=>$usuario,
            "optionsEstado"=>Tarea::OPTIONS_ESTADOS,
            "listaProvincias"=>$provincias,
            "listaOperarios"=>$operarios
        ]);
    }
    /**
     * Inserta una tarea en la bd con los datos del formulario
     * Primero válida que los datos no tengas errores con el validador de errores
     * Luego devuelve la página con la tarea recién creada
     * Solo para administradores
     * @param Request
     * @return mixed
     */
    public function store(Request $request){
         // Obtiene las cookies del usuario y token, comprueba que son validas y en caso de que no lo sean devulve la vista login
        if(!SeguridadUsuario::validarUsuario()) return redirect()->route('login.index');
        // Obtiene el usuario que está logueado
        $usuario = Usuario::getUsuario(SeguridadUsuario::getIdUsuario());

        // Si el usuario no es admin redirigir al inicio
        if(!$usuario->esAdmin()) return redirect()->route('home');

        // Crea un validador de errores
        $validador_err = new ValidarErrores();
        // Comprueba los errores
        $gestor_err = $validador_err->validarCampos($request);

        // Si hay errores devuelve la vista create con los datos necesarios, los que recibimos y los errores
        if($gestor_err->hayErrores()){
            $operarios = Usuario::getOperarios();
            $provincias = Provincia::getProvincias();
            return view('tareas.create', [
                'usuario'=>$usuario,
                'request' => $request->all(),
                'gestor_err' => $gestor_err,
                "optionsEstado"=>Tarea::OPTIONS_ESTADOS,
                "listaProvincias"=>$provincias,
                "listaOperarios"=>$operarios
            ]);
        }
        // Crea una tarea con los datos del formulario
        $tarea = Tarea::registroToTarea($request->all());
        // Guarda la tarea en la bd
        $tarea->guardar();

        // Obtiene el id del registro insertado
        $id = Tarea::getUltimoId();
        // Devuelve la vista con el registro recién insertado
        return redirect()->route('tareas.show', $id);
    }
    /**
     * Muestra la página con la lista de tareas en formato tabla
     * @param int $idTarea
     * @return mixed
     */
    public function show($idTarea){
         // Obtiene las cookies del usuario y token, comprueba que son validas y en caso de que no lo sean devulve la vista login
        if(!SeguridadUsuario::validarUsuario()) return redirect()->route('login.index');
        // Obtiene el usuario que está logueado
        $usuario = Usuario::getUsuario(SeguridadUsuario::getIdUsuario());

        // Obtiene la tarea usando el id
        $tarea = Tarea::getTarea($idTarea);
        // Devuelve la vista con la tarea
        return view('tareas/show', compact(['tarea', 'usuario']));
    }
    /**
     * Devuelve la página con la lista de tareas y un buscador para poder filtrar las tareas por diferentes campos y criterios
     * @return mixed
     */
    public function search(){
         // Obtiene las cookies del usuario y token, comprueba que son validas y en caso de que no lo sean devulve la vista login
        if(!SeguridadUsuario::validarUsuario()) return redirect()->route('login.index');
        
        $filtros = [];
        // Si hemos realizado una busqueda, tenemos que recibir los query params y filtrar las tareas
        if(request()->query()){
            $filtros = request()->all();
        }
        
        // Obtiene el usuario que está logueado
        $usuario = Usuario::getUsuario(SeguridadUsuario::getIdUsuario());
        // Obtiene la pagina actual através de los query params
        $page = request()->query('page') ?? 1;
        // Obtiene el resultado de la búsqueda en la bd
        $resultado = Tarea::getTareas($page, $filtros??null);

        // Devuelve la vista con los datos necesarios y los resultados de la búsqueda.
        return view('tareas/search', [
            'usuario'=>$usuario,
            "tareas"=>$resultado["tareas"],
            "resultados"=> $resultado["registros"],
            "paginas"=> $resultado["paginas"],
            'page'=> $page,
            'filtros'=>$filtros,
            "OPTIONS_CAMPOS"=>Tarea::OPTIONS_CAMPOS,
            "OPTIONS_CRITERIOS"=>Tarea::OPTIONS_CRITERIOS,
        ]);
    }
    /**
     * Devuelve la página para poder modificar los datos de una tarea
     * Solo para administradores
     * @param int $idTarea
     * @return mixed
     */
    public function edit($idTarea){
        // Obtiene las cookies del usuario y token, comprueba que son validas y en caso de que no lo sean devulve la vista login
        if(!SeguridadUsuario::validarUsuario()) return redirect()->route('login.index');
        // Obtiene el usuario que está logueado
        $usuario = Usuario::getUsuario(SeguridadUsuario::getIdUsuario());

         // Si el usuario no es admin redirigir al inicio
        if(!$usuario->esAdmin()) return redirect()->route('home');

        $tarea = Tarea::getTarea($idTarea);
        $operarios = Usuario::getOperarios();
        $provincias = Provincia::getProvincias();
        return view('tareas/edit', [
            'usuario'=>$usuario,
            "tarea"=>$tarea, 
            "optionsEstado"=>Tarea::OPTIONS_ESTADOS,
            "listaProvincias"=>$provincias,
            "listaOperarios"=>$operarios
        ]);
    }
    /**
     * Recibe y comprueba los datos enviados por el formulario, en caso que todo esté correcto actualiza los datos de la tarea
     * Solo para administradores
     * @param Request $request
     * @param int $idTarea
     * @return mixed
     */
    public function update(Request $request, $idTarea){
         // Obtiene las cookies del usuario y token, comprueba que son validas y en caso de que no lo sean devulve la vista login
        if(!SeguridadUsuario::validarUsuario()) return redirect()->route('login.index');
        // Obtiene el usuario que está logueado
        $usuario = Usuario::getUsuario(SeguridadUsuario::getIdUsuario());

        // Si el usuario no es admin redirigir al inicio
        if(!$usuario->esAdmin()) return redirect()->route('home');

        // Crea un validador de errores
        $validador_err = new ValidarErrores();
        // Comprueba los errores
        $gestor_err = $validador_err->validarCampos($request);

        // Obtiene la tarea usando el id
        $tarea = Tarea::getTarea($idTarea);
        
        // En caso de haber errores devuelve la vista edit con los datos necesarios, los que recibimos y los errores
        if($gestor_err->hayErrores()){
            $operarios = Usuario::getOperarios();
            $provincias = Provincia::getProvincias();
            return view('tareas/edit', [
                "usuario"=>$usuario,
                "tarea"=>$tarea,
                "gestor_err"=>$gestor_err,
                "request"=>$request,
                "listaProvincias"=>$provincias,
                "listaOperarios"=>$operarios,
                "optionsEstado"=>Tarea::OPTIONS_ESTADOS
            ]);
        }
        
        // Modifica los atributos de la tarea
        $tarea->nif = $request->input('nif', $tarea->nif);
        $tarea->contacto = $request->input('contacto', $tarea->contacto);
        $tarea->telefono = $request->input('telefono', $tarea->telefono);
        $tarea->telefono = $request->input('telefono', $tarea->telefono);
        $tarea->operario = $request->input('operario', $tarea->operario);
        $tarea->correo = $request->input('correo', $tarea->correo);
        $tarea->direccion = $request->input('direccion', $tarea->direccion);
        $tarea->poblacion = $request->input('poblacion', $tarea->poblacion);
        $tarea->provincia = $request->input('provincia', $tarea->provincia);
        $tarea->cod_postal = $request->input('cod_postal', $tarea->cod_postal);
        $tarea->estado = $request->input('estado', $tarea->estado);
        $tarea->fecha_realizacion = $request->input('fecha_realizacion', $tarea->fecha_realizacion);
        $tarea->anotaciones_p = $request->input('anotaciones_posteriores', $tarea->anotaciones_p);
        $tarea->anotaciones_a = $request->input('anotaciones_anteriores', $tarea->anotaciones_a);
        
        // Actualiza la tarea en la bd
        $tarea->actualizar();
        
        // Devuelve la vista
        return redirect()->route('tareas.show', $idTarea);
    }
    /**
     * Devuelve la página para actualizar el estado de una tarea.
     * Solo para operarios
     * @param int $idTarea
     * @return mixed
     */
    public function completar($idTarea){
        // Obtiene las cookies del usuario y token, comprueba que son validas y en caso de que no lo sean devulve la vista login
        if(!SeguridadUsuario::validarUsuario()) return redirect()->route('login.index');
        // Obtiene el usuario que está logueado
        $usuario = Usuario::getUsuario(SeguridadUsuario::getIdUsuario());

        // Si el usuario es admin redirigir al inicio
        if($usuario->esAdmin()) return redirect()->route('home');

        // Obtiene la tarea usando el id
        $tarea = Tarea::getTarea($idTarea);

        // Devuelve la vista con la tarea
        return view('tareas/completar', [
            'usuario'=>$usuario,
            "tarea"=>$tarea,
            "optionsEstado"=>Tarea::OPTIONS_ESTADOS
        ]);
    }
    /**
     * Comprueba los datos enviados por el formulario y actualiza la tarea.
     * Solo para operarios
     * @param Request $request
     * @param int $idTarea
     * @return mixed
     */
    public function completarUpdate(Request $request, $idTarea){
         // Obtiene las cookies del usuario y token, comprueba que son validas y en caso de que no lo sean devulve la vista login
        if(!SeguridadUsuario::validarUsuario()) return redirect()->route('login.index');
        // Obtiene el usuario que está logueado
        $usuario = Usuario::getUsuario(SeguridadUsuario::getIdUsuario());

        // Si el usuario es admin redirigir al inicio
        if($usuario->esAdmin()) return redirect()->route('home');

        // Crea un validador de errores
        $validador_err = new ValidarErrores();
        // Comprueba los errores
        $gestor_err = $validador_err->validarCampos($request, ['fecha_realizacion']);
        
        // Obtiene la tarea usando el id
        $tarea = Tarea::getTarea($idTarea);
        
        // Si hay errores devuelve la vista completar con los datos necesarios, los que recibimos y los errores
        if($gestor_err->hayErrores()){
            return view('tareas/completar', [
                'usuario'=>$usuario,
                "tarea"=>$tarea,
                "gestor_err"=>$gestor_err,
                "request"=>$request,
                "optionsEstado"=>Tarea::OPTIONS_ESTADOS
            ]);
        }

        // Si no hay errores modifica la tarea.
        $tarea->estado = $request->estado;
        $tarea->fecha_realizacion = $request->fecha_realizacion;
        $tarea->anotaciones_p = $request->anotaciones_posteriores;

        // Guarda el archivo en storage
        if($request->hasFile('fichero')){
            $fichero = $request->file('fichero');
            $nombreFichero = $fichero->getClientOriginalName();
            $fichero->storeAs("tareas/$tarea->id/ficheros_tarea", $nombreFichero, "public");
            $tarea->fichero = "tareas/$tarea->id/ficheros_tarea/$nombreFichero";
        }

        // Guarda las imagenes en storage
        if($request->hasFile('fotos')){
            $fotos_arr = [];
            foreach($request->file('fotos') as $foto){
                $nombreFoto = $foto->getClientOriginalName();
                $foto->storeAs("tareas/$tarea->id/fotos_tarea", $nombreFoto, "public");
                $rutaFoto = "tareas/$tarea->id/fotos_tarea/$nombreFoto";

                $fotos_arr[] = $rutaFoto;
            }
            // Guarda las imagenes en la bd
            $tarea->imagenes = $fotos_arr;
            $tarea->guardarImagenes();
        }
        
        // Actualiza la tarea en la bd
        $tarea->completar();
        
        // Redirecciona a la vista de la tarea que se ha modificado
        return redirect()->route('tareas.show', $idTarea);
    }
    /**
     * Devuelve la página de confirmación para la eliminación de una tarea.
     * Solo para administradores
     * @param int $idTarea
     * @return mixed
     */
    public function confirmacion($idTarea){
        // Obtiene las cookies del usuario y token, comprueba que son validas y en caso de que no lo sean devulve la vista login
        if(!SeguridadUsuario::validarUsuario()) return redirect()->route('login.index');
        // Obtiene el usuario que está logueado
        $usuario = Usuario::getUsuario(SeguridadUsuario::getIdUsuario());

        // Si el usuario no es admin redirigir al inicio
        if(!$usuario->esAdmin()) return redirect()->route('home');

        $tarea = Tarea::getTarea($idTarea);
        return view('tareas/confirmacion', compact(['tarea', 'usuario']));
    }
    /**
     * Elimina la tarea de la bd
     * Solo para administradores
     * @param int $idTarea
     * @return mixed
     */
    public function delete($idTarea){
         // Obtiene las cookies del usuario y token, comprueba que son validas y en caso de que no lo sean devulve la vista login
        if(!SeguridadUsuario::validarUsuario()) return redirect()->route('login.index');
        // Obtiene el usuario que está logueado
        $usuario = Usuario::getUsuario(SeguridadUsuario::getIdUsuario());
        
        // Si el usuario no es admin redirigir al inicio
        if(!$usuario->esAdmin()) return redirect()->route('home');

        // Elimina la tarea de la bd
        $tarea = new Tarea();
        $tarea->id = $idTarea;
        $resultado = $tarea->eliminar();

        // Elimina la carpeta de la tarea en storage
        $rutaDirectorio = "public/tareas/$tarea->id";
        if(Storage::directoryExists($rutaDirectorio)){
            Storage::deleteDirectory($rutaDirectorio);
        }

        // Devuelve la vista con el resultado
        return view('tareas/resultado', [
            'usuario'=>$usuario,
            'tarea'=>$tarea,
            'resultado'=>$resultado
        ]); 
    }
}
