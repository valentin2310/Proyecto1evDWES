<?php

namespace App\Http\Controllers;

use App\Models\Provincia;
use App\Models\SeguridadUsuario;
use App\Models\Tarea;
use App\Models\Usuario;
use App\Models\ValidarErrores;
use Illuminate\Http\Request;

class TareaController extends Controller
{

    public function index(){
        // Obtiene las cookies del usuario y token, comprueba que son validas y en caso de que no lo sean devulve la vista login
        if(!SeguridadUsuario::validarUsuario()) return redirect()->route('login.index');
        $usuario = Usuario::getUsuario(SeguridadUsuario::getIdUsuario());

        $page = request()->query('page') ?? 1;
        $resultado = Tarea::getTareas($page);

        return view('tareas/index', [
            'usuario'=>$usuario,
            'tareas'=> $resultado["tareas"],
            "resultados"=> $resultado["registros"],
            "paginas"=> $resultado["paginas"],
            'page'=> $page
        ]);
    }

    public function create(){
         // Obtiene las cookies del usuario y token, comprueba que son validas y en caso de que no lo sean devulve la vista login
        if(!SeguridadUsuario::validarUsuario()) return redirect()->route('login.index');
        $usuario = Usuario::getUsuario(SeguridadUsuario::getIdUsuario());

        $operarios = Usuario::getOperarios();
        $provincias = Provincia::getProvincias();
        return view('tareas/create', [
            'usuario'=>$usuario,
            "optionsEstado"=>Tarea::OPTIONS_ESTADOS,
            "listaProvincias"=>$provincias,
            "listaOperarios"=>$operarios
        ]);
    }

    public function store(Request $request){
         // Obtiene las cookies del usuario y token, comprueba que son validas y en caso de que no lo sean devulve la vista login
        if(!SeguridadUsuario::validarUsuario()) return redirect()->route('login.index');

        $validador_err = new ValidarErrores();
        $gestor_err = $validador_err->validarCampos($request);

        if($gestor_err->hayErrores()){
            $usuario = Usuario::getUsuario(SeguridadUsuario::getIdUsuario());
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

        $tarea = Tarea::registroToTarea($request->all());
        $tarea->guardar();

        // TODO: Obtener el id del registro insertado
        $id = Tarea::getUltimoId();
        return redirect()->route('tareas.show', $id);
    }

    public function show($idTarea){
         // Obtiene las cookies del usuario y token, comprueba que son validas y en caso de que no lo sean devulve la vista login
        if(!SeguridadUsuario::validarUsuario()) return redirect()->route('login.index');
        $usuario = Usuario::getUsuario(SeguridadUsuario::getIdUsuario());

        $tarea = Tarea::getTarea($idTarea);
        return view('tareas/show', compact(['tarea', 'usuario']));
    }

    public function search(){
         // Obtiene las cookies del usuario y token, comprueba que son validas y en caso de que no lo sean devulve la vista login
        if(!SeguridadUsuario::validarUsuario()) return redirect()->route('login.index');
        
        $filtros = [];
        // Si hemos realizado una busqueda, tenemos que recibir los query params y filtrar las tareas
        if(request()->query()){
            $filtros = request()->all();
        }
        
        $usuario = Usuario::getUsuario(SeguridadUsuario::getIdUsuario());
        $page = request()->query('page') ?? 1;
        $resultado = Tarea::getTareas($page, $filtros??null);

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

    public function edit($idTarea){
         // Obtiene las cookies del usuario y token, comprueba que son validas y en caso de que no lo sean devulve la vista login
         if(!SeguridadUsuario::validarUsuario()) return redirect()->route('login.index');
         $usuario = Usuario::getUsuario(SeguridadUsuario::getIdUsuario());

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

    public function update(Request $request, $idTarea){
         // Obtiene las cookies del usuario y token, comprueba que son validas y en caso de que no lo sean devulve la vista login
        if(!SeguridadUsuario::validarUsuario()) return redirect()->route('login.index');

        $validador_err = new ValidarErrores();
        $gestor_err = $validador_err->validarCampos($request);

        $tarea = Tarea::getTarea($idTarea);
        
        if($gestor_err->hayErrores()){
            $usuario = Usuario::getUsuario(SeguridadUsuario::getIdUsuario());
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
        
        $tarea->actualizar();
        
        return redirect()->route('tareas.show', $idTarea);
    }

    public function completar($id){
         // Obtiene las cookies del usuario y token, comprueba que son validas y en caso de que no lo sean devulve la vista login
         if(!SeguridadUsuario::validarUsuario()) return redirect()->route('login.index');
         $usuario = Usuario::getUsuario(SeguridadUsuario::getIdUsuario());

        $tarea = Tarea::getTarea($id);
        return view('tareas/completar', [
            'usuario'=>$usuario,
            "tarea"=>$tarea,
            "optionsEstado"=>Tarea::OPTIONS_ESTADOS
        ]);
    }

    public function completarUpdate(Request $request, $idTarea){
         // Obtiene las cookies del usuario y token, comprueba que son validas y en caso de que no lo sean devulve la vista login
        if(!SeguridadUsuario::validarUsuario()) return redirect()->route('login.index');

        $validador_err = new ValidarErrores();
        $gestor_err = $validador_err->validarCampos($request, []);
        
        $usuario = Usuario::getUsuario(SeguridadUsuario::getIdUsuario());
        $tarea = Tarea::getTarea($idTarea);
        
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
        
        $tarea->completar();
        
        return redirect()->route('tareas.show', $idTarea);
    }
    public function confirmacion($idTarea){
         // Obtiene las cookies del usuario y token, comprueba que son validas y en caso de que no lo sean devulve la vista login
         if(!SeguridadUsuario::validarUsuario()) return redirect()->route('login.index');
         $usuario = Usuario::getUsuario(SeguridadUsuario::getIdUsuario());

        $tarea = Tarea::getTarea($idTarea);
        return view('tareas.confirmacion', compact(['tarea', 'usuario']));
    }

    public function delete($idTarea){
         // Obtiene las cookies del usuario y token, comprueba que son validas y en caso de que no lo sean devulve la vista login
        if(!SeguridadUsuario::validarUsuario()) return redirect()->route('login.index');

        $tarea = new Tarea();
        $tarea->id = $idTarea;
        $resultado = $tarea->eliminar();

        return view('tareas.resultado', [
            'tarea'=>$tarea,
            'resultado'=>$resultado
        ]);
    }
}
