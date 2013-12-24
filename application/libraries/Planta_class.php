<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Planta_class {
    //put your code here
    
    Public $arreglo;
    Public $tabla;
    Public $campos;
    
    public function __construct()
    { 
        $this->arreglo  = array();
        $this->tabla    = "planta";
        $this->campos   = "";
    }
        
    public function get_planta($id_planta = null){        
        
        //$fecha_hoy = date("Y-m-d H:m"); 
        
        $orden      = array(
                        array('campo'=>'planta.id_planta','direccion'=>'asc')
                        //array('campo'=>'capacidad.fecha_inicio','direccion'=>'desc')
                        );
        $grupo      = "planta.id_planta";
        
        $campos     = "planta.*";
        $condicion  = array();
        if($id_planta != null)    { $condicion += array("planta.id_planta" => $id_planta)         ;}
                
        //$condicion += array("capacidad.fecha_inicio <" => $fecha_hoy);
                
        $join       = array(
                        array('tabla'=>'capacidad','condicion'=>'planta.id_planta=capacidad.id_planta')
                        );        
        $this->arreglo = array(
                        "campos"        => $campos,
                        "condicion"     => $condicion,
                        "orden"         => $orden
                        //"direccion"     => $direccion,
                        //"grupo"         => $grupo,
                        //"join"          => $join            
                        );          
    }
    
    public function get_registro_campo($campo=null,$valor=null){  
        
        if($this->campos == null){
            $campos     = $this->tabla.".*";
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
