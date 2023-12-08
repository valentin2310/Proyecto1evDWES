<?php
/**
 * Autor: Valentin Andrei Culea
 * Fecha: 07/12/2023
 * Versión 1
 */

namespace App\Models;

use Illuminate\Http\Request;
use DateTime;

class ValidarErrores
{
    // Gestor de errores donde se irán guardando todos los posibles errores
    private GestorErrores $gestor;

    /**
     * Constructor del objeto.
     * Crea un nuevo gestor de errores
     */
    public function __construct(){
        $this->gestor = new GestorErrores();
    }
    /**
	 * Limpia los datos eliminando los espacios por delante y detrás, los slashes y los carácteres especiales
	 * @param mixed $data
     * @return mixed
	 */
    public function limpiarCampo($data) {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }
    /**
     * Valida los campos del formulario del login, creación y modificación de usuarios
     * @param Request $request
     * @return GestorErrores
     */
    public function validarCamposUsuario(Request $request){
        // Si el campo está vacío añadir error
        if(empty($request->input('usuario'))){
            $this->gestor->addError('usuario', 'El campo usuario no puede estar vacío');
        }else{
            // Comprobar si el usuario es válido
            $this->validarUsuario($request->usuario);
        }
        // Si el campo está vacío añadir error
        if(empty($request->input('password'))){
            $this->gestor->addError('password', 'El campo contraseña no puede estar vacío');
        }else{
            // Comprobar si la contraseña es valida
            $this->validarPassword($request->password);
        }

        // Si el formulario es el de creación/modificación omprobar la contraseña
        if($request->input('password_2')){
            // El campo no puede estar vacío
            if(empty($request->input('password_2'))){
                $this->gestor->addError('password_2', 'El campo contraseña no puede estar vacío');
            }
            // Las contraseñas deben coincidir
            else if($request->input('password') != $request->input('password_2')){
                $this->gestor->addError('password_2', 'Las contraseñas no coinciden');
            }
        }

        // Devuelve el gestor de errores
        return $this->gestor;
    }
    /**
     * Valida que los campos de los formulario de las tareas sean válidos
     * @param Request $request
     * @param array $camposRequeridos
     * @return GestorErrores
     */
    public function validarCampos(Request $request, $camposRequeridos = ["descripcion", "contacto", "correo", "nif"]){
        // Recorre los campos requeridos y comprueba que no estén vaciós
        foreach($camposRequeridos as $campo){
            $valor = $request->input($campo);
            $valor = $this->limpiarCampo($valor);

            if(empty($valor)){
                $this->gestor->addError($campo, "El campo $campo es obligatorio");
            } 
            // Comprueba si el correo es válido
            else if($campo == "correo"){
                $this->validarCorreo($valor);
            } 
            // Comprueba que el nif es válido
            else if($campo == "nif"){
                $this->validarNif($valor);
            }
        }

        // Comprueba que el codigo postal sea válido
        if(!empty($request->input('cod_postal'))){
            $this->validarCodPostal($request->input('cod_postal'));
        }
        // Commpueba que la fecha de realización sea válida
        if(!empty($request->input('fecha_realizacion'))){
            $fechaCreacion = empty($request->input('fecha_creacion')) ? date('d/m/Y') : $request->input('fecha_creacion');
            $this->validarFechaRealizacion($fechaCreacion, $request->input('fecha_realizacion'));
        }
        // Comprueba que el contacto sea válido
        $this->validarCampoTexto('contacto', $request->contacto);

        // Devuelve el gestor de errores
        return $this->gestor;
    }
    /**
     * Valida que el nif tenga el formato correcto y que sea válido.
     * @param string $string
     */
    public function validarNif($string){
        // Tiene que tener un formato válido, formato español
        $letras = "TRWAGMYFPDXBNJZSQVHLCKE";
        $letrasNIE = "XYZ";
        // El patrong que tiene que tener el nif
        $patron = "/^([XYZ]|[0-9]){1}[0-9]{7}[A-Z]{1}$/";

        // Convierte el nif a mayúsculas
        $nif = strtoupper($string);

        // Comprueba que tenga el formato correcto
        if(preg_match($patron, $nif)){
            $nifSinLetra = substr($nif, 0, strlen($nif)-1);

            // Si el nif tiene xyz al inicio sustituirlo por su correspondiente valor numérico
            if(preg_match("/[XYZ]+/", $nifSinLetra)){
                for($i = 0; $i < strlen($letrasNIE); $i++){
                    if($nifSinLetra[0] == $letrasNIE[$i]){
                        $nifSinLetra = $i . substr($nifSinLetra, 1);
                    }
                }
            }

            // Obtiene la letra del nif
            $resto = intval($nifSinLetra)%23;
            $letra = $letras[$resto];

            // Comprueba si la letra del nif es válida
            if($nif[8] != $letra){
                $this->gestor->addError('nif', 'La letra del nif no coincide');
            }

        }else{
            $this->gestor->addError('nif', 'El formato del nif no es válido');
        }
    }
    /**
     * Comprueba que el formato del codigo postal sea correcto
     * @param string $string
     */
    public function validarCodPostal($string){
        // Tiene que tener formato válido, 5 números
        $patron = "/^[0-9]{5}$/";
        $valor = $this->limpiarCampo($string);

        // Comprueba el formato
        if(!preg_match($patron, $valor)){
            $this->gestor->addError('cod_postal', 'El formato del código postal no es válido');
        }
    }
    /**
     * Comprueba el formato del correo
     * @param string $string
     */
    public function validarCorreo($string){
        // No puede estar vacío y tiene que tener un formato válido

        $valor = $this->limpiarCampo($string);

        // Comprueba el formato
        if(!filter_var($valor, FILTER_VALIDATE_EMAIL)){
            $this->gestor->addError('correo', 'El formato del correo no es válido');
        }
    }
    /**
     * Comprueba que la fecha de realización sea válida.
     * La fecha de realización debe ser mayor a la fecha actual
     * La fecha de realización debe ser mayor a la fecha de creación
     * @param string $fechaCreacion
     * @param string @fechaRealizacion
     */
    public function validarFechaRealizacion($fechaCreacion, $fechaRealizacion){
        // Comprueba que el formato de las fechas sea valido
        $fechaCreacion_obj = $this->fechaFormatoValido($fechaCreacion);
        $fechaRealizacion_obj = $this->fechaFormatoValido($fechaRealizacion);

        if(!$fechaCreacion_obj){
            $this->gestor->addError('fecha_creacion', 'El formato no es válido, debe ser dd/mm/yyyy');
        }

        if(!$fechaRealizacion_obj){
            $this->gestor->addError('fecha_realizacion', 'El formato no es válido, debe ser dd/mm/yyyy');
            return;
        }

        // Comprueba que la fecha de realización sea mayor a la actual y a la de creación
        if($fechaRealizacion_obj < new DateTime() || $fechaRealizacion_obj < $fechaCreacion_obj){
            $this->gestor->addError('fecha_realizacion', 'La fecha realización debe ser mayor que la fecha actual y a la de creación');
        }
    }
    /**
     * Comprueba que el formato de la fecha sea válido
     * @param string $fecha
     * @param string $formato
     * @return bool
     */
    public function fechaFormatoValido($fecha, $formato = 'd/m/Y'){
        $valor = $this->limpiarCampo($fecha);
        // Crea un objeto con el formato indicado
        $fechaObjeto = DateTime::createFromFormat($formato, $valor);

        // Si es capaz de crearlo quiero decir que el formato es válido
        if($fechaObjeto && $fechaObjeto->format($formato) == $valor){
            return $fechaObjeto;
        }
        // En caso contrario el formato no es válido
        return false;
    }
    /**
     * Comprueba que el formato de los campos de texto sean válidos
     * Deben cumplir que no tengan carácteres raros y solo letras, la longitd mínima es de 3
     * @param string $campo
     * @param string $string
     */
    public function validarCampoTexto($campo, $string){
        // Si el campo o el valor del campo están vacíos no hacer nada
        if (!$string || !$campo) return;

        // El patron válido
        $patron = "/^[a-zA-zñÑ áéíóúÁÉÍÓÚ]*$/";
        $valor = $this->limpiarCampo($string);

        // Si no coincide con el patrón añadir error
        if(!preg_match($patron, $valor)){
            $this->gestor->addError($campo, 'El campo contiene carácteres que no son válidos');
        }
        // Debe tener mínimo 3 carácteres
        else if(strlen($valor) < 3){
            $this->gestor->addError($campo, 'Tiene que haber mínimo 3 carácteres');
        }
    }
    /**
     * Comprueba que le usuario sea válido.
     * Puede tener letras y números.
     * Tiene que tener un longitud mínima de 3 carácteres y una longitud máxima de 20 carácteres
     * @param string $string
     */
    public function validarUsuario($string){
        // Patrón válido
        $patron = "/^[a-zA-z]{3}[a-zA-Z_0-9]{0,17}$/";
        $valor = $this->limpiarCampo($string);

        // Si no tiene la longitud correcta añadir error
        if(strlen($valor) > 20 || strlen($valor) < 3){
            $this->gestor->addError('usuario', 'El nombre de usuario debe tener entre 3 y 20 carácteres');
        }
        // Comprueba que el usuario tenga el patrón correcto
        else if(!preg_match($patron, $valor)){
            $this->gestor->addError('usuario', 'El nombre de usuario contiene carácteres no válidos');
        }
    }
    /**
     * Comprueba que la contraseña sea válida
     * Debe tener una longitud mínima de 3 carácteres
     * @param string $string
     */
    public function validarPassword($string){
        $valor = $this->limpiarCampo($string);

        // Debe tener más de 3 carácteres
        if(strlen($valor) < 3){
            $this->gestor->addError('password', 'La contraseña debe tener entre 3 y 20 carácteres');
        }
    }
}
