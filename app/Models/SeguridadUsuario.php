<?php
/**
 * Autor: Valentin Andrei Culea
 * Fecha: 07/12/2023
 * Versión 1
 */

namespace App\Models;

class SeguridadUsuario
{
    public static function validarUsuario(){
        $cookie_usuario = $_COOKIE["id_usuario"] ?? null;
        $cookie_token = $_COOKIE["token_usuario"] ?? null;
        $cookie_valido = Usuario::validarUsuario($cookie_usuario, $cookie_token);

        session_start();
        $session_usuario = $_SESSION["id_usuario"] ?? null;
        $session_token = $_SESSION["token_usuario"] ?? null;
        $session_valido = Usuario::validarUsuario($session_usuario, $session_token);

        return $cookie_valido || $session_valido;
            
    }

    public static function getIdUsuario(){
        $cookie_usuario = $_COOKIE["id_usuario"] ?? null;
        $cookie_token = $_COOKIE["token_usuario"] ?? null;

        $session_usuario = $_SESSION["id_usuario"] ?? null;
        $session_token = $_SESSION["token_usuario"] ?? null;

        if($cookie_usuario && $cookie_token) return $cookie_usuario;
        else if($session_usuario && $session_token) return $session_usuario;
        else return null;
    }
}
