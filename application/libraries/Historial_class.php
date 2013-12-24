<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Historial_class {
    //put your code here
    
    Public $arreglo;
    Public $tabla;
    Public $limite;
    
    public function __construct()
    { 
        $this->arreglo  = array();
        $this->tabla    = "historial";
        $this->limite   = 0;
    }
    
    public function get_registro_campo($campo=null,$valor=null,$id_planta=null){  
        $campos     = $this->tabla.".*, interaccion.mensaje interaccion, usuario.id_sap nom_usuario, tipo_entidad.mensaje predicado";
        $condicion  = null;
        
        if(     ($campo!=null) && ($valor!=null)    ){
            $condicion  = array(                           
                            $this->tabla.".".$campo  =>  $valor
                            );
        }
        
        if($id_planta != null){  $condicion  += array( "id_planta"  =>  $id_planta ); }
        
        $orden = array(array("campo" => "fecha_creacion", "direccion" => "desc"));
        
        $join       = array(
                        array('tabla'=>'interaccion','condicion'=>'interaccion.id_interaccion=historial.id_interaccion'),
                        array('tabla'=>'usuario','condicion'=>'historial.id_usuario=usuario.id_usuario'),
                        array('tabla'=>'tipo_entidad','condicion'=>'tipo_entidad.id_tipo=historial.objetivo_tipo')    
                        );
        $group      = "historial.id_historial";
        
        $this->arreglo = array(
                        "campos"        => $campos,
                        "condicion"     => $condicion,
                        "join"          => $join,
                        "group"         => $group,
                        "orden"         => $orden
                        );     
        if($this->limite != 0){
            $this->arreglo += array("limite" => $this->limite);
        }
    }
    
    
}
?>
