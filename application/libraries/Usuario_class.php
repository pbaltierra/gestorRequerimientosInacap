<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Usuario_class {
    //put your code here
    
    Public $arreglo;
    Public $tabla;
    Public $grupo;
    Public $excepcion;
    
    public function __construct()
    { 
        $this->arreglo  = array();
        $this->tabla    = "usuario";
        $this->grupo    = null;
        $this->excepcion = null;
    }
        
    public function get_usuario($id_planta=null, $id_usuario = null){        
                
        //$limite     = null;
        $orden      = "usuario.id_sap";
        //$id_planta = 1;
        //echo "planta=".$id_planta;
        
        $direccion  = "asc";
        $campos     = "usuario.*";
        $condicion  = array();
        if($this->grupo == null){ 
           $grupo = "usuario.id_usuario";
        }else{
            $grupo = $this->grupo;
        }
        
        //echo $id_planta;
        
        
        //if($id_planta!= null)   { $condicion += array("usuario_planta.id_planta"  =>  $id_planta)       ;}
        if($id_usuario!= null)  { $condicion += array("usuario.id_usuario" => $id_usuario)       ;}
        if($this->excepcion!= null)  { $condicion += array("usuario.id_usuario !=" => $this->excepcion)       ;}
        
        //if($cliente != null)    { $condicion += array("ov.cliente" => $cliente)     ;}
        /*
        $join       = array(                            
                            array("tabla" =>"usuario_planta", "condicion" => "usuario.id_usuario=usuario_planta.id_usuario"),
                            
                            array("tabla" =>"planta", "condicion" => "usuario_planta.id_planta = planta.id_planta")
                            );
        */
        
        $this->arreglo = array(
                        "campos"        => $campos,
                        "condicion"     => $condicion,
                        "orden"         => $orden,
                        "direccion"     => $direccion,
                        //"join"          => $join,
                        "grupo"         => $grupo
                        );        
    }
    
    /*
    public function get_registro_campo($campo,$valor,$id_planta=null){  
        $campos   = "usuario.*,planta.id_planta";
        
        $condicion  = array(                           
                            $campo  =>  $valor
                            );
        
        $this->arreglo = array(
                        "campos"        => $campos,
                        "condicion"     => $condicion
                        );    
               
       if($id_planta!= null)   { $condicion += array("usuario_planta.id_planta"  =>  $id_planta);  }
       
       $join       = array(
                            
                            array("tabla" =>"usuario_planta", "condicion" => "usuario_planta.id_usuario = usuario.id_usuario"),
                            array("tabla" =>"planta", "condicion" => "usuario_planta.id_planta = planta.id_planta")
                            );
       
       if($this->grupo == null){ 
           $grupo = "usuario.id_usuario";
       }else{
           $grupo = $this->grupo;
       }
       $this->arreglo = array(
                        "campos"        => $campos,
                        "condicion"     => $condicion,
                        //"orden"         => $orden,
                        //"direccion"     => $direccion,
                        "grupo"         => $grupo,
                        "join"          => $join
                        );     
         
    }
    */
    
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
