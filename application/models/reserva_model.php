<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Reserva_model extends CI_Model {
    //put your code here
    Public $id_reserva;
    Public $prioridad;
    Public $id_usuario;
    Public $valido;
    Public $fecha_creacion;
    Public $fecha_actualizacion;
    Public $fecha;
    Public $capacidad;
    Public $kilogramos;
    Public $piezas;  
    Public $id_ov;  
    Public $id_ovsap;
    //Public $id_planta;
    
    // Atributos externos a la bd relacional
    //Public $tabla;
    Public $fecha_for;    
    //Public $campoValido;
    Public $id_tipo;
    Public $cliente;
    
    public function __construct()
    {        
        //$this->load->model('crud_model');
        
        $this->id_reserva           = 0;
        $this->prioridad            = 0;
        $this->id_usuario           = 0;
        $this->valido               = 1;
        $this->fecha_creacion       = 0;
        $this->fecha_actualizacion  = 0;
        $this->fecha                = 0;
        $this->capacidad            = 0;
        $this->kilogramos           = 0;
        $this->piezas               = 0;  
        $this->id_ov                = 0;
        
        
        // Atributos externos a la bd relacional
        $this->fecha_for            = 0;        
        //$this->tabla                = "reserva";        
        $this->id_tipo              = 0;
        $this->cliente              = "";
        $this->id_ovsap             = 0;
        //$this->campoValido          = "valido";
        
    }    
    public function set_fecha($fecha){
        $this->fecha_for = date("d/m/Y H:i",$fecha);
    }
        
    public function get_registros_arr($interno=null){         
        $arreglo = get_object_vars($this); 
        if($interno == null){
            unset(
                    $arreglo['fecha_for'],                    
                    $arreglo['id_tipo'],
                    $arreglo['cliente'],
                    $arreglo['id_ovsap'],
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
