<?php
/**
 * Autor: Valentin Andrei Culea
 * Fecha: 07/12/2023
 * Versión 1
 */

namespace App\Models;

class GestorErrores
{
    // Variable de tipo array que guardará los errores
    private $errores;

    /**
     * Contructor del objeto.
     * Al crearse inicializa un array vacío a la lista de errores.
     */
    public function __construct(){
        $this->errores = [];
    }
    /**
     * Estiliza los mensajes de error
     * @param string $mensaje
     * @return string
     */
    public function formatearError($mensaje){
        return "<small class='text-danger'><i class='fa-solid fa-circle-exclamation'></i> $mensaje</small>";
    }
    /**
     * Devuelve true si hay algún error en el array de errores
     * @return bool
     */
    public function hayErrores(){
        return !empty($this->errores);
    }
    /**
     * Devuelve true si hay algún error en el campo pasado por parámetro
     * @param string $campo
     * @return bool
     */
    public function hayError($campo){
        return !empty($this->errores[$campo]);
    }
    /**
     * Añade un error a la lista de errores
     * @param string $campo
     * @param string $mensaje
     */
    public function addError($campo, $mensaje){
        $this->errores[$campo] = $mensaje;
    }
    /**
     * Devuelve el mensaje de error del campo pasado por parámetro.
     * Si no hay ningún erroe devuelve null
     * @param string $campo
     * @return string|NULL
     */
    public function getMensajeError($campo){
        return $this->hayError($campo) ? $this->errores[$campo] : null;
    }

}
