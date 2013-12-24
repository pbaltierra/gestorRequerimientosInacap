<?php
 
/**
 * @author McCubo
 * @copyright 2011
 * @license GPL V2.
 */
class Cliente_model extends CI_Model {
    
    public $id_cliente;
    public $id_interno;
    public $nombre;
    public $alias;
    public $valido;
    
    
    public function __construct() {
        parent::__construct(); //Llamada al constructor padre.
        $this->id_cliente   = 0;
        $this->nombre       = "";
        $this->alias        = "";
        $this->id_interno   = "";
        $this->valido       = 1;
    }
    
    public function get_registros_arr($interno=null){         
        
        $this->get_nombre();
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
        $this->set_nombre($this->nombre);
    }   
    
     public function set_nombre($nombre){
        $this->nombre = strtoupper($nombre);
    }
    
    public function get_nombre(){
        $this->nombre = strtoupper($this->nombre);
        return $this->nombre;
    }
     
}
?>