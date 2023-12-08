<?php
/**
 * Autor: Valentin Andrei Culea
 * Fecha: 07/12/2023
 * Versión 1
 */

namespace App\Models;

use DateTime;

class Tarea
{

    public $id, $poblacion, $provincia, $cod_postal, $operario;
    public $nif, $contacto, $telefono, $descripcion, $correo, $direccion, $estado, $anotaciones_a, $anotaciones_p;
    public $fecha_creacion;
    public $fecha_realizacion;
    public $fichero;
    public $imagenes;

    // Lista con los estados que puede tener una tarea
    const OPTIONS_ESTADOS = [
        "B"=> "Esperando a ser aprobada",
        "P"=> "Pendiente",
        "R"=> "Realizada",
        "C"=> "Cancelada",
    ];

    // Lista con los campos que puede tener una tarea
    const OPTIONS_CAMPOS = [
        'id' => 'ID',
        'nif' => 'NIF',
        'contacto' => 'Contacto',
        'operario' => 'Operario',
        'telefono' => 'Teléfono',
        'descripcion' => 'Descripción',
        'correo' => 'Correo',
        'direccion' => 'Dirección',
        'poblacion' => 'Población',
        'provincia' => 'Provincia',
        'cod_postal' => 'Código postal',
        'estado' => 'Estado',
        'fecha_creacion' => 'Fecha creación',
        'fecha_realizacion' => 'Fecha realización',
        'anotaciones_anteriores' => 'Anotaciones anteriores',
        'anotaciones_posteriores' => 'Anotaciones posteriores',
    ];

    // Lista de criterios por los que se puede buscar una tarea
    const OPTIONS_CRITERIOS = [
        'like' => 'Igual',
        'not like' => 'No igual',
        '>' => 'Mayor que',
        '<' => 'Menor que',
        '>=' => 'Mayor o igual que',
        '<=' => 'Menor o igual que',
    ];

    public function __construct(){

    }

    /**
     * Recibe un registro de tipo array asociativo u objeto y lo convierte a un objeto Tarea
     * @param object|array $registro
     * @return Tarea
     */
    public static function registroToTarea($reg){
        $tarea = new Tarea();

        if(is_object($reg)){
            $tarea = Tarea::registroObjToTarea($reg);
        }else{
            $tarea = Tarea::registroAssocToTarea($reg);
        }

        // Convierte el nif a mayúsculas
        if($tarea->nif != null){
            $tarea->nif = strtoupper($tarea->nif);
        }
        // Formatea la fecha de creación
        if($tarea->fecha_creacion != null){
            $tarea->fecha_creacion = self::formatearTimestampToDMY($tarea->fecha_creacion);
        }
        // Formatea la fecha de realización
        if($tarea->fecha_realizacion != null){
            $tarea->fecha_realizacion = self::formatearFechaDMY($tarea->fecha_realizacion);
        }

        return $tarea;
    }
    /**
     * Convierte un objeto a Tarea con los atributos de dicho objeto
     * @param object $registro
     * @return Tarea
     */
    public static function registroObjToTarea($reg){
        $tarea = new Tarea();

        $tarea->id = $reg->id ?? null;
        $tarea->nif = $reg->nif ?? null;
        $tarea->contacto = $reg->contacto ?? null;
        $tarea->telefono = $reg->telefono ?? null;
        $tarea->descripcion = $reg->descripcion ?? null;
        $tarea->correo = $reg->correo ?? null;
        $tarea->direccion = $reg->direccion ?? null;
        $tarea->poblacion = $reg->poblacion ?? null;
        $tarea->provincia = $reg->provincia ?? null;
        $tarea->cod_postal = $reg->cod_postal ?? null;
        $tarea->estado = $reg->estado ?? null;
        $tarea->operario = $reg->operario ?? null;
        $tarea->fecha_creacion = $reg->fecha_creacion ?? null;
        $tarea->fecha_realizacion = $reg->fecha_realizacion ?? null;
        $tarea->anotaciones_a = $reg->anotaciones_anteriores ?? null;
        $tarea->anotaciones_p = $reg->anotaciones_posteriores ?? null;
        $tarea->fichero = $reg->fichero ?? null;

        return $tarea;
    }
    /**
     * Convierte un array asociativo a Tarea
     * @param array
     * @return Tarea
     */
    public static function registroAssocToTarea($reg){
        $tarea = new Tarea();

        $tarea->id = $reg["id"] ?? null;
        $tarea->nif = $reg["nif"] ?? null;
        $tarea->contacto = $reg["contacto"] ?? null;
        $tarea->telefono = $reg["telefono"] ?? null;
        $tarea->descripcion = $reg["descripcion"] ?? null;
        $tarea->correo = $reg["correo"] ?? null;
        $tarea->direccion = $reg["direccion"] ?? null;
        $tarea->poblacion = $reg["poblacion"] ?? null;
        $tarea->provincia = $reg["provincia"] ?? null;
        $tarea->cod_postal = $reg["cod_postal"] ?? null;
        $tarea->estado = $reg["estado"] ?? null;
        $tarea->operario = $reg["operario"] ?? null;
        $tarea->fecha_creacion = $reg["fecha_creacion"] ?? null;
        $tarea->fecha_realizacion = $reg["fecha_realizacion"] ?? null;
        $tarea->anotaciones_a = $reg["anotaciones_anteriores"] ?? null;
        $tarea->anotaciones_p = $reg["anotaciones_posteriores"] ?? null;
        $tarea->fichero = $reg["fichero"] ?? null;

        return $tarea;
    }
    /**
     * Devuelve la lista de tareas paginada y con los filtro pasados por parámtros
     * @param int $pagina
     * @param array $filtros
     * @return array
     */
    public static function getTareas($page = 1, $filtros = []){
        // Cantidad de resultados por pagina
        $offset = 10;
        // Punto de inicio desde el que se empieza a mostrar las tareas
        $inicio = ($page -1) * $offset;
        // Filtro que se usarán para la búsqueda
        $sql_filtro = '';
        // Lista de filtros generados en el formulario
        $arr_filtros = [];
        // Sql para ordenar las tareas, por defecto se ordenan por la fecha de realización de forma descendente
        $sql_order_by = 'fecha_realizacion DESC';
        // Obtiene los posibles criterios para buscar una tarea para validar que los criterios pasados por parámetro sean válidos
        $criterios_tarea = Tarea::OPTIONS_CRITERIOS;

        // Genera el sql para filtrar los datos según los campos y criterios seleccionados
        // Comprueba si el valor no está vacío y si el criterio es válido.
        // En caso que el criterio no sea válido omitirá ese filtro de búsqueda
        if(!empty($filtros["valor1"]) && isset($criterios_tarea[$filtros["criterio1"]])){
            $arr_filtros[] = Database::limpiarCampo($filtros["campo1"])." ".$filtros["criterio1"]." '".Database::limpiarCampo($filtros["valor1"])."'";
        }
        if(!empty($filtros["valor2"]) && isset($criterios_tarea[$filtros["criterio2"]])){
            $arr_filtros[] = Database::limpiarCampo($filtros["campo2"])." ".$filtros["criterio2"]." '".Database::limpiarCampo($filtros["valor2"])."'";
        }
        if(!empty($filtros["valor3"]) && isset($criterios_tarea[$filtros["criterio3"]])){
            $arr_filtros[] = Database::limpiarCampo($filtros["campo3"])." ".$filtros["criterio3"]." '".Database::limpiarCampo($filtros["valor3"])."' ";
        }
        // Muestra las tareas que estén pendientes de completar
        if(!empty($filtros["pendientes"])){
            $arr_filtros[] = "estado like 'B'";
        }
        // Si existen filtros para la busqueda estón se juntan en el sql del filtro separados por 'and'
        if(count($arr_filtros) > 0){
            $sql_filtro = implode(' and ', $arr_filtros);
        }
        // Si no hay ningún filtro se mostrarán todas las tareas
        else{
            $sql_filtro = '1';
        }

        // Generar order by
        if(!empty($filtros["order"])){
            $sql_order_by = Database::limpiarCampo($filtros["order"])." ".Database::limpiarCampo($filtros["order-mode"] ?? 'desc');
        }

        // Obtiene la instqncia Singleton de la bd
        $db = Database::getInstance();

        // Obtener las tareas de la bd con los filtros y la paginación correspondiente ordenados por un campo.
        $sql = "SELECT * FROM tarea WHERE $sql_filtro ORDER BY $sql_order_by LIMIT $inicio, $offset";
        $db->consulta($sql);

        // Lista en donde se guardarán los registros de la bd
        $lista = [];

        // Lee los registros de la bd y los guarda en la lista convertidos a Tarea
        while($reg = $db->leeRegistro()){
            $tarea = Tarea::registroToTarea($reg);
            $lista[] = $tarea;
        }

        // Obtener el total de resultados sin paginar
        $rs = $db->leeUnRegistro("tarea", $sql_filtro, "COUNT(*) as count");
        $totalRegistros = $rs["count"];
        $totalPaginas = ceil($totalRegistros/$offset);

        // Devuelve un array con las tareas, la cantidad de registros de nuestra busqueda y el total de páginas
        return [
            "tareas"=>$lista,
            "registros"=>$totalRegistros,
            "paginas"=>$totalPaginas
        ];

    }
    /**
     * Devuelve la tarea con el id pasado por parámetro
     * @param int $idTarea
     * @return Tarea|NULL
     */
    public static function getTarea($id){
        // Obtiene la instancia de la bd
        $db = Database::getInstance();

        // Obtiene la tarea de la bd
        $resultado = $db->leeUnRegistro("tarea", "id = ".Database::limpiarCampo($id));

        // Si existe
        if($resultado){
            // Convierte el registro a Tarea y la devuelve
            $tarea = Tarea::registroToTarea($resultado);
            return $tarea;
        }

        // Si no existe devuelve null
        return null;

    }
    /**
     * Obtiene el id del último registro insertado en la bd
     * @return int
     */
    public static function getUltimoId(){
        $db = Database::getInstance();
        return $db->lastID();
    }
    /**
     * Inserta una tarea en la bd usando una sentencia preparada
     * @return bool
     */
    public function guardar(){
        // Obtiene la instancia de la bd
        $db = Database::getInstance();

        // Crea el sql para insertar la tarea
        $sql = "INSERT INTO tarea(nif, contacto, telefono, descripcion, correo, direccion, poblacion, provincia, cod_postal, estado, fecha_creacion, operario, fecha_realizacion) 
        VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        // Crea una sentencia preparad acon el sql
        $stmt = $db->crearSentenciaPreparada($sql);

        // Formatea la fecha de realización para que no haga problemas a la hora de insertarla en la bd
        $fecha_realizacion_f = self::formatearFechaBD($this->fecha_realizacion);

        // Enlaza las variables de la sentencia preparada
        $stmt->bind_param('sssssssisssis',
            $this->nif,
            $this->contacto,
            $this->telefono,
            $this->descripcion,
            $this->correo,
            $this->direccion,
            $this->poblacion,
            $this->provincia,
            $this->cod_postal,
            $this->estado,
            $this->fecha_creacion,
            $this->operario,
            $fecha_realizacion_f
        );

        // Devuelve el resultado de la ejecución de la sentencia preparada
        return $stmt->execute();

    }
    /**
     * Actualiza la tarea en la bd usando una sentencia preparada
     * @return bool
     */
    public function actualizar(){
        // Obtiene la instancia de la bd
        $db = Database::getInstance();

        // Crea el sql para actualizar la tarea
        $sql = "UPDATE tarea 
        SET nif = ?, contacto = ?, telefono = ?, descripcion = ?, correo = ?, direccion = ?, poblacion = ?, provincia = ?, cod_postal = ?, estado = ?, operario = ?, fecha_realizacion = ?, anotaciones_posteriores = ?, anotaciones_anteriores = ? 
        WHERE id = ?";
        
        // Crea la sentencia preparada usando el sql
        $stmt = $db->crearSentenciaPreparada($sql);

        // Formatea la fecha de realización para que no haga problemas a la hora de insertarla en la bd
        $fecha_realizacion_f = self::formatearFechaBD($this->fecha_realizacion);

        // Enlaza las variables de la sentencia preparada
        $stmt->bind_param('sssssssississsi',
            $this->nif,
            $this->contacto,
            $this->telefono,
            $this->descripcion,
            $this->correo,
            $this->direccion,
            $this->poblacion,
            $this->provincia,
            $this->cod_postal,
            $this->estado,
            $this->operario,
            $fecha_realizacion_f,
            $this->anotaciones_p,
            $this->anotaciones_a,
            $this->id
        );

        // Devuelve el resultado de la ejecución de la sentencia preparada
        return $stmt->execute();
    }
    /**
     * Actualiza el estado de una tarea además de permitir subir documentación adjunta usando sentencia preparada.
     * Lo que se guarda de la documentación adjunta en la bd es la ruta de esta.
     * @return bool
     */
    public function completar(){
        // Obtiene la instancia de la bd
        $db = Database::getInstance();
        // Crea el sql para actualizar la tarea
        $sql = "UPDATE tarea 
        SET estado = ?, fecha_realizacion = ?, anotaciones_posteriores = ?, fichero = ? 
        WHERE id = ?";
        
        // Crea la sentencia preparada usando el sql
        $stmt = $db->crearSentenciaPreparada($sql);

        // Formatea la fecha de realización para que no haga problemas a la hora de insertarla en la bd
        $fecha_realizacion_f = self::formatearFechaBD($this->fecha_realizacion);

        // Enlaza las variables de la sentencia preparada
        $stmt->bind_param('ssssi',
            $this->estado,
            $fecha_realizacion_f,
            $this->anotaciones_p,
            $this->fichero,
            $this->id
        );

        // Devuelve el resultado de la ejecución de la sentencia preparada
        return $stmt->execute();
    }
    /**
     * Elimina una tarea usando sentencia preparada
     * @return bool
     */
    public function eliminar(){
        // Obtiene la instancia de la bd
        $db = Database::getInstance();
        // Crea el sql para eliminar la tarea
        $sql = "DELETE FROM tarea WHERE id = ?";
        
        // Crea la sentencia preparada usando el sql
        $stmt = $db->crearSentenciaPreparada($sql);

        // Enlaza las variables de la sentencia preparada
        $stmt->bind_param('i',
            $this->id
        );

        // Devuelve el resultado de la ejecución de la sentencia preparada
        return $stmt->execute();
    }
    /**
     * Formatea la fecha pasada por parámetro de dia/mes/año a el formato en el que se guarda en la bd
     * @param string $fecha
     * @return string|NULL
     */
    public function formatearFechaBD($fecha){
        // Si la fecha es null devuelve null
        if($fecha == null) return null;

        // Crea un objeto DateTime con un string con el formato de la fecha pasado por parámetro
        $fechaObjeto = DateTime::createFromFormat('d/m/Y', $fecha);
        // Devuelve la fecha en formato válida para la bd
        return $fechaObjeto->format('Y-m-d H:i:s');
    }
    /**
     * Formatea la fecha pasada por parámetro de año-mes-dia a dia/mes/año
     * @param string $fecha
     * @return string|NULL
     */
    public static function formatearFechaDMY($fecha){
        // Si la fecha es null devuelve null
        if($fecha == null) return null;

        // Crea un objeto DateTime con un string con el formato de la fecha pasado por parámetro
        $fechaObjeto = DateTime::createFromFormat('Y-m-d', $fecha);

        // Si no ha podido crear el objeto fecha devuelve null
        if($fechaObjeto == null) return $fecha;
        // Si ha podido devuelve la fecha con el nuevo formato
        return $fechaObjeto->format('d/m/Y');
    }
    /**
     * Formatea el formato de la fecha de timestamp a dia/mes/año
     * @param string $fecha
     * @return string|NULL
     */
    public static function formatearTimestampToDMY($fecha){
        // Si la fecha es null devuelve null
        if($fecha == null) return null;

        // Crea un objeto DateTime con un string con el formato de la fecha pasado por parámetro
        $fechaObjeto = DateTime::createFromFormat('Y-m-d H:i:s', $fecha);
        
        // Si no ha podido crear el objeto fecha devuelve null
        if($fechaObjeto == null) return $fecha;
        // Si ha podido devuelve la fecha con el nuevo formato
        return $fechaObjeto->format('d/m/Y');
    }
    /**
     * Devuelve el nombre de usuario que tiene el operario de la tarea
     * @return Usuario|NULL
     */
    public function getOperario(){
        // Si la tarea no tiene un operario asignado devuelve null
        if($this->operario == null) return null;
        // Obtiene los datos de dicho operario
        $usuario = Usuario::getUsuario($this->operario);
        // Devuelve el nombre de usuario
        return $usuario->usuario ?? null;
    }
    /**
     * Guarda todas las rutas de las imagenes de la tarea en al bd
     */
    public function guardarImagenes(){
        // Obtiene la instancia de la bd
        $db = Database::getInstance();

        // Crea el sql para insertar las imagenes
        $sql = "INSERT INTO img(path, tarea) VALUES(?, ?)";
        // Crea una sentencia preparada con el sql
        $stmt = $db->crearSentenciaPreparada($sql);

        // Inserta todas imagenes usando la sentencia preparada
        foreach($this->imagenes as $img){
            $stmt->bind_param('si', $img, $this->id);
            $stmt->execute();
        }

        $stmt->close();
    }
    /**
     * Obtiene todas las rutas de las imagenes de dicha tarea
     * @return array
     */
    public function getImagenes(){
        // Obtiene la instancia de la bd
        $db = Database::getInstance();
        
        // Crea una consulta en la bd para obtener las imagenes de la tarea
        $db->consulta("SELECT path FROM img WHERE tarea = $this->id");

        // Lista donde se guardaran las rutas de las imagenes
        $lista = [];

        // Lee todos los registros y los guarda en la lista
        while($reg = $db->leeRegistro()){
            $lista[] = $reg;
        }

        // Devuelve la lista con las rutas
        return $lista;
    }
}
