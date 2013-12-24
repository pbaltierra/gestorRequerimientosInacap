<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Crud_model extends CI_Model {
    //put your code here

    Public $valido;
    Public $fecha_creacion;
    Public $fecha_actualizacion;
    Public $tabla;
    Public $fecha;
    Public $campoValido;
    Public $id_ultimo;

    Public $data;
    Public $campos; 
    Public $condicion; // array
    Public $operador;
    Public $orden;
    Public $direccion;
    Public $limite;
    Public $join; // array
    Public $grupo;
    Public $actualizacion; //array
    Public $like;

    public function __construct()
    {        
        $this->valido               = 1;
        $this->fecha_creacion       = 0;
        $this->fecha_actualizacion  = 0;
        $this->tabla                = "";
        $this->campoValido          = "valido";
        $this->campoIndice          = "id_".$this->tabla;
        
        

        $this->campos       = "*"; 
        $this->condicion    = null; // array
        $this->operador     = null;
        $this->orden        = null;
        $this->direccion    = "asc";
        $this->limite       = null;
        $this->join         = null; // array
        $this->grupo        = null;
        $this->actualizacion = null;
        $this->like         =null;
    }
    
    //public function get_registros($campos="*", $condicion = null, $operador=null, $orden = null, $direccion = "asc", $limite = null, $join=null, $grupo=null){
    public function get_registros($arreglo=null,$tipo=null,$todos=null){          
        
        if($arreglo!=null)  $this->set_select($arreglo);
        
        switch ($tipo){
            
            default     :   $this->db->select($this->campos); break;
            case "sum"  :   $this->db->select_sum($this->campos); break;
        }
        
        $this->db->from($this->tabla);
        if(($this->operador==null) || ($this->operador=="and")){
            if($this->condicion   != null)    { $this->db->where($this->condicion); }                
        }else if($this->operador=="or"){
            if($this->condicion   != null)    { $this->db->or_where($this->condicion ); } 
        }       
        //var_dump($this->join);
        if($this->join!=null){
            foreach ($this->join as $value) {
                if(     ($value["tabla"]!=null) && ($value["condicion"]!=null)    ){
                    if(isset($value["tipo"])){
                        $this->db->join($value["tabla"],$value["condicion"], $value["tipo"]); 
                    }else{
                        $this->db->join($value["tabla"],$value["condicion"]); 
                    }
                }
            }           
        }
        
        $cont = 0; 
        if($this->like!=null){
            foreach ($this->like as $value) {
                if(     ($value["campo"]!=null) && ($value["valor"]!=null)    ){
                    if(isset($value["operador"])){
                        //$this->db->join($value["tabla"],$value["condicion"], $value["tipo"]); 
                        //if($cont == 0){$this->db->like($this->campoValido, $this->valido); $cont++;}
                        if(isset($value["wildcard"])){
                            $this->db->or_like($value["campo"], $value["valor"],$value["wildcard"]);
                        }else{
                            $this->db->or_like($value["campo"], $value["valor"]);
                        }
                    }else{
                        if(isset($value["wildcard"])){
                            $this->db->like($value["campo"], $value["valor"],$value["wildcard"]);
                        }else{
                            $this->db->like($value["campo"], $value["valor"]);
                        }
                        //$this->db->join($value["tabla"],$value["condicion"]); 
                    }
                }
            }           
        }
        
        
        if($todos == null){
            $this->db->where(array($this->tabla.".".$this->campoValido => $this->valido));
        }
        
        
        if( ($this->orden       != null) && (!is_array($this->orden))   )    { 
            $this->db->order_by($this->orden,$this->direccion);            
        }else if(is_array($this->orden)){
            foreach ($this->orden as $value) {
                $this->db->order_by($value["campo"],$value["direccion"]);  
            }   
        }    
               
        if($this->limite      != null)    { $this->db->limit($this->limite); }
        if($this->grupo       != null)    { $this->db->group_by($this->grupo); }
        
        $query = $this->db->get();
        
        return $query->result_array();
        //return $query->row_array();                
    }   
 
    
    public function get() {
        $offset = $this->input->post('offset');
        $parametro = $this->input->post('parameter');
        $tabla = $this->input->post('tabla');
        $model = $tabla.'_model';
        $limit = $this->input->post('limit');
        if ($parametro != '') {
            $total = $this->$model->count_like($parameter);
            $response = $this->$model->get_like($parameter, $offset, $limit);
        } else {
            $total = $this->$model->count();
            $response = $this->$model->get($offset, $limit);
        }
        //$data['paginacion'] = $this->paginar($offset, $limit, $total, 'offset');
        //$data['resposne'] = $response;
        echo json_encode($data);
    }
    
    public function get_registros_arr(){         
         $arreglo = get_object_vars($this);
         return $arreglo;         
    }
    
    public function set_select($arreglo){     
        reset($arreglo);  
        while (list($key, $value) = each($arreglo)) {  
          $this->$key = $value;  
        }         
    }
    
    
    public function add_registro($data){
        
            if($this->db->insert($this->tabla, $data) ){
                $this->id_ultimo = $this->db->insert_id();
                return 1;
            }else{
                return 0;
            }
            
    }
    
    public function update_registro($arreglo){
        if($arreglo!=null)  $this->set_select($arreglo);
        
        if(($this->operador==null) || ($this->operador=="and")){
            if($this->condicion   != null)    { $this->db->where($this->condicion); }                
        }else if($this->operador=="or"){
            if($this->condicion   != null)    { $this->db->or_where($this->condicion ); } 
        }   
        
        if( $this->db->update($this->tabla, $this->actualizacion, $this->condicion) ){
            return true;
        }else{
            return false;
        }
    }
    
    
    public function delete_registro($arreglo){
        if($arreglo!=null)  $this->set_select($arreglo);
        
        if(($this->operador==null) || ($this->operador=="and")){
            if($this->condicion   != null)    { $this->db->where($this->condicion); }                
        }else if($this->operador=="or"){
            if($this->condicion   != null)    { $this->db->or_where($this->condicion ); } 
        }   
        
        if($this->actualizacion==null){
            $this->actualizacion = array(
                                        $this->tabla.".".$this->campoValido => 0
                                        ); 
        }
        
        if( $this->db->update($this->tabla, $this->actualizacion, $this->condicion) ){
            return true;
        }else{
            return false;
        }
    }
    
}

?>
