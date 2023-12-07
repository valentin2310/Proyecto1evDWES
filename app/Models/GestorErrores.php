<?php
/**
 * Autor: Valentin Andrei Culea
 * Fecha: 07/12/2023
 * Versión 1
 */

namespace App\Models;

class GestorErrores
{

    private $errores;

    public function __construct(){
        $errores = [];
    }

    public function formatearError($mensaje){
        return "<small class='text-danger'><i class='fa-solid fa-circle-exclamation'></i> $mensaje</small>";
    }

    public function hayErrores(){
        return !empty($this->errores);
    }

    public function hayError($campo){
        return !empty($this->errores[$campo]);
    }

    public function addError($campo, $mensaje){
        $this->errores[$campo] = $mensaje;
    }

    public function getMensajeError($campo){
        return $this->hayError($campo) ? $this->errores[$campo] : null;
    }

}
