<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Usuario_planta_class {
    //put your code here
    
    Public $arreglo;
    Public $tabla;
    Public $grupo;
    Public $campos;
    
    public function __construct()
    { 
        $this->arreglo  = array();
        $this->tabla    = "usuario_planta";
        $this->grupo    = null;
        $this->campos   = null;
    }
        
    public function get_usuario($id_planta=null, $id_usuario = null, $grupo=null){        
                
        //$limite     = null;
        $orden      = "usuario.user";
        //$id_planta = 2;
        //echo "planta=".$id_planta;
        
        $direccion  = "asc";
        $campos     = "usuario.*, planta.nombre nom_planta";
        $condicion  = array();
        
        if($this->grupo != null){
            $grupo = $this->grupo;
        }else{
            $grupo = "usuario.id_usuario";
        }
        
        $condicion  = array(
                            "usuario.valido !="     => 0, 
                            "planta.valido !="      => 0 
                            );
        
        
        if($id_planta!= null)   { $condicion += array("usuario_planta.id_planta"  =>  $id_planta);  }
        if($id_usuario!= null)  { $condicion += array("usuario.id_usuario" => $id_usuario)         ;}
        //if($cliente != null)    { $condicion += array("ov.cliente" => $cliente)     ;}
        
        $join       = array(
                            array("tabla" =>"planta", "condicion" => "usuario_planta.id_planta = planta.id_planta"),
                            array("tabla" =>"usuario", "condicion" => "usuario_planta.id_usuario = usuario.id_usuario")
                            );
        
        $this->arreglo = array(
                        "campos"        => $campos,
                        "condicion"     => $condicion,
                        "orden"         => $orden,
                        "direccion"     => $direccion,
                        "join"          => $join,
                        "grupo"         => $grupo
                        );        
    }
    
    public function get_registro_campo($campo=null,$valor=null){  
        $campos     = "usuario_planta.*";
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
    
    
    public function get_registro_campo_full($campo,$valor,$id_planta=null){  
        
        if ($this->campos == null){
            $campos     = "usuario.*,planta.id_planta,planta.nombre nom_planta";
        }else{
            $campos     = $this->campos;
        }
        $condicion  = array(                           
                            $campo                  =>  $valor,
                            "usuario.valido !="     => 0, 
                            "planta.valido !="      => 0
                            );
        
        //$this->arreglo = array( "campos"        => $campos, "condicion"     => $condicion  );    
        
        
        
       if($id_planta!= null)   { $condicion += array("usuario_planta.id_planta"  =>  $id_planta);  }
       
       
       $join       = array(                            
                            array("tabla" =>"usuario", "condicion" => "usuario_planta.id_usuario = usuario.id_usuario"),
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
        
    
}
?>
