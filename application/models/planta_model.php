<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Planta_model extends CI_Model {
    //put your code here
    Public $canal;
    Public $id_interno;
    Public $id_planta;
    Public $nombre;
    Public $direccion;
    Public $direccion2;
    Public $valido;
    Public $capacidad;
    Public $fecha_creacion;
    Public $fecha_actualizacion;
    
    //Public $obj_capacidad;
    //Public $arr_capacidades;
    
    Public $tabla;    
    Public $campoValido;
    
    
    public function __construct()
    {        
        $this->id_planta            = 0;
        $this->nombre               = "";
        $this->direccion            = "";
        $this->direccion2           = "";
        $this->valido               = 1;
        $this->fecha_actualizacion  = 0;
        $this->fecha_creacion       = 0;
        $this->capacidad            = 0;
        $this->canal                = "";
        $this->id_interno           = "";
        //$this->load->model('crud_model');
        //$this->obj_capacidad = new Capacidad_model();        
        $this->tabla                = "planta";
        $this->campoValido          = "valido";
        
        //$this->obj_crud             = new Crud_model();
    }
    
    public function get_registros_arr($interno=null){         
         $arreglo = get_object_vars($this);
         if($interno == null){
            unset(
                    $arreglo['campoValido'],                    
                    $arreglo['tabla'],
                    $arreglo['capacidad'],
                    $arreglo['fecha_creacion'],
                    $arreglo['fecha_actualizacion']
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
