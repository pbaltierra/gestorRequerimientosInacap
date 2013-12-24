<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Ov_model extends CI_Model {
    //put your code here
    Public $id_ov;
    Public $estado;
    Public $id_usuario;
    Public $valido;
    Public $fecha_creacion;
    Public $fecha_actualizacion;
    Public $id_tipo;
    Public $capacidad;
    Public $kilogramos;
    Public $piezas;  
    Public $cliente;
    Public $id_planta;
    Public $fecha_entrega;
    Public $comentario;
    Public $id_material;
    Public $material;
    Public $id_cliente;
    Public $posicion;
    //Public $id_ovsap;
    Public $id_ovsap_old;
    
    //Public $tabla;
    Public $fecha;
    //Public $campoValido;
    Public $tipo_nombre;
    
    public function __construct()
    {        
        $this->id_ov                = 0;
        $this->estado               = 0;
        $this->id_usuario           = 0;
        $this->valido               = 1;
        $this->fecha_creacion       = 0;
        $this->fecha_actualizacion  = 0;
        $this->id_tipo              = 0;        
        $this->capacidad            = 0;
        $this->kilogramos           = 0;
        $this->piezas               = 0;  
        $this->cliente              = "";
        $this->id_planta            = 0;
        $this->fecha_entrega        = "";
        $this->comentario           = "";
        $this->id_material          = "";
        $this->material             = "";
        $this->id_cliente           = 1;
        $this->posicion             = 0;
        //$this->id_ovsap             = 0;
        $this->id_ovsap_old         = 0;
        
        $this->fecha                = "";
        $this->tipo_nombre          = "";
    }
    
    public function set_fecha($fecha){
        //$this->fecha = date("d/m/Y H:m",$fecha);
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
    
    
    
    
    public function get_registros($campos="*", $condicion = null, $arreglo = null, $operador=null, $orden = null, $direccion = "desc", $limite = null, $join=null, $grupo=null){
        
        $this->db->select($campos);
        $this->db->from($this->tabla);
        if(($operador==null) || ($operador=="and")){
            if($condicion   != null)    { $this->db->where($condicion); }                
        }else if($operador=="or"){
            if($condicion   != null)    { $this->db->or_where($condicion); } 
        }       
        
        if($join!=null){
            foreach ($join as $value) {
                if(     ($value["tabla"]!=null) && ($value["condicion"]!=null)    ){
                   $this->db->join($value["tabla"],$value["condicion"]); 
                }
            }           
        }
        if($orden       != null)    { $this->db->order_by($orden,$direccion); }
        if($limite      != null)    { $this->db->limit($limite); }
        if($grupo       != null)    { $this->db->group_by($grupo); }
        
        $query = $this->db->get();
        if($arreglo == null){
            return $query->result_array();            
        }else if ($arreglo == 1){
            return $query->row_array();
        }          
    }   
    
    
    
    public function get_clientes(){
        
        $sql = "SELECT distinct cliente,id_ov FROM ".$this->tabla." where cliente is not null group by cliente";
        
        $query = $this->db->query($sql);
        return $query->result_array();
    }
    
    */
    
    public function get_registros_arr($interno=null){         
         
        $this->get_cliente();
        $arreglo = get_object_vars($this);
                
         if($interno == null){
            unset(
                    $arreglo['tipo_nombre'],
                    $arreglo['fecha'],
                    //$arreglo['valido'],
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
        $this->set_fecha_entrega($this->fecha_entrega);  
        $this->set_cliente($this->cliente); 
        $this->set_capacidad($this->capacidad);
        $this->set_kilogramos($this->kilogramos);
        $this->set_piezas($this->piezas);
    }
    
    public function set_fecha_entrega($fecha_entrega){
        $fecha_entrega = strtotime($fecha_entrega);
        //$arr_fecha = explode("/", $fecha_entrega);
        //return;
        
        $fecha = mktime(23,59,59,date("m",$fecha_entrega),date("d",$fecha_entrega),date("Y",$fecha_entrega));
        $this->fecha_entrega = date("Y-m-d H:i",$fecha);
    }
    
    public function set_cliente($cliente){
        $this->cliente = strtoupper($cliente);
    }
    
    public function get_cliente(){
        $this->cliente = strtoupper($this->cliente);
        return $this->cliente;
    }
    
    public function set_capacidad($capacidad){
        $this->capacidad = doubleval($capacidad);
        return $this->capacidad;
    }
    
    public function set_kilogramos($kilogramos){
        $this->kilogramos = doubleval($kilogramos);
        return $this->kilogramos;
    }
    public function set_piezas($piezas){
        $this->piezas = doubleval($piezas);
        return $this->piezas;
    }
    /*
    public function add_registro(){
        try {
            $data = array(
                    'id_usuario'            => $this->id_usuario,
                    'capacidad'             => $this->capacidad,
                    'kilogramos'            => $this->kilogramos,
                    'piezas'                => $this->piezas,
                    'id_planta'             => $this->id_planta,
                    'valido'                => $this->valido,
                    'id_tipo'               => $this->id_tipo,
                    'cliente'               => $this->cliente
             );

             if($this->db->insert($this->tabla, $data) ){
                 return true;
             }else{
                 return false;
             }
             
        }catch(Exception $ex){
            echo 'Excepción capturada: ',  $e->getMessage(), "\n";
        }
    
    }
     * 
     */
}

?>
