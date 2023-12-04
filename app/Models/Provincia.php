<?php

namespace App\Models;

class Provincia
{

    public $id, $provincia;

    public function __construct(){

    }

    public static function getProvincias(){
        $db = Database::getInstance();

        $db->consulta("SELECT * FROM provincias");

        $lista = [];

        while($reg = $db->leeRegistro()){
            $provincia = new Provincia();

            $provincia->id = $reg["id"];
            $provincia->provincia = $reg["provincia"];

            $lista[] = $provincia;
        }

        return $lista;
    }

    public static function getLocalidades($provincia){
        $db = Database::getInstance();

        $db->consulta("SELECT * FROM localidades where provincia = $provincia");

        $lista = [];

        while($reg = $db->leeRegistro()){
            $localidad = new Localidad();

            $localidad->id = $reg["id"];
            $localidad->provincia = $reg["provincia"];
            $localidad->localidad = $reg["localidad"];

            $lista[] = $localidad;
        }

        return $lista;
    }
}
