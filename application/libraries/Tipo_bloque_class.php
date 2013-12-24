<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Tipo_bloque_class {
    //put your code here
    
    Public $arreglo;
    Public $tabla;
    
    public function __construct()
    { 
        $this->arreglo  = array();
        $this->tabla    = "tipo_bloque";
    }
    
    public function get_tipos_blo(){       
               
        $campos         = "*";
        $orden          = array(array("campo"=>"orden", "direccion" => "asc"));
        $this->arreglo  = array(
                        "campos"    => $campos,
                        "orden"     => $orden
                        );      
    }
    
    
}
?>
