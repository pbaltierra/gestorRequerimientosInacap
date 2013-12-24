<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Reserva_class {
    //put your code here
    
    Public $arreglo;
    Public $tabla;
    
    public function __construct()
    { 
        
        $this->arreglo  = array();
        $this->tabla    = "reserva";
    }
    
    public function get_reservas_dh($desde,$hasta,$id_planta){         
        
        $campos      = "reserva.*,ov.id_tipo,ov.cliente, ov.id_planta";
        $condicion = array(
                        'reserva.fecha >'   => $desde, 
                        'reserva.fecha <'   => $hasta,
                        'ov.id_planta '     => $id_planta
                        );        
        $join       = array(
                        array('tabla'=>'ov','condicion'=>'reserva.id_ov=ov.id_ov')
                        );
        
        $orden      = "fecha";
        
        $this->arreglo = array(
                            "campos"        => $campos,
                            "condicion"     => $condicion,
                            "join"          => $join,
                            "orden"         => $orden
                        );        
    }
    
    
    public function get_reservas_hora($hora_for,$id_planta){
        
        $campos     = "reserva.*,ov.id_ovsap,ov.id_planta, ov.cliente, tipo_ov.nombre id_tipo";
        $condicion  = array(
                            "ov.id_planta"  =>  $id_planta,
                            "reserva.fecha" =>  $hora_for
                            );        
        $join       = array(
                        array('tabla'=>'ov','condicion'=>'reserva.id_ov=ov.id_ov'),
                        array('tabla'=>'tipo_ov','condicion'=>'ov.id_tipo=tipo_ov.id_tipo')
                        );
        
        $this->arreglo  = array(
                        "campos"    => $campos,
                        "condicion" => $condicion,
                        "join"      => $join
                        );
    }
    
    public function get_reservas_ov($id_ov,$id_planta){
        
        $campos     = "reserva.*,ov.id_planta";
        $condicion  = array(
                            "reserva.id_ov" =>$id_ov,
                            "ov.id_planta"  =>$id_planta    
                            );
        $join       = array(
                        array('tabla'=>'ov','condicion'=>'reserva.id_ov=ov.id_ov')
                        );
        $this->arreglo  = array(
                        "campos"        => $campos,
                        "condicion"     => $condicion,
                        "join"          => $join
                        );      
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
