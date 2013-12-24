<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Capacidad_model extends CI_Model {
    //put your code here
    Public $id_capacidad;
    Public $fecha_creacion;
    Public $fecha_actualizacion;    
    Public $fecha_inicio;
    Public $capacidad;
    Public $id_planta;
    Public $valido;
    
    //Public $tabla;    
    //Public $campoValido;
    Public $nom_planta;
    
    public function __construct()
    {        
        $this->id_capacidad         = 0;
        $this->fecha_actualizacion  = "";
        $this->fecha_creacion       = "";
        $this->fecha_inicio         = "";
        $this->capacidad            = 0;
        $this->id_planta            = 0;
        $this->valido               = 1;
        
        //$this->tabla                = "capacidad";
        //$this->campoValido          = "valido";
        $this->nom_planta           = "";
    }
 
    
    public function formatear_fecha(){
        //echo "fecha ini ".$this->fecha_inicio;
        if($this->fecha_inicio!=null){
            $arr_fecha  = explode('/',  $this->fecha_inicio);//strtotime($this->fecha_inicio);
            $fecha_b    = mktime(null, null, null, $arr_fecha[1], $arr_fecha[0], $arr_fecha[2]);
            $this->fecha_inicio  = date("Y-m-d H:i",$fecha_b);
        }
        
        //return $fecha;
    }    
    
    public function get_registros_arr($interno=null){         
         $arreglo = get_object_vars($this); 
        if($interno == null){
            
            unset(
                    $arreglo['nom_planta']
                    );            
        }         
        return $arreglo;       
    }
    
    public function set_registro_arr($arreglo){        
        reset($arreglo);  
        while (list($key, $value) = each($arreglo)) {  
          $this->$key = $value;  
        }         
    }
    
}

?>
