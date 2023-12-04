<?php

namespace App\Models;

use Illuminate\Http\Request;
use DateTime;

class ValidarErrores
{
    private GestorErrores $gestor;

    public function __construct(){
        $this->gestor = new GestorErrores();
    }

    public function limpiarCampo($data) {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }

    public function validarLogin(Request $request){
        if(empty($request->input('usuario'))){
            $this->gestor->addError('usuario', 'El campo usuario no puede estar vacío');
        }else{
            $this->validarUsuario($request->usuario);
        }
        if(empty($request->input('password'))){
            $this->gestor->addError('password', 'El campo contraseña no puede estar vacío');
        }else{
            $this->validarPassword($request->password);
        }

        return $this->gestor;
    }

    public function validarCampos(Request $request, $camposRequeridos = ["descripcion", "contacto", "correo", "nif"]){
        // Recorrer todos los campos enviados
        // La descripcion y la persona de contacto no deben estar vacíos

        foreach($camposRequeridos as $campo){
            $valor = $request->input($campo);
            $valor = $this->limpiarCampo($valor);

            if(empty($valor)){
                $this->gestor->addError($campo, "El campo $campo es obligatorio");
            } else if($campo == "correo"){
                $this->validarCorreo($valor);
            } else if($campo == "nif"){
                $this->validarNif($valor);
            }
        }

        if(!empty($request->input('cod_postal'))){
            $this->validarCodPostal($request->input('cod_postal'));
        }
        if(!empty($request->input('fecha_realizacion'))){
            $fechaCreacion = empty($request->input('fecha_creacion')) ? date('d/m/Y') : $request->input('fecha_creacion');
            $this->validarFechaRealizacion($fechaCreacion, $request->input('fecha_realizacion'));
        }
        $this->validarCampoTexto('descripcion', $request->descripcion);
        $this->validarCampoTexto('contacto', $request->contacto);

        return $this->gestor;
    }

    public function validarNif($string){
        // Tiene que tener un formato válido, formato español
        $letras = "TRWAGMYFPDXBNJZSQVHLCKE";
        $letrasNIE = "XYZ";
        $patron = "/^([XYZ]|[0-9]){1}[0-9]{7}[A-Z]{1}$/";

        $nif = strtoupper($string);

        if(preg_match($patron, $nif)){
            $nifSinLetra = substr($nif, 0, strlen($nif)-1);

            if(preg_match("/[XYZ]+/", $nifSinLetra)){
                for($i = 0; $i < strlen($letrasNIE); $i++){
                    if($nifSinLetra[0] == $letrasNIE[$i]){
                        $nifSinLetra = $i . substr($nifSinLetra, 1);
                    }
                }
            }

            $resto = intval($nifSinLetra)%23;
            $letra = $letras[$resto];

            if($nif[8] != $letra){
                $this->gestor->addError('nif', 'La letra del nif no coincide');
            }

        }else{
            $this->gestor->addError('nif', 'El formato del nif no es válido');
        }
    }

    public function validarCodPostal($string){
        // Tiene que tener formato válido, 5 números
        $patron = "/^[0-9]{5}$/";
        $valor = $this->limpiarCampo($string);

        if(!preg_match($patron, $valor)){
            $this->gestor->addError('cod_postal', 'El formato del código postal no es válido');
        }
    }
    
    public function validarCorreo($string){
        // No puede estar vacío y tiene que tener un formato válido

        $valor = $this->limpiarCampo($string);

        if(!filter_var($valor, FILTER_VALIDATE_EMAIL)){
            $this->gestor->addError('correo', 'El formato del correo no es válido');
        }
    }

    public function validarFechaRealizacion($fechaCreacion, $fechaRealizacion){
        // La fecha de realización debe ser mayor a la fecha actual
        // La fecha de realización debe ser mayor a la fecha de creación
        $fechaCreacion_obj = $this->fechaFormatoValido($fechaCreacion);
        $fechaRealizacion_obj = $this->fechaFormatoValido($fechaRealizacion);

        if(!$fechaCreacion_obj){
            $this->gestor->addError('fecha_creacion', 'El formato no es válido, debe ser dd/mm/yyyy');
        }

        if(!$fechaRealizacion_obj){
            $this->gestor->addError('fecha_realizacion', 'El formato no es válido, debe ser dd/mm/yyyy');
            return;
        }

        if($fechaRealizacion_obj < new DateTime() || $fechaRealizacion_obj < $fechaCreacion_obj){
            $this->gestor->addError('fecha_realizacion', 'La fecha realización debe ser mayor que la fecha actual y a la de creación');
        }
    }

    public function fechaFormatoValido($fecha, $formato = 'd/m/Y'){
        $valor = $this->limpiarCampo($fecha);
        $fechaObjeto = DateTime::createFromFormat($formato, $valor);

        if($fechaObjeto && $fechaObjeto->format($formato) == $valor){
            return $fechaObjeto;
        }
        return false;
    }

    public function validarCampoTexto($campo, $string){
        if (!$string || !$campo) return;

        $patron = "/^[a-zA-zñÑ áéíóúÁÉÍÓÚ]*$/";
        $valor = $this->limpiarCampo($string);

        if(!preg_match($patron, $valor)){
            $this->gestor->addError($campo, 'El campo contiene carácteres que no son válidos');
        }else if(strlen($valor) < 3){
            $this->gestor->addError($campo, 'Tiene que haber mínimo 3 carácteres');
        }
    }

    public function validarUsuario($string){
        $patron = "/^[a-zA-z]{3}[a-zA-Z_0-9]{0,17}$/";
        $valor = $this->limpiarCampo($string);

        if(strlen($valor) > 20 || strlen($valor) < 3){
            $this->gestor->addError('usuario', 'El nombre de usuario debe tener entre 3 y 20 carácteres');
        }else if(!preg_match($patron, $valor)){
            $this->gestor->addError('usuario', 'El nombre de usuario contiene carácteres no válidos');
        }
    }

    public function validarPassword($string){
        $valor = $this->limpiarCampo($string);

        if(strlen($valor) < 3){
            $this->gestor->addError('password', 'La contraseña debe tener entre 3 y 20 carácteres');
        }
    }
}
