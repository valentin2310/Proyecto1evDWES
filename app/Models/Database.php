<?php
/**
 * Autor: Valentin Andrei Culea
 * Fecha: 07/12/2023
 * Versión 1
 */

namespace App\Models;

use mysqli;

class Database
{
	// Instancia del objeto Database
    static $_instance = null;
	// Variable de la conexion con la bd mysqli
    private $link;
	// Resultado actual de la consulta en la bd
    private $result; 
	// Registro actual del resultado en la bd
    private $regActual;
     

	/**
	 * Contructor del objeto.
	 * Conecta automáticamente a la bd usando los datos de configuración de la bd
	 */
    private function __construct() {
		$db_conf = include(app_path('Config\dbconfig.php'));
        $this->conectar($db_conf);
    }
	/**
	 * Sobreescribe el metodo para clonar para aplicar el patrón Singleton
	 */
    private function __clone() {}
    
	/**
	 * Devuelva la instancia actual del objeto en caso de que exista, por el contrario crea una nueva.
	 * Aplica el patrón singleton
	 * @return Database
	 */
    public static function getInstance() {
        if (!(self::$_instance instanceof self)){
			self::$_instance=new self();
		}
		return self::$_instance;
    }
    
	/**
	 * Crea la conexion con al bd usando los datos pasados por parámetro.
	 * @param array
	 */
    public function conectar($conf) {
        if(! is_array($conf)) {
            exit();
        }

        $this->link = new mysqli($conf['servidor'], $conf['usuario'], $conf['password']);

        if(! $this->link) {
            printf("Error de conexión: %s\n", mysqli_connect_error());
			exit();
        }

        $this->link->select_db($conf['base_datos']);
        $this->link->query("SET NAMES 'utf8'");
    }

    /**
	 * Ejecuta una consulta SQL y devuelve el resultado de esta
	 * @param string $sql
	 * @return mixed
	 */
	public function consulta($sql)
	{
		$this->result=$this->link->query($sql);
		return $this->result;
	}


    /**
	 * Devuelve el siguiente registro del result set devuelto por una consulta.
	 *
	 * @param mixed $result
	 * @return array | NULL
	 */
	public function leeRegistro($result=NULL)
	{
		if (! $result)
		{
			if (! $this->result)
			{
				return NULL;
			}
			$result=$this->result;
		}
		$this->regActual=$result->fetch_assoc();
		return $this->regActual;
	}

	/**
	 * Devuelve el último registro leido
	 */
	public function registroActual()
	{
		return $this->regActual;
	}

    /**
	 * Devuelve el valor del último campo autonumérico insertado
	 * @return int
	 */
	public function lastID()
	{
		return $this->link->insert_id;
	}

	/**
	 * Devuelve el primer registro que cumple la condición indicada
	 * @param string $tabla
	 * @param string $condicion
	 * @param string $campos
	 * @return array|NULL
	 */
	public function leeUnRegistro($tabla, $condicion, $campos='*')
	{
		$sql="select $campos from $tabla where $condicion limit 1";
		$rs=$this->link->query($sql);
		if($rs)
		{
			return $rs->fetch_assoc();
		}
		else
		{
			return NULL;
		}
	}

	/**
	 * Crea una sentencia preparada y la devuelve para manipular los datos de la bd de forma más segura
	 * @param string $sql
	 */
	public function crearSentenciaPreparada($sql){
		return $this->link->prepare($sql);
	}
	/**
	 * Limpia los datos eliminando los espacios por delante y detrás, los slashes y los carácteres especiales
	 * @param mixed $data
	 */
	public static function limpiarCampo($data) {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }

}
