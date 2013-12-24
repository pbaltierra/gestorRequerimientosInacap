<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Bloqueado_class {
    //put your code here
    
    Public $arreglo;
    Public $tabla;
    
    public function __construct()
    { 
        $this->arreglo  = array();
        $this->tabla    = "bloqueado";
    }
    
   public function get_bloqueados_dh($desde,$hasta,$id_planta){         
        
        $campos      = "bloqueado.*,usuario.email autor,mensaje.mensaje";
        $condicion = array(
                        'bloqueado.fecha_inicio >='   => $desde, 
                        'bloqueado.fecha_termino <='   => $hasta,
                        'bloqueado.id_planta '     => $id_planta
                        );        
        $join       = array(
                        array('tabla'=>'usuario','condicion'=>'bloqueado.id_usuario=usuario.id_usuario'),
                        array('tabla'=>'mensaje','condicion'=>'bloqueado.id_mensaje=mensaje.id_mensaje')
                        );
        $grupo  = "bloqueado.id_bloqueado";
        //$orden      = "fecha";
        
        $this->arreglo = array(
                            "campos"        => $campos,
                            "condicion"     => $condicion,
                            "join"          => $join,
                            "grupo"         => $grupo
                        );        
    }
    
    
    public function get_registro_campo($campo,$valor,$id_planta=null){  
        $campos   = $this->tabla.".*, mensaje.mensaje";
        /*
        $orden = array(                           
                            array('campo'=>'capacidad.fecha_inicio','direccion'=>'desc')
                            );
        */
        $condicion  = array(                           
                            $this->tabla.".".$campo  =>  $valor
                            );
        
        if($id_planta!=null){
            $condicion +=   array($this->tabla.".id_planta"   =>  $id_planta);
        }
        
        $join       = array(
                        array('tabla'=>'mensaje','condicion'=>'mensaje.id_mensaje=bloqueado.id_mensaje')
                        );
        
        
        $this->arreglo = array(
                        "campos"        => $campos,
                        "condicion"     => $condicion,
                        "join"          => $join
                        //"orden"         => $orden
                        );     
    }
    
    
    
}
?>
