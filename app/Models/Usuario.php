<?php
/**
 * Autor: Valentin Andrei Culea
 * Fecha: 07/12/2023
 * Versión 1
 */

namespace App\Models;

class Usuario
{
    public $id, $usuario, $tipo, $password, $ultimo_login;

    // Lista con los tipos de usuarios que hay en la aplicación
    private const TIPOS_USUARIOS = [
        "ADMIN" => 0,
        "OPERARIO" => 1
    ];
        
    public function __construct(){
    
    }
    /**
     * Recibe un registro de tipo array asociativo u objeto y lo convierte a un objeto Usuario
     * @param object|array $registro
     * @return Usuario
     */
    public static function registroToUsuario($reg){
        if(is_object($reg)){
            return Usuario::registroObjToUsuario($reg);
        }
        return Usuario::registroAssocToUsuario($reg);
    }
    /**
     * Convierte un objeto a Usuario con los atributos de dicho objeto
     * @param object $registro
     * @return Usuario
     */
    public static function registroObjToUsuario($reg){
        $usuario = new Usuario();

        $usuario->id = $reg->id ?? null;
        $usuario->usuario = $reg->usuario ?? null;
        $usuario->tipo = $reg->tipo ?? null;
        $usuario->password = $reg->password ?? null;
        $usuario->ultimo_login = $reg->ultimo_login ?? null;

        return $usuario;
    }
     /**
     * Convierte un array asociativo a Usuario
     * @param array
     * @return Usuario
     */
    public static function registroAssocToUsuario($reg){
        $usuario = new Usuario();

        $usuario->id = $reg["id"] ?? null;
        $usuario->usuario = $reg["usuario"] ?? null;
        $usuario->tipo = $reg["tipo"] ?? null;
        $usuario->password = $reg["password"] ?? null;
        $usuario->ultimo_login = $reg["ultimo_login"] ?? null;

        return $usuario;
    }
    /**
     * Obtiene el usuario con el id pasado por parámetro
     * @param int $idUsuario
     * @return Usuario|NULL
     */
    public static function getUsuario($id){
        // Obtiene la instancia de la bd
        $db = Database::getInstance();

        // Obtiene el usuario de la bd
        $resultado = $db->leeUnRegistro("usuarios", "id = ".Database::limpiarCampo($id), "id, usuario, tipo, token, ultimo_login");

        // Si el usuario existe lo devuelve
        if($resultado){
            return Usuario::registroToUsuario($resultado);
        }

        // Si no existe devuelve null
        return null;
    }

    public static function getOperarios(){
        // Obtiene la instancia de la bd
        $db = Database::getInstance();
        
        // Crea una consulta en la bd para obtener todos los operarios
        $db->consulta("SELECT id, usuario, ultimo_login FROM usuarios WHERE tipo = " . self::TIPOS_USUARIOS["OPERARIO"]);

        // Lista donde se guardarán todos los operarios
        $lista = [];

        // Lee los registros y los convierte a Usuario para guardarlos en la lista
        while($reg = $db->leeRegistro()){
            $operario = Usuario::registroToUsuario($reg);
            $lista[] = $operario;
        }

        // Devuelve la lista con los operarios
        return $lista;
    }

    public static function getUsuarios($page = 1){
        // Cantidad de resultados por pagina
        $offset = 10;
        // Punto de inicio desde el que se empieza a mostrar los usuarios
        $inicio = ($page - 1) * $offset;
        // El tipo de usuario que vamos a buscar que será de tipo operario
        $tipo = self::TIPOS_USUARIOS["OPERARIO"];
        // Genera el sql del filtro que vamos a aplicar
        $sql_filtro = "tipo = $tipo";

        // Obtiene la instqncia Singleton de la bd
        $db = Database::getInstance();
    
        // Obtiene los usuarios con el filtro y la paginación
        $db->consulta("SELECT id, usuario, ultimo_login FROM usuarios WHERE $sql_filtro LIMIT $inicio, $offset");

        // Lista donde se guardarán los usuarios
        $lista = [];

        // Lee los registros y los convierte a Usuario para guardarlos en la lista
        while($reg = $db->leeRegistro()){
            $operario = Usuario::registroToUsuario($reg);
            $lista[] = $operario;
        }

        // Obtener el total de resultados sin paginar
        $rs = $db->leeUnRegistro("usuarios", $sql_filtro, "COUNT(*) as count");
        $totalRegistros = $rs["count"];
        $totalPaginas = ceil($totalRegistros/$offset);

        // Devuelve un array con los usuarios, la cantidad de registros de nuestra busqueda y el total de páginas
        return [
            "usuarios"=>$lista,
            "registros"=>$totalRegistros,
            "paginas"=>$totalPaginas
        ];
    }
    /**
     * Comprueba que los datos del formulario de inicio de sesión son correctos.
     * En caso de que sean correctos devuelve el id del usuario
     * @return int|bool
     */
    public function validarLogin(){
        // Obtiene la instqncia Singleton de la bd
        $db = Database::getInstance();

        // Crea el sql para obtener el usuario con dicho usuario y contraseña
        $sql = "SELECT id FROM usuarios WHERE usuario LIKE ? AND password LIKE ?";
        // Crea una sentencia preparada con el sql
        $stmt = $db->crearSentenciaPreparada($sql);

        // Enlaza los parámetros de la sentencia preparada
        $stmt->bind_param('ss',
            $this->usuario,
            $this->password
        );

        // Si existe el usuario devuelve su id
        if($stmt->execute()){
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            return $row ? $row["id"] : null;
        }

        // Si no existe devuelve false
        return false;
    }
    /**
     * Comprueba que las credenciales del usuario son válidas
     * @param int $id
     * @param string $token
     * @return bool
     */
    public static function validarUsuario($id, $token){
        // Si el id o el token son nulos devolver falso
        if($id == null || $token == null) return false;

        // Obtiene la instqncia Singleton de la bd
        $db = Database::getInstance();

        // Crea el sql para obtener el usuario con dicho id y token
        $sql = "SELECT id FROM usuarios WHERE id = ? AND token LIKE ?";
        // Crea una sentencia preparada con el sql
        $stmt = $db->crearSentenciaPreparada($sql);

        // Enlaza los parámetros de la sentencia preparada
        $stmt->bind_param('is',
            $id,
            $token
        );

        // Devuelve el resultado de la sentencia preparada
        return $stmt->execute();
    }
    /**
     * Genera un token aleatorio de 32 carácteres de longitud
     * @return string
     */
    public static function generarToken(){
        // Caracteres que contendrá el token
        $caracteres = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $longitudCaracteres = strlen($caracteres);
        $longitudToken = 32;

        $token = '';

        // Genera el token
        for ($i = 0; $i < $longitudToken; $i++) {
            $token .= $caracteres[random_int(0, $longitudCaracteres - 1)];
        }

        // Devuelve el token
        return $token;
    }
    /**
     * Actualiza el token del usuario con el id pasado por parámetro
     * @param int $idUsuario
     * @return string|NULL
     */
    public static function updateToken($id){
        // Genera el nuevo token
        $newToken = self::generarToken();

        // Obtiene la instancia de la bd
        $db = Database::getInstance();

        // Crea el sql para actualizar el token del usuario y actualizar el ultimo login
        $sql = "UPDATE usuarios SET token = ?, ultimo_login = CURRENT_TIMESTAMP WHERE id = ?";
        // Crea una sentencia preparada con el sql
        $stmt = $db->crearSentenciaPreparada($sql);

        // Vincula los parámetros de la sentencia preparada
        $stmt->bind_param('si',
            $newToken,
            $id
        );

        // Devuelve el nuevo token si la ejecución de la sentencia preparada fue exitosa
        if($stmt->execute()){
            return $newToken;
        }

        // En caso contrario devuelve null
        return null;
    }
    /**
     * Devuelve true si el usuario es administrador
     * @return bool
     */
    public function esAdmin(){
        return $this->tipo == self::TIPOS_USUARIOS["ADMIN"];
    }
    /**
     * Devuelve el usuario que ha sido el último en iniciar sesión
     * @return Usuario
     */
    public static function getUltimoLogin(){
        // Obtiene la instancia de la bd
        $db = Database::getInstance();
        
        // Crea una consulta para obtener el usuario que ha inicado sesión el último
        $db->consulta("SELECT id, usuario FROM usuarios ORDER BY ultimo_login DESC LIMIT 1");

        // Obtiene el registro
        $reg = $db->leeRegistro();
        // Si hay un registro devuelve el usuario, si no, devuelve null
        return $reg ? Usuario::registroToUsuario($reg) : null;
    }
    /**
     * Inserta un usuario en la bd usando una sentencia preparada
     * @return bool
     */
    public function guardar(){
         // Obtiene la instancia de la bd
        $db = Database::getInstance();

        // Crea el sql para insertar el usuario
        $sql = "INSERT INTO usuarios(usuario, tipo, password) VALUES(?, ?, ?)";
        // Crea una sentencia preparada con el sql
        $stmt = $db->crearSentenciaPreparada($sql);
        
        // El tipo de usuario será de tipo operario
        $tipo = self::TIPOS_USUARIOS["OPERARIO"];

        // Vincula los parámetros de la sentencia preparada
        $stmt->bind_param('sis',
            $this->usuario,
            $tipo,
            $this->password
        );

        // Devuelve el resultado de la ejecución de la sentencia preparada
        return $stmt->execute();
    }
    /**
     * Actualiza el usuario usando una sentencia preparada
     * @return bool
     */
    public function update(){
        // Obtiene la instancia de la bd
        $db = Database::getInstance();

        // Crea el sql para actualizar el usuario
        $sql = "UPDATE usuarios SET usuario = ?, password = ? WHERE id = ?";
        // Crea una sentencia preparada con el sql
        $stmt = $db->crearSentenciaPreparada($sql);

        // Vincula los parámetros de la sentencia preparada
        $stmt->bind_param('ssi',
            $this->usuario,
            $this->password,
            $this->id
        );

        // Devuelve el resultado de la ejecución de la sentecia preparada
        return $stmt->execute();
    }
    /**
     * Elimina el usuario usando una sentencia preparada
     * @return bool
     * @return mixed
     */
    public function eliminar(){
        // Obtiene la isntancia de la bd
        $db = Database::getInstance();

        // Crea el sql para eliminar el usuario
        $sql = "DELETE FROM usuarios WHERE id = ?";
        // Crea la sentencia preparada con el sql
        $stmt = $db->crearSentenciaPreparada($sql);

        // Vincula los parámetros de la sentencias preparada
        $stmt->bind_param('i',
            $this->id
        );

        // Devuelve el resultado de la ejecución de la sentencia preparada
        return $stmt->execute();
    }
}
