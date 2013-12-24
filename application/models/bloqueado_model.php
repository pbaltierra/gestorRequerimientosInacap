<?php
 
/**
 * @author McCubo
 * @copyright 2011
 * @license GPL V2.
 */
class Bloqueado_model extends CI_Model {
    
    public $id_bloqueado;
    public $id_planta;
    public $fecha_inicio;
    public $fecha_termino;
    public $id_usuario;
    public $id_mensaje;
    public $id_tipo;
    public $valido;
    public $mensaje;
    public $autor;
    
    public function __construct() {
        parent::__construct(); //Llamada al constructor padre.
        $this->id_bloqueado        = 0;
        $this->id_tipo          = 6;
        $this->id_planta        = 0;
        $this->valido           = 1;
        $this->fecha_inicio     = "";
        $this->fecha_termino    = "";
        $this->id_usuario       = 0;
        $this->id_mensaje       = 0;
        
        $this->mensaje = "";
        $this->autor = "";
        
    }
    
    public function get_registros_arr($interno=null){         
        $arreglo = get_object_vars($this); 
        if($interno == null){
            
            unset(
                    $arreglo['mensaje'],                    
                    $arreglo['autor']
                    //$arreglo['nom_planta']
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