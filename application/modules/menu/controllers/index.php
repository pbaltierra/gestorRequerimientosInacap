<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
 
class Index extends MX_Controller
{
    
    private $arr_plantas;
    private $arr_tipos;
    private $arr_permisos;
    
    public function __construct()
    {
        
        parent::__construct();
        $this->load->model('index_model');
        
    }
    
    public function index()
    {
        $data['nombre']         = $this->session->userdata('nombre')." ".$this->session->userdata('ape_paterno');
        $this->arr_plantas      = $this->session->userdata('plantas');
        $this->arr_tipos        = $this->session->userdata('tipos');
        
        //var_dump($this->arr_tipos);
        //return;
        
        $this->arr_permisos     = $this->session->userdata('permisos');
        
        $obj_acceso = new Acceso_class();
        $resultado = $this->revisar_permisos($obj_acceso,$this->arr_permisos,1,"r"); // 1: Mantenedores
        if($resultado) $data['ver_mant'] = true; else $data['ver_mant'] = false;
        
        $resultado = $this->revisar_permisos($obj_acceso,$this->arr_permisos,2,"r"); // 2: Estadisticas
        if($resultado) $data['ver_esta'] = true; else $data['ver_esta'] = false;
        
        $resultado = $this->revisar_permisos($obj_acceso,$this->arr_permisos,4,"r"); // 4: Mantenedor plantas
        if($resultado) $data['ver_mant_plan'] = true; else $data['ver_mant_plan'] = false;
        
        $resultado = $this->revisar_permisos($obj_acceso,$this->arr_permisos,10,"r"); // 10: Mantenedor de capacidades
        if($resultado) $data['ver_mant_cap'] = true; else $data['ver_mant_cap'] = false;
        
        $data['menu_plantas']   = $this->crear_menu_plantas($this->arr_plantas);        
        $data['menu_tipos']     = $this->crear_menu_tipos($this->arr_tipos);
        $data['nom_planta']     = $this->session->userdata('nom_planta');
        if(strlen($data['nom_planta'])>10){    
            $data['nom_planta']     = substr($this->session->userdata('nom_planta'), 0, 10)."...";
        }    
        
        $data['id_planta']      = $this->session->userdata('id_planta');
        
        $this->load->view('index',$data);
     }
    
    public function revisar_permisos($obj_acceso,$arr_permisos,$id_seccion,$criterio){
        //$resultado = false;
        $obj_acceso->id_seccion     = $id_seccion; 
        $obj_acceso->criterio       = $criterio;
        $obj_acceso->arr_permisos   = $arr_permisos;
        
        return $obj_acceso->verificar_permisos();
    } 

    public function crear_menu_plantas($arr_plantas)
    {  
       $html = "<ul class='dropdown-menu'>"; 
       //var_dump($arr_plantas);
       //return;
       
       if($arr_plantas){
        foreach($arr_plantas as $valor){
            $html .="<li>";
            $html .="<a href='".base_url()."index.php/login/index/".$valor['id_planta']."'>";
            $html .= $valor['nom_planta'];
            $html .="</a>";
            $html .="</li>";
        }
       } 
       $html .= "</ul>";
       return $html;
    }
    
    public function crear_menu_tipos($arr_tipos)
    {  
      // $html = "<ul class='dropdown-menu'>"; 
       $html = "";
       $html .="<li class='nav-header'>Perfil</li>";
       foreach($arr_tipos as $valor){
           $html .="<li class='disabled'>";
           $html .="<a href='javascript:void(0);'>";
           $html .= $valor['nombre'];
           $html .="</a>";
           $html .="</li>";
       }
       //$html .= "</ul>";
       return $html;
    }
}
/*
*end modules/login/controllers/index.php
*/  