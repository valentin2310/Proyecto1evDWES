<?php

namespace App\Models;

class Usuario
{
    public $id, $usuario, $tipo, $password, $ultimo_login;
    
    private const TIPOS_USUARIOS = [
        "ADMIN" => 0,
        "OPERARIO" => 1
    ];
        
    public function __construct(){
    
    }
    
    public static function registroToUsuario($reg){
        if(is_object($reg)){
            return Usuario::registroObjToUsuario($reg);
        }
        return Usuario::registroAssocToUsuario($reg);
    }

    public static function registroObjToUsuario($reg){
        $usuario = new Usuario();

        $usuario->id = $reg->id ?? null;
        $usuario->usuario = $reg->usuario ?? null;
        $usuario->tipo = $reg->tipo ?? null;
        $usuario->password = $reg->password ?? null;
        $usuario->ultimo_login = $reg->ultimo_login ?? null;

        return $usuario;
    }

    public static function registroAssocToUsuario($reg){
        $usuario = new Usuario();

        $usuario->id = $reg["id"] ?? null;
        $usuario->usuario = $reg["usuario"] ?? null;
        $usuario->tipo = $reg["tipo"] ?? null;
        $usuario->password = $reg["password"] ?? null;
        $usuario->ultimo_login = $reg["ultimo_login"] ?? null;

        return $usuario;
    }
    
    public static function getUsuario($id){
        $db = Database::getInstance();

        $resultado = $db->leeUnRegistro("usuarios", "id = ".Database::limpiarCampo($id), "id, usuario, tipo, token, ultimo_login");

        if($resultado){
            return Usuario::registroToUsuario($resultado);
        }

        return null;
    }

    public static function getOperarios(){
        $db = Database::getInstance();
        
        $db->consulta("SELECT id, usuario FROM usuarios WHERE tipo = " . self::TIPOS_USUARIOS["OPERARIO"]);

        $lista = [];

        while($reg = $db->leeRegistro()){
            $operario = Usuario::registroToUsuario($reg);
            $lista[] = $operario;
        }

        return $lista;
    }

    public function validarLogin(){
        $db = Database::getInstance();

        $sql = "SELECT id FROM usuarios WHERE usuario LIKE ? AND password LIKE ?";

        $stmt = $db->crearSentenciaPreparada($sql);

        $stmt->bind_param('ss',
            $this->usuario,
            $this->password
        );

        if($stmt->execute()){
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            return $row ? $row["id"] : null;
        }

        return false;
    }

    public static function validarUsuario($id, $token){
        if($id == null || $token == null) return false;

        $db = Database::getInstance();

        $sql = "SELECT id FROM usuarios WHERE id = ? AND token LIKE ?";

        $stmt = $db->crearSentenciaPreparada($sql);

        $stmt->bind_param('is',
            $id,
            $token
        );

        return $stmt->execute();
    }

    public static function getUltimoId(){
        $db = Database::getInstance();
        return $db->lastID();
    }

    public static function generarToken(){
        $caracteres = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $longitudCaracteres = strlen($caracteres);
        $longitudToken = 32;

        $token = '';

        for ($i = 0; $i < $longitudToken; $i++) {
            $token .= $caracteres[random_int(0, $longitudCaracteres - 1)];
        }

        return $token;
    }

    public static function updateToken($id){
        $newToken = self::generarToken();

        $db = Database::getInstance();

        $sql = "UPDATE usuarios SET token = ?, ultimo_login = CURRENT_TIMESTAMP WHERE id = ?";

        $stmt = $db->crearSentenciaPreparada($sql);

        $stmt->bind_param('si',
            $newToken,
            $id
        );

        if($stmt->execute()){
            return $newToken;
        }

        return null;
    }

    public function esAdmin(){
        return $this->tipo == self::TIPOS_USUARIOS["ADMIN"];
    }
}
