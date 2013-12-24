<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Tipo_ov_class {
    //put your code here
    
    Public $arreglo;
    Public $tabla;
    
    public function __construct()
    { 
        $this->arreglo  = array();
        $this->tabla    = "tipo_ov";
    }
    
    public function get_registro_campo($campo=null,$valor=null){  
        $campos     = $this->tabla.".*";
        $condicion  = null;
        
        if(     ($campo!=null) && ($valor!=null)    ){
        $condicion  = array(                           
                            $campo  =>  $valor
                            );
        }        
        $this->arreglo = array(
                        "campos"        => $campos,
                        "condicion"     => $condicion
                        );     
    }
    
    
}
?>
