<?php

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

    const OPTIONS_ESTADOS = [
        "B"=> "Esperando a ser aprobada",
        "P"=> "Pendiente",
        "R"=> "Realizada",
        "C"=> "Cancelada",
    ];

    const OPTIONS_CAMPOS = [
        'id' => 'ID',
        'nif' => 'NIF',
        'contacto' => 'Contacto',
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

    public static function registroToTarea($reg){
        $tarea = new Tarea();

        if(is_object($reg)){
            $tarea = Tarea::registroObjToTarea($reg);
        }else{
            $tarea = Tarea::registroAssocToTarea($reg);
        }

        if($tarea->nif != null){
            $tarea->nif = strtoupper($tarea->nif);
        }
        if($tarea->fecha_creacion != null){
            $tarea->fecha_creacion = self::formatearTimestampToDMY($tarea->fecha_creacion);
        }
        if($tarea->fecha_realizacion != null){
            $tarea->fecha_realizacion = self::formatearFechaDMY($tarea->fecha_realizacion);
        }

        return $tarea;
    }

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

    public static function getTareas($page = 1, $filtros = []){
        $offset = 10;
        $inicio = ($page -1) * $offset;
        $sql_filtro = '';
        $arr_filtros = [];

        if(!empty($filtros["valor1"])){
            $arr_filtros[] = Database::limpiarCampo($filtros["campo1"])." ".Database::limpiarCampo($filtros["criterio1"])." '".Database::limpiarCampo($filtros["valor1"])."'";
        }
        if(!empty($filtros["valor2"])){
            $arr_filtros[] = Database::limpiarCampo($filtros["campo2"])." ".Database::limpiarCampo($filtros["criterio2"])." '".Database::limpiarCampo($filtros["valor2"])."'";
        }
        if(!empty($filtros["valor3"])){
            $arr_filtros[] = Database::limpiarCampo($filtros["campo3"])." ".Database::limpiarCampo($filtros["criterio3"])." '".Database::limpiarCampo($filtros["valor3"])."' ";
        }
        if(count($arr_filtros) > 0){
            $sql_filtro = implode(' and ', $arr_filtros);
        }else{
            $sql_filtro = '1';
        }

        $db = Database::getInstance();

        // Obtener las tareas
        $db->consulta("SELECT * FROM tarea WHERE $sql_filtro ORDER BY fecha_realizacion DESC LIMIT $inicio, $offset");

        $lista = [];

        while($reg = $db->leeRegistro()){
            $tarea = Tarea::registroToTarea($reg);
            $lista[] = $tarea;
        }

        // Obtener el total de resultados sin paginar
        $rs = $db->leeUnRegistro("tarea", $sql_filtro, "COUNT(*) as count");
        $totalRegistros = $rs["count"];
        $totalPaginas = ceil($totalRegistros/$offset);

        return [
            "tareas"=>$lista,
            "registros"=>$totalRegistros,
            "paginas"=>$totalPaginas
        ];

    }
    public static function getTarea($id){

        $db = Database::getInstance();

        $resultado = $db->leeUnRegistro("tarea", "id = ".Database::limpiarCampo($id));

        if($resultado){
            $tarea = Tarea::registroToTarea($resultado);
            return $tarea;
        }

        return null;

    }

    public static function getUltimoId(){
        $db = Database::getInstance();
        return $db->lastID();
    }

    public function guardar(){
        $db = Database::getInstance();

        $sql = "INSERT INTO tarea(nif, contacto, telefono, descripcion, correo, direccion, poblacion, provincia, cod_postal, estado, fecha_creacion, operario, fecha_realizacion) 
        VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $db->crearSentenciaPreparada($sql);

        $fecha_realizacion_f = self::formatearFechaBD($this->fecha_realizacion);

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

        return $stmt->execute();

    }

    public function actualizar(){
        $db = Database::getInstance();

        $sql = "UPDATE tarea 
        SET nif = ?, contacto = ?, telefono = ?, descripcion = ?, correo = ?, direccion = ?, poblacion = ?, provincia = ?, cod_postal = ?, estado = ?, operario = ?, fecha_realizacion = ?, anotaciones_posteriores = ?, anotaciones_anteriores = ? 
        WHERE id = ?";
        
        $stmt = $db->crearSentenciaPreparada($sql);

        $fecha_realizacion_f = self::formatearFechaBD($this->fecha_realizacion);

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

        return $stmt->execute();
    }

    public function completar(){
        $db = Database::getInstance();

        $sql = "UPDATE tarea 
        SET estado = ?, fecha_realizacion = ?, anotaciones_posteriores = ?, fichero = ? 
        WHERE id = ?";
        
        $stmt = $db->crearSentenciaPreparada($sql);

        $fecha_realizacion_f = self::formatearFechaBD($this->fecha_realizacion);

        $stmt->bind_param('ssssi',
            $this->estado,
            $fecha_realizacion_f,
            $this->anotaciones_p,
            $this->fichero,
            $this->id
        );

        return $stmt->execute();
    }
    public function eliminar(){
        $db = Database::getInstance();

        $sql = "DELETE FROM tarea WHERE id = ?";
        
        $stmt = $db->crearSentenciaPreparada($sql);

        $stmt->bind_param('i',
            $this->id
        );

        return $stmt->execute();
    }
    public function formatearFechaBD($fecha){
        if($fecha == null) return null;

        $fechaObjeto = DateTime::createFromFormat('d/m/Y', $fecha);
        return $fechaObjeto->format('Y-m-d H:i:s');
    }
    public static function formatearFechaDMY($fecha){
        if($fecha == null) return null;

        $fechaObjeto = DateTime::createFromFormat('Y-m-d', $fecha);

        if($fechaObjeto == null) return $fecha;
        return $fechaObjeto->format('d/m/Y');
    }
    public static function formatearTimestampToDMY($fecha){
        if($fecha == null) return null;

        $fechaObjeto = DateTime::createFromFormat('Y-m-d H:i:s', $fecha);
        
        if($fechaObjeto == null) return $fecha;
        return $fechaObjeto->format('d/m/Y');
    }
    public function getOperario(){
        if($this->operario == null) return null;
        $usuario = Usuario::getUsuario($this->operario);
        return $usuario->usuario ?? null;
    }

    public function guardarImagenes(){
        $db = Database::getInstance();

        $sql = "INSERT INTO img(path, tarea) VALUES(?, ?)";
        $stmt = $db->crearSentenciaPreparada($sql);

        foreach($this->imagenes as $img){
            $stmt->bind_param('si', $img, $this->id);
            $stmt->execute();
        }

        $stmt->close();
    }

    public function getImagenes(){
        $db = Database::getInstance();
        
        $db->consulta("SELECT path FROM img WHERE tarea = $this->id");

        $lista = [];

        while($reg = $db->leeRegistro()){
            $lista[] = $reg;
        }

        return $lista;
    }
}
