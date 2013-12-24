<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
 
class Historial extends MX_Controller
{
    /*
    
    private $fecha_actual;
    private $fecha_hasta;
    
    private $obj_dia;
    //private $obj_capacidad;
    */
    private $data;
    private $id_usuario;
    private $id_planta;
    private $dias_min;
    public  $arr_historial;
    public  $tabla;
    /*
    private $arr_tipos_blo;
    
    public $arr_reservas;
    
    public $arr_capacidades;
    
    public $num_dias;
    public $hra_num_inicio;
    */
    
    public function __construct()
    {        
        parent::__construct();
        $this->load->library('session');
        
        
        $this->load->helper('url');
        $this->load->helper('date');
        
        $this->load->database();
        
        $this->load->model('historial_model');        
        $this->load->model('crud_model');
        
        
        $this->id_planta        = $this->session->userdata('id_planta');        
        $this->id_usuario       = $this->session->userdata('id_usuario'); 
        
        $this->dias_min         = 3;
        $this->arr_historial    = array();
        
        $this->tabla            = "";
         
        //echo "planta; ".$this->id_planta;
        
        /*
        $this->load->model('capacidad_model');
        $this->load->model('ov_model');
        $this->load->model('reserva_model');
        $this->load->model('tipo_bloque_model');
        
        //$this->load->library('calendario_class');
        $this->load->library('bloque_class');
        $this->load->library('dia_class');
        
        
        $this->num_dias         = 14;
        $this->hra_num_inicio   = 7;
        $this->arr_reservas     = array();
       
        $this->arr_capacidades  = array();
        
        
        
        //$this->obj_capacidad    = $this->set_capacidad();
        
        $this->obj_dia          = new Dia_class();
        $this->arr_tipos_blo    = array();
        */
    }
    public function index($lim=null)
    {        
       $this->arr_historial = $this->get_historial($lim);
       //var_dump($this->arr_historial);
       if(!$lim){
           $this->crear_tabla();
       }else{
           $this->crear_tabla_full();
       }     
       $this->get_html();       
       
       if(!$lim){
            $this->load->view('index',  $this->data);
       }else{
           echo $this->tabla;
       }
     }
     
     public function get_historial($lim = null){
        $arr_obj = null;
         $obj_historial_c = new Historial_class();
         $tabla = $obj_historial_c->tabla;
         $campo = "id_usuario";
         $valor = $this->id_usuario;
         
         if($lim == null){
            $obj_historial_c->limite = 5;
         }
         $obj_historial_c->get_registro_campo($campo, $valor, $this->id_planta);
         
         $arreglo = $obj_historial_c->arreglo;
         
         $obj_crud = new Crud_model();
         $obj_crud->tabla = $tabla;
                  
         $registros = $obj_crud->get_registros($arreglo);

        $i = 0;        
        foreach ($registros as $valor) {
            $arr_obj[$i] = new Historial_model();             
            $arr_obj[$i]->set_registro_arr($valor);
            $i++;
        } 
        return $arr_obj;
     }
     
     
     public function crear_tabla(){
         
         $this->tabla = '<table id="historial" class="table table-bordered table-hover" >';         
         $this->tabla .='
            <tr> 
                <th> Fecha </th>
                <th> Autor </th>
                <th> Actividad </th>
                <!-- th> Reportar </th -->                
            </tr>';
         
         
         
         if(count($this->arr_historial)>0){
             foreach($this->arr_historial as $valor){
                $fecha_corta = strtotime($valor->fecha_creacion);
                $fecha_corta = date("d/m/Y", $fecha_corta); 
                
                $img_aut        = "<img src='".base_url()."/assets/img/aut.png'>";
                
                $this->tabla .='
                <tr> 
                   <td> '.$fecha_corta.' </td>
                   <td> '.$valor->nom_usuario.' </td>
                   <td> '.$valor->interaccion.' '.$valor->predicado.' '.$valor->objetivo_id.'</td>
                   <!-- td><a href="javascript:void(0);" class="det_ov" data-id-tipo="" data-id-ov="" >'.$img_aut.'</td-->    
                </tr>';
             }
         }
         
         $this->tabla .= '</table>';
     }
     
     
     
     public function crear_tabla_full(){
         
         $this->tabla = '<table id="historial" class="table table-bordered table-hover" >';         
         $this->tabla .='
            <tr> 
                <th> Fecha </th>
                <th> Autor </th>
                <th> Actividad </th>                             
            </tr>';
         
         
         
         if(count($this->arr_historial)>0){
             foreach($this->arr_historial as $valor){
                $fecha_corta = strtotime($valor->fecha_creacion);
                $fecha_corta = date("d/m/Y", $fecha_corta); 
                
                $img_aut        = "<img src='".base_url()."/assets/img/aut.png'>";
                
                $this->tabla .='
                <tr> 
                   <td> '.$fecha_corta.' </td>
                   <td> '.$valor->nom_usuario.' </td>
                   <td> '.$valor->interaccion.' '.$valor->predicado.' '.$valor->objetivo_id.'</td>
                   
                </tr>';
             }
         }
         
         $this->tabla .= '</table>';
     }
     
     
     
     
     public function ov(){
         $fecha = now();
         $fecha_entrega  = mktime(null,null,null,date("m",$fecha),date("d",$fecha)+$this->dias_min,date("Y",$fecha));  
         
         $this->data['fecha_format'] = date("Y-m-d",  $fecha_entrega);        
         $this->load->view('ov',$this->data);
         
     }
     
     public function ov_view($id_ov){
         $obj_ov_c = new Ov_class();
         $obj_ov_c->get_ovs($this->id_planta,$id_ov);
         $arreglo = $obj_ov_c->arreglo;
                  
         $obj_crud = new Crud_model();
         $obj_crud->tabla = $obj_ov_c->tabla;                  
         $registros = $obj_crud->get_registros($arreglo);
         
               
         $i = 0;
         foreach ($registros as $valor) {
            $arr_obj[$i] = new Ov_model();             
            $arr_obj[$i]->set_registro_arr($valor);
            $i++;
         } 
         $this->arr_ovs = $arr_obj;
         
         
         $this->data['cliente']         = $this->arr_ovs[0]->cliente;
         $this->data['capacidad']       = $this->arr_ovs[0]->capacidad;
         $this->data['kilogramos']      = $this->arr_ovs[0]->kilogramos;
         $this->data['piezas']          = $this->arr_ovs[0]->piezas;
         
         $fecha = strtotime($this->arr_ovs[0]->fecha_entrega);
         $fecha_entrega  = mktime(null,null,null,date("m",$fecha),date("d",$fecha),date("Y",$fecha));  
         $this->data['fecha_format']    = date("Y-m-d",  $fecha_entrega);     
         
         $this->data['comentario']      = $this->arr_ovs[0]->comentario;
         
         $this->load->view('ov_view',$this->data);
     }     
     /* 
     public function crear_arr_obj($registros, $obj){
         $i = 0;
         foreach ($registros as $valor) {
            $arr_obj[$i] = $obj;             
            $arr_obj[$i]->set_registro_arr($valor);
            $i++;
        } 
        return $arr_obj;
     }
     */
     
     
     
     
     
     public function get_html(){
         $this->data['tabla'] = $this->tabla;
     }
     
         
     public function insertar_ov($sap=null){    
         
         $respuesta = 0;
         
         $cliente       = $this->input->post('cliente');
         $capacidad     = $this->input->post('capacidad');
         $kilogramos    = $this->input->post('kilogramos');
         $piezas        = $this->input->post('piezas');
         $fecha_entrega = $this->input->post('fecha_entrega');
         $comentario    = $this->input->post('comentario');
         
         if(    ($cliente!=null) && ($capacidad!=null) && ($kilogramos!=null) && ($piezas!=null) && ($fecha_entrega!=null)    ){
             
             $obj_ov_c  = new Ov_class();
             $tabla     = $obj_ov_c->tabla;             
             
             $obj_ov = new Ov_model();
             //$cliente = str_replace("%20"," ",$cliente);
             
             $data = array(
                    'id_usuario'            => $this->id_usuario,
                    'capacidad'             => $capacidad,
                    'kilogramos'            => $kilogramos,
                    'piezas'                => $piezas,
                    'id_planta'             => $this->id_planta,
                    'valido'                => 1,
                    'id_tipo'               => 2,
                    'cliente'               => $cliente,
                    'fecha_entrega'         => $fecha_entrega,
                    'comentario'            => $comentario                     
             );
             //var_dump($data);
             
             
             if($sap!=null)     $data['id_tipo'] = 1;
             
             $obj_ov->set_registro_arr($data);
             $new_data = $obj_ov->get_registros_arr();
             
             //var_dump($new_data);
             //return;
             
             $obj_crud = new Crud_model();
             $obj_crud->tabla = $tabla;
             $respuesta = $obj_crud->add_registro($new_data);
             //$respuesta = $obj_ov->add_registro();
             
             if($respuesta) $respuesta=1;
             else $respuesta = -2;
         }else{
             $respuesta = -1;
         }
         
         echo $respuesta;
         
     }
     
    
}
/*
*end modules/login/controllers/index.php
*/  