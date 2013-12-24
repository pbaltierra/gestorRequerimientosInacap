<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Perfil_class {
    //put your code here
    
    Public $arreglo;
    Public $tabla;
    
    public function __construct()
    { 
        $this->arreglo  = array();
        $this->tabla    = "perfil";
    }
    
    public function get_registro_campo($campo=null,$valor=null){  
        $campos     = "*";
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
            $campos     = "perfil.*,usuario.*";
        }else{
            $campos     = $this->campos;
        }
        $condicion  = array(                           
                            $campo  =>  $valor
                            );
        
        $this->arreglo = array(
                        "campos"        => $campos,
                        "condicion"     => $condicion
                        );    
               
       //if($id_planta!= null)   { $condicion += array("usuario_planta.id_planta"  =>  $id_planta);  }
       
       $join       = array(                            
                            array("tabla" =>"usuario", "condicion" => "usuario.id_usuario = perfil.id_usuario"),
                            array("tabla" =>"tipo_usuario", "condicion" => "tipo_usuario.id_tipo = perfil.id_tipo"),
                            array("tabla" =>"permiso", "condicion" => "permiso.id_tipo = tipo_usuario.id_tipo"),
                            array("tabla" =>"seccion", "condicion" => "permiso.id_seccion = seccion.id_seccion"),
                            );
       
       if($this->grupo == null){ 
           $grupo = "perfil.id_perfil";
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
