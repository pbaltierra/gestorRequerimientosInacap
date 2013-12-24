<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Capacidad_class {
    //put your code here
    
    Public $arreglo;
    Public $tabla;
    
    public function __construct()
    { 
        $this->arreglo  = array();
        $this->tabla    = "capacidad";
    }
    
    public function get_capacidades_dh($desde,$id_planta,$hasta=null){
       
        if($hasta == null){
            $condicion      = array(
                            'fecha_inicio <=' => $desde, 
                            'id_planta =' => $id_planta 
                            );
            $direccion      = "desc";
            $limite         = 1;
        }else{
            $condicion = array(
                        'fecha_inicio >=' => $desde, 
                        'fecha_inicio <=' => $hasta, 
                        'id_planta =' => $id_planta 
                        );
            $direccion  = "asc";
            $limite     =   null;
        }
        
        
        $campos         = "fecha_inicio,capacidad";
        $orden          = "fecha_inicio";
        
        
        $this->arreglo = array(
                        "campos"    => $campos,
                        "condicion" => $condicion,
                        "orden"     => $orden,
                        "direccion" => $direccion,
                        "limite"    => $limite
                        );      
       
    }
    
    
    public function get_registro_campo($campo,$valor,$id_planta=null){  
        $campos   = $this->tabla.".*, planta.nombre nom_planta";
        
        $orden = array(                           
                            array('campo'=>'capacidad.fecha_inicio','direccion'=>'desc')
                            );
        
        $condicion  = array(                           
                            $this->tabla.".".$campo  =>  $valor
                            );
        
        if($id_planta!=null){
            $condicion +=   array($this->tabla.".id_planta"   =>  $id_planta);
        }
        
        $join       = array(
                        array('tabla'=>'planta','condicion'=>'planta.id_planta=capacidad.id_planta')
                        );
        
        
        $this->arreglo = array(
                        "campos"        => $campos,
                        "condicion"     => $condicion,
                        "join"          => $join,
                        "orden"         => $orden
                        );     
    }
    
    
    
}
?>
