<?php
 
/**
 * @author McCubo
 * @copyright 2011
 * @license GPL V2.
 */
class Historial_model extends CI_Model {
    
    public $id_historial;
    public $id_usuario;
    public $valido;
    public $id_interaccion;
    public $objetivo_tipo;
    public $objetivo_id;
    public $fecha_creacion;
    public $id_planta;
    
    
    public $interaccion;
    public $nom_usuario;
    public $predicado; 
    
    public function __construct() {
        parent::__construct(); //Llamada al constructor padre.
        $this->id_historial     = 0;
        $this->id_usuario       = "";
        $this->valido           = 1;
        $this->id_interaccion   = 0;
        $this->objetivo_tipo    = 0;
        $this->objetivo_id      = 0;
        $this->id_planta        = 0;
        
        $this->interaccion  = "";
        $this->nom_usuario  = "";
        $this->predicado    = "";
    }
    
    public function get_registros_arr($interno=null){         
        $arreglo = get_object_vars($this); 
        if($interno == null){
            
            unset(
                    $arreglo['fecha_creacion'],
                    $arreglo['nom_usuario'],
                    $arreglo['predicado'],
                    $arreglo['interaccion']
                    );
            
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