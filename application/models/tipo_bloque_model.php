<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Tipo_bloque_model extends CI_Model
{
    Public $id;
    Public $nombre;       
    Public $color_texto;
    Public $color_fondo;
    Public $tabla;
    
    public function __construct()
    {        
        parent::__construct();  
        
        $this->id_tipo      = 0;
        $this->nombre       = "";
        $this->color_fondo  = "";
        $this->color_texto  = "";
        
        $this->tabla        = "tipo_bloque";
        
    }
    
    
    
    /*   
    public function get_registro($criterio="", $valor="", $arreglo=null)
    {
        if (($criterio == "") && ($valor==""))
        {
            $query = $this->db->get($this->tabla);
            return $query->result_array();
        }

        $query = $this->db->get_where($this->tabla, array($criterio => $valor,  $this->campoValido => 1));
        
        if($arreglo == null){
            return $query->row_array();
        }else if ($arreglo == 1){
            return $query->result_array();
        }  
    }
    
    public function get_registros($campos="*", $condicion = null, $arreglo = null, $operador=null, $orden = null, $direccion = "desc", $limite = null, $join=array()){
        
        $this->db->select($campos);
        $this->db->from($this->tabla);
        if(($operador==null) || ($operador=="and")){
            if($condicion   != null)    { $this->db->where($condicion); }                
        }else if($operador=="or"){
            if($condicion   != null)    { $this->db->or_where($condicion); } 
        }       
        
        foreach ($join as $value) {
            if(     ($value["tabla"]!=null) && ($value["condicion"]!=null)    ){
               $this->db->join($value["tabla"],$value["condicion"]); 
            }
        }           
        
        if($orden       != null)    { $this->db->order_by($orden,$direccion); }
        if($limite      != null)    { $this->db->limit($limite); }
        
        $query = $this->db->get();
        if($arreglo == null){
            return $query->result_array();            
        }else if ($arreglo == 1){
            return $query->row_array();
        }          
    }   
    */
    
    
    public function get_registros_arr(){         
         $arreglo = get_object_vars($this);
         return $arreglo;         
    }
    
    public function set_registro_arr($arreglo){        
        reset($arreglo);  
        while (list($key, $value) = each($arreglo)) {  
          $this->$key = $value;  
        }         
    }
    
    
}
/*
*end modules/login/models/index_model.php
*/