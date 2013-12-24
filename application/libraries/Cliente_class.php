<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Cliente_class {
    //put your code here
    
    Public $arreglo;
    Public $tabla;
    Public $campos;
    
    public function __construct()
    { 
        $this->arreglo  = array();
        $this->tabla    = "cliente";
    }
    
    public function get_registro_campo($campo=null,$valor=null){  
        
        if($this->campos == null){
            $campos     = $this->tabla.".*";
        }else{
            $campos =   $this->campos;
        }
        
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
