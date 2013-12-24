<?php
 
/**
 * @author McCubo
 * @copyright 2011
 * @license GPL V2.
 */
class Tipo_entidad_model extends CI_Model {
    
    public $id_tipo;
    public $nombre;
    public $valido;
    public $tabla;
    
    
    public function __construct() {
        parent::__construct(); //Llamada al constructor padre.
        $this->id_tipo  = 0;
        $this->nombre   = "";
        $this->valido   = 1;
        $this->tabla    = "";
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