<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Tipo_ov_model extends CI_Model {
    
    Public $id_tipo;
    Public $valido;
    Public $nombre;  
    Public $tabla;
    Public $campoValido;
    
    public function __construct()
    {        
        $this->id_tipo              = 0;
        $this->valido               = 1;
        $this->tabla                = "tipo_ov";
        $this->campoValido          = "valido";
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

?>
