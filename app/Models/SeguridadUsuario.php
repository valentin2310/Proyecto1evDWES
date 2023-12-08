<?php
/**
 * Autor: Valentin Andrei Culea
 * Fecha: 07/12/2023
 * Versión 1
 */

namespace App\Models;

class SeguridadUsuario
{
    /**
     * Obtiene las cookies y las variables de sesión que contienen las credenciales del usuario al iniciar sesión y comprueba que sea válido.
     * En caso de que algunas de las 2 opciones sea válido devuelve true
     * Esta función sirve para comprpbar que el usuario ha iniciado sesión y las credenciales son válidas
     * @return bool
     */
    public static function validarUsuario(){
        // Obtiene las cookies
        $cookie_usuario = $_COOKIE["id_usuario"] ?? null;
        $cookie_token = $_COOKIE["token_usuario"] ?? null;
        // Comprueba en la bd si las credenciales son válidas
        $cookie_valido = Usuario::validarUsuario($cookie_usuario, $cookie_token);
        
        // Obtiene las variables de sesión
        session_start();
        $session_usuario = $_SESSION["id_usuario"] ?? null;
        $session_token = $_SESSION["token_usuario"] ?? null;
        // Comprueba en la bd si son válidas
        $session_valido = Usuario::validarUsuario($session_usuario, $session_token);

        // Devuelve true si alguna de las 2 es válida
        return $cookie_valido || $session_valido;
            
    }
    /**
     * Obtiene el id del usuario que ha iniciado sesión usando las credenciales guardadas en las cookies o en las variables de sesión
     * @return int|NULL
     */
    public static function getIdUsuario(){
        // Obtiene las credenciales de las cookies
        $cookie_usuario = $_COOKIE["id_usuario"] ?? null;
        $cookie_token = $_COOKIE["token_usuario"] ?? null;

        // Obtiene las credenciales de las variables de sesión
        $session_usuario = $_SESSION["id_usuario"] ?? null;
        $session_token = $_SESSION["token_usuario"] ?? null;

        // Devuelve el id de usuario si existen las credenciales, si no existen las 2 devuelve null
        if($cookie_usuario && $cookie_token) return $cookie_usuario;
        else if($session_usuario && $session_token) return $session_usuario;
        else return null;
    }
}
