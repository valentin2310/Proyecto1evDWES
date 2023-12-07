<?php
/**
 * Autor: Valentin Andrei Culea
 * Fecha: 07/12/2023
 * Versión 1
 */

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

}
