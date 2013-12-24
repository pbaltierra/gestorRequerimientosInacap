<?php
 
/**
 * @author McCubo
 * @copyright 2011
 * @license GPL V2.
 */
class Mensaje_model extends CI_Model {
    
    public $id_mensaje;
    public $mensaje;
    public $valido;
    public $id_usuario;
    public $id_receptor;
        
    public function __construct() {
        parent::__construct(); //Llamada al constructor padre.
        $this->id_mensaje  = 0;
        $this->mensaje   = "";
        $this->valido   = 1;
        $this->id_usuario = 0;
        $this->id_receptor = 0;
    }
    
    public function get_registros_arr($interno=null){         
        $arreglo = get_object_vars($this); 
        if($interno == null){
            /*
            unset(
                    $arreglo['tabla'],                    
                    $arreglo['campoValido'],
                    $arreglo['nom_planta']
                    );
            */
        }         
        return $arreglo;         
    }
    
    public function set_registro_arr($arreglo){        
        reset($arreglo);  
        while (list($key, $value) = each($arreglo)) {  
          $this->$key = $value;  
        }         
    }   
    
     
}
?>