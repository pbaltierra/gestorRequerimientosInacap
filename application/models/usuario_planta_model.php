<?php
 
/**
 * @author McCubo
 * @copyright 2011
 * @license GPL V2.
 */
class Usuario_planta_model extends CI_Model {
    
    public $id_usuario_planta;
    public $id_planta;
    public $id_usuario;
    public $valido;
    
    //public $id_tipo;
    
    public function __construct() {
        parent::__construct(); //Llamada al constructor padre.
        
        $this->id_planta    = 0;
        $this->id_usuario   = 0;
        $this->valido       = 1;
    }
    
    public function get_registros_arr($interno=null){         
        $arreglo = get_object_vars($this); 
        if($interno == null){
            /*
            unset(
                    //$arreglo['tabla'],                    
                    //$arreglo['campoValido'],
                    //$arreglo['id_tipo']
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