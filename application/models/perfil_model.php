<?php
 
/**
 * @author McCubo
 * @copyright 2011
 * @license GPL V2.
 */
class Perfil_model extends CI_Model {
    
    public $id_perfil;
    public $id_usuario;
    public $id_tipo;
    public $valido;
    
    public function __construct() {
        parent::__construct(); //Llamada al constructor padre.
        $this->id_perfil    = 0;
        $this->id_usuario   = 0;
        $this->id_tipo      = 0;
        $this->valido   = 1;
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