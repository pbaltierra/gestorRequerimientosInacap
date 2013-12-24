<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
 
class Mantenedor extends MX_Controller
{
    /*
    private $data;
    private $fecha_actual;
    private $fecha_hasta;
    
    private $obj_dia;
    //private $obj_capacidad;
    */
    private $id_usuario;
    private $id_planta;
    private $tabla_html;
    private $data;
    private $nom_planta;
    
    private $arr_usuarios;
    private $arr_plantas;
    private $arr_capacidades;
    private $arr_msg;
    private $mensajes;
    private $arr_tipos_usu;
    private $arr_permisos;
    private $arr_perfil;
    private $btn_atras;
    private $arr_plantas_usu;
    /*
    
    
    
    public $arr_ovs;
    public $arr_capacidades;
    
    public $num_dias;
    public $hra_num_inicio;
    */
    
    public function __construct()
    {        
        parent::__construct();
        $this->load->library('usuario_class');
        
        $this->load->library('session');
        $this->load->library('table');
        
        $this->load->helper('form');  
        $this->load->helper('url');
        
        $this->load->database();
        
        $this->load->model('historial_model');
        $this->load->model('planta_model');
        $this->load->model('crud_model');
        $this->load->model('usuario_model');
        $this->load->model('perfil_model');
        $this->load->model('capacidad_model');
        $this->load->model('usuario_planta_model');
        
        $this->id_planta        = $this->session->userdata('id_planta');  
        $this->nom_planta       = $this->session->userdata('nom_planta');  
        $this->id_usuario       = $this->session->userdata('id_usuario'); 
        $this->arr_permisos     = $this->session->userdata('permisos'); 
        $this->arr_perfil       = $this->session->userdata('tipos'); 
        $this->arr_plantas_usu  = $this->session->userdata('plantas'); 
        
        
        $this->tabla_html       = "";
        $this->data             = "";
        $this->arr_msg          = array();       
        
        $this->arr_usuarios     = array();  
        $this->arr_tipos_usu    = array();  
        $this->arr_capacidades  = array();
        
        $this->data["msg"]      = "";
        $this->data['id_planta'] = "";
        
        //$this->btn_atras = '<a class="btn btn-success btn-small" href="'.base_url().'/index.php/administracion/index/'.$tipo.'" id="btn_volver">Volver</a>';
        
        
        $tmpl = array ('table_open' => '<table class="table table-bordered table-hover table-condensed table-striped" >');
        $this->table->set_template($tmpl); 
        
        $this->mensajes[0] = "Usuario existente";
        $this->mensajes[1] = "Usuario existente"; 
        $this->mensajes[2] = "Email inválido"; 
        
        $this->mensajes[10] ="No puede autoeliminarse";
        
        $this->mensajes[20] ="Planta inexistente";
        
        $this->data['btn'] = '<a class="btn btn-success btn-small" href="javascript:void(0);" id="btn_crear">Crear</a>'; 
    
        $obj_acceso = new Acceso_class();
        //----------------------------------------
        $resultado = $this->revisar_permisos($obj_acceso,$this->arr_permisos,1,"r"); // 1: mantenedores
        if($resultado)  $this->data['ver_mant'] = true; else  $this->data['ver_mant'] = false;
        //----------------------------------------
        
        $resultado = $this->revisar_permisos($obj_acceso,$this->arr_permisos,3,"r"); // 3: mant. usuarios
        if($resultado)  $this->data['ver_mant_usu'] = true; else  $this->data['ver_mant_usu'] = false;
        
        //----------------------------------------
        
        $resultado = $this->revisar_permisos($obj_acceso,$this->arr_permisos,11,"c"); // 11: crear usuario gral
        if($resultado)  $this->data['crear_usu_gral'] = true; else  $this->data['crear_usu_gral'] = false;
        
        
        //----------------------------------------
        
        $resultado = $this->revisar_permisos($obj_acceso,$this->arr_permisos,4,"r"); // 4: mant. plantas
        if($resultado)  $this->data['ver_mant_pla'] = true; else  $this->data['ver_mant_pla'] = false;
        
        $resultado = $this->revisar_permisos($obj_acceso,$this->arr_permisos,4,"c"); // 4: mant. plantas
        if($resultado)  $this->data['crear_mant_pla'] = true; else  $this->data['crear_mant_pla'] = false;
        
        $resultado = $this->revisar_permisos($obj_acceso,$this->arr_permisos,4,"u"); // 4: mant. plantas
        if($resultado)  $this->data['editar_mant_pla'] = true; else  $this->data['editar_mant_pla'] = false;
        
        $resultado = $this->revisar_permisos($obj_acceso,$this->arr_permisos,4,"d"); // 4: mant. plantas
        if($resultado)  $this->data['eliminar_mant_pla'] = true; else  $this->data['eliminar_mant_pla'] = false;
        
        //----------------------------------------
        
        $resultado = $this->revisar_permisos($obj_acceso,$this->arr_permisos,10,"r"); // 10: Mantenedor de capacidades
        if($resultado) $this->data['ver_mant_cap'] = true; else $this->data['ver_mant_cap'] = false;
        
        //----------------------------------------
        
        $resultado = $this->revisar_permisos($obj_acceso,$this->arr_permisos,5,"r"); // 5: mant. cal
        if($resultado)  $this->data['ver_mant_cal'] = true; else  $this->data['ver_mant_cal'] = false;
        
        $resultado = $this->revisar_permisos($obj_acceso,$this->arr_permisos,5,"u"); // 5: mant. cal
        if($resultado){  
            $this->data['editar_mant_cal'] = true;
            $this->session->set_userdata('modo','admin');
        }else{  
            $this->data['editar_mant_cal'] = false;            
        }
        
        //----------------------------------------
        
        $resultado = $this->revisar_permisos($obj_acceso,$this->arr_permisos,8,"r"); // 8: preferencias
        if($resultado)  $this->data['ver_pref'] = true; else  $this->data['ver_pref'] = false;
        
        $resultado = $this->revisar_permisos($obj_acceso,$this->arr_permisos,8,"u"); // 8: preferencias
        if($resultado)  $this->data['ver_pref'] = true; else  $this->data['ver_pref'] = false;
        
        
    }
    
     public function revisar_permisos($obj_acceso,$arr_permisos,$id_seccion,$criterio){
        //$resultado = false;
        $obj_acceso->id_seccion     = $id_seccion; 
        $obj_acceso->criterio       = $criterio;
        $obj_acceso->arr_permisos   = $arr_permisos;
        
        return $obj_acceso->verificar_permisos();
    } 
    
    
    public function crear($tipo=null){
        
        
           
        
            
        $id_planta = $this->input->post('id_planta');
        
        if($tipo != null){
            if($tipo != "capacidad"){
                $this->data['btn2'] = '<a class="btn btn-success btn-small" href="'.base_url().'index.php/administracion/index/'.$tipo.'" id="btn_volver">Volver</a>';
            }else{
                $this->data['btn2'] = '<a class="btn btn-success btn-small" href="'.base_url().'index.php/administracion/index/planta/'.$id_planta.'" id="btn_volver">Volver</a>';
            }
            
            if($tipo == "planta"){
                $this->data['btn']="";
            }
            
            $vista = "nvo_".$tipo;
            $this->arr_plantas      = $this->get_plantas();
            $this->arr_tipos_usu    = $this->get_tipos_usu(); 
            //var_dump($this->arr_tipos_usu);
            
            if($id_planta != null){
                $this->data['id_planta']    = $id_planta;
                $arr = $this->get_plantas($id_planta);
                $this->data['nom_planta']   = $arr[0]->nombre;
            }
            
            
            
            if(!$this->data['crear_usu_gral']){
                $this->data['combo_plantas']    = $this->nom_planta."<input type='hidden' name='plantas[]' value='".$this->id_planta."' />"; 
            } else{
                $this->data['combo_plantas']    = $this->crear_checkbox_plantas(); 
            } 
            
            $this->data['combo_tipos_usu']  = $this->crear_checkbox_tipos_usu();
            
            $this->load->view($vista,  $this->data);
        }  else {
            
            
            //$this->data['btn'] = '<a class="btn btn-success btn-small" href="javascript:void(0);" id="btn_crear">Crear</a>'; 
        }
    }
    
    
    public function get_plantas($id_planta=null){ 
            $arr_obj = array();
            
            if(!$this->data['crear_usu_gral']){
               $id_planta = $this->id_planta;
            }  
                
                $obj_plantas_c  = new Planta_class();
                $obj_plantas_c->get_planta($id_planta);
                $arreglo    = $obj_plantas_c->arreglo;            

                $obj_crud       = new Crud_model();   
                $obj_crud->tabla = $obj_plantas_c->tabla;            

                $registros = $obj_crud->get_registros($arreglo);
                $i = 0;             
                foreach ($registros as $valor) {
                   $arr_obj[$i] = new Planta_model();             
                   $arr_obj[$i]->set_registro_arr($valor);
                   $i++;
                } 
                //var_dump($arr_obj);
              
            return $arr_obj;              
    }
    
    
    public function get_capacidades($id_planta=null){        
            $arr_obj = array();
            $obj_capacidad_c  = new Capacidad_class();
            
            $campo = "valido";
            $valor = 1;
            $obj_capacidad_c->get_registro_campo($campo, $valor, $id_planta);
            
            $arreglo    = $obj_capacidad_c->arreglo;            
            
            $obj_crud       = new Crud_model();   
            $obj_crud->tabla = $obj_capacidad_c->tabla;            
            
            $registros = $obj_crud->get_registros($arreglo);
            $i = 0;             
            foreach ($registros as $valor) {
               $arr_obj[$i] = new Capacidad_model();             
               $arr_obj[$i]->set_registro_arr($valor);
               $i++;
            } 
            return $arr_obj;              
    }
    
    
    
    public function crear_combo_plantas(){
        $options = array();
        //var_dump($this->arr_plantas);
        foreach($this->arr_plantas as $valor) {
            $options += array($valor->id_planta => $valor->nombre);
        }
        return form_dropdown('id_planta', $options, 'large');
    }
    
    
    public function get_tipos_usu(){  
            $arr_obj = array();
            $obj_tipo_usu_c  = new Tipo_usuario_class();
            $obj_tipo_usu_c->get_registro_campo();
            $arreglo    = $obj_tipo_usu_c->arreglo;            
            
            $obj_crud           = new Crud_model();   
            $obj_crud->tabla    = $obj_tipo_usu_c->tabla;            
            
            $registros = $obj_crud->get_registros($arreglo);
            $i = 0;  
            
            foreach ($registros as $valor) {
               $arr_obj[$i] = new Tipo_usuario_model();         
               $arr_obj[$i]->set_registro_arr($valor);
               $i++;
            } 
            
            if(!$this->data['crear_usu_gral']){
                unset($arr_obj[0]);
            }
            
            
            
            return $arr_obj; 
    }
    
    public function crear_checkbox_tipos_usu($arr_obj_perfil = null){
        //$options = array();
        $html   = "";
        $nombre = "checkbox_";
        //var_dump($this->arr_plantas);
        $i = 0;
        
        
        
        
        foreach($this->arr_tipos_usu as $valor) {
            //$options += array($valor->id_tipo => $valor->nombre);
            $data = array(
                'name'        => "tipos[]",
                'id'          => $nombre.$i,
                'value'       => $valor->id_tipo,
                'checked'     => FALSE,
                'style'       => 'margin:10px',
                'checked2'    => '',
                //'js'          => 'onclick="edit_tipo()"'  
                'js'          => ''  
                );
            if(count($arr_obj_perfil)>0){
                foreach($arr_obj_perfil as $valor2){
                    if(($valor2->id_tipo) == ($valor->id_tipo)){
                        $data['checked']    = TRUE;
                        $data['checked2']   = " checked='checked' ";
                        //echo "true";
                    }
                                    
                }
            }
            $html .= "<div style='50%'>";
            //$html .= form_checkbox($data);
            //$html .= form_checkbox($data['name'],$data['value'],$data['checked'],$data['checked2']);            
            $html .= '<input class="tipo_usu" type="checkbox" value="'.$data['value'].'" name="'.$data['name'].'" id="'.$data['id'].'" style="'.$data['style'].'" '.$data['checked2'].' '.$data['js'].'  >';
            $html .= "<div style='display:inline-block;'>".$valor->nombre."</div>";//form_label($valor->nombre, $nombre.$i);
            $html .= "</div>";
            $i++;
        }
        
        return $html;       
    }
    
    
     public function crear_checkbox_plantas($arr_obj_planta = null){
        $html   = "";
        $nombre = "check_plantas_";
        $i = 0;
        $habilitado = "";
        
        
        
       
        foreach($this->arr_plantas as $valor) {
            $data = array(
                'name'        => "plantas[]",
                'id'          => $nombre.$i,
                'value'       => $valor->id_planta,
                'checked'     => FALSE,
                'style'       => 'margin:10px',
                'checked2'    => '',
                //'js'          => 'onclick="edit_tipo()"'  
                'js'          => ''  
                );
         
           
            
            //var_dump($arr_obj_planta);
            
            if(count($arr_obj_planta)>0){
                foreach($arr_obj_planta as $valor2){
                    if(($valor2->id_planta) == ($valor->id_planta)){
                        $data['checked']    = TRUE;
                        $data['checked2']   = " checked='checked' ";
                    }
                }
            }
            
            
            
            $html .= "<div style='50%'>";
            //$html .= form_checkbox($data);
            //$html .= form_checkbox($data['name'],$data['value'],$data['checked'],$data['checked2']);            
            $html .= '<input '.$habilitado.' class="plantas" type="checkbox" value="'.$data['value'].'" name="'.$data['name'].'" id="'.$data['id'].'" style="'.$data['style'].'" '.$data['checked2'].' '.$data['js'].'  >';
            $html .= "<div style='display:inline-block;'>".$valor->nombre."</div>";
            $html .= "</div>";
            $i++;
        }
         
        return $html;       
    }
    
    
    
    
    public function insertar($tipo=null){
        
        $exito      = array();
        $errores    = array();
        $tabla      = "";
        $resultado  ="";
        switch($tipo){
            case "usuario"  :  
                $obj_usuario_c    = new Usuario_class(); 
                $tabla = $obj_usuario_c->tabla;
                
                $exito += array(0 => "Usuario añadido exitosamente");
                
                $nombre         = $this->input->post('nombre');
                //$user           = $this->input->post('user');
                $email          = $this->input->post('email');
                $clave          = $this->input->post('clave');
                $ape_paterno    = $this->input->post('ape_paterno');
                $ape_materno    = $this->input->post('ape_materno');
                //$id_planta      = $this->input->post('id_planta');
                $id_sap         = $this->input->post('id_sap');
                
                $plantas        = $this->input->post('plantas');
                $perfil         = $this->input->post('tipos');
                              
                $data = array(
                                    "nombre"        => $nombre,
                                    //"user"          => $user,
                                    "email"         => $email,
                                    "clave"         => $clave,
                                    "ape_paterno"   => $ape_paterno,
                                    "ape_materno"   => $ape_materno,
                                    "plantas"       => $plantas,
                                    "id_sap"        => $id_sap,
                                    "perfil"        => $perfil
                                    );
                
                
                $obj_usuario    = new Usuario_model(); 
                $obj_usuario->set_registro_arr($data); 
                $obj_usuario->clave = $obj_usuario->encriptar_clave($clave);

                $data = $obj_usuario->get_registros_arr();
                $errores = $this->validar_ins_usuario($obj_usuario);
                $vista="nvo_usuario"  ;
                                
                break;
            case "planta"   : 
                $vista="nvo_planta"   ;
                $exito += array(0 => "Planta añadida exitosamente");
                
                $obj_planta_c   = new Planta_class(); 
                $tabla          = $obj_planta_c->tabla;
                
                $nombre         = $this->input->post('nombre');
                $id_interno     = $this->input->post('codigo');
                $canal          = $this->input->post('canal');
                $direccion      = $this->input->post('direccion');
                $direccion2     = $this->input->post('direccion2'); 
                
                
                $obj_planta    = new Planta_model(); 
                $data = array(
                                    "nombre"        => $nombre,
                                    "id_interno"    => $id_interno,
                                    "canal"         => $canal,
                                    "direccion"     => $direccion,
                                    "direccion2"    => $direccion2
                                    );
                                
                $obj_planta->set_registro_arr($data); 
                $data = $obj_planta->get_registros_arr();                
                $errores = $this->validar_ins_planta($obj_planta);
                
                
                
                break; 
            case "capacidad"   : 
                $vista="nvo_capacidad"   ;
                $exito += array(0 => "Capacidad añadida exitosamente");
                
                $obj_capacidad_c    = new Capacidad_class(); 
                $tabla = $obj_capacidad_c->tabla;
                
                $id_planta  = $this->input->post('id_planta');
                $fecha      = $this->input->post('fecha');
                $capacidad  = $this->input->post('capacidad');                
                                
                
                $obj_capacidad    = new Capacidad_model(); 
                $data = array(
                                    "id_planta"     => $id_planta,
                                    "fecha_inicio"  => $fecha,
                                    "capacidad"     => $capacidad
                                    );
                
                
                $obj_capacidad->set_registro_arr($data); 
                $obj_capacidad->formatear_fecha();
                
                //var_dump($obj_capacidad);
                
                $data = $obj_capacidad->get_registros_arr();                
                $errores = $this->validar_ins_capacidad($obj_capacidad);
                
                //return;
                break;
            default:
                $vista="usuario";
                break;
        } 
           
        if(count($errores) == 0){
            $obj_crud       = new Crud_model();   
            $obj_crud->tabla = $tabla;            
            $resultado = $obj_crud->add_registro($data);
            $this->arr_msg = $exito;
            
            if(($resultado == 1) && ($tipo == "usuario")){
                    $obj_perfil_c           = new Perfil_class();
                    $obj_usuario_planta_c   = new Usuario_planta_class(); 
                    
                    foreach($perfil as $valor){
                        $obj = new Perfil_model();
                        $obj->id_tipo = $valor;
                        $obj->id_usuario = $obj_crud->id_ultimo;
                        
                        $data = $obj->get_registros_arr();
                        
                        $obj_crud_2           = new Crud_model();   
                        $obj_crud_2->tabla    = $obj_perfil_c->tabla;            
                        $resultado = $obj_crud_2->add_registro($data);
                    }
                    
                    foreach($plantas as $valor){
                        $obj = new Usuario_planta_model();
                        $obj->id_planta = $valor;
                        $obj->id_usuario = $obj_crud->id_ultimo;
                        
                        $data = $obj->get_registros_arr();
                        
                        $obj_crud_2           = new Crud_model();   
                        $obj_crud_2->tabla    = $obj_usuario_planta_c->tabla;            
                        $resultado = $obj_crud_2->add_registro($data);
                    }
                    
                    
            }            
        }else{
            $this->arr_msg = $errores;
            //echo "id_error=".$errores[0];
        }
        $this->data["msg"] = $this->crear_msg($this->arr_msg);
        $this->crear($tipo);
    }
     
    
    
    
    public function eliminar(){
        
        //$exito      = array(0 => "Usuario eliminado exitosamente");
        $exito      = 1;
        $errores    = array();
        //$tabla      = "";
        $resultado  ="";
        
        $id         = $this->input->post('id');
        $tabla      = $this->input->post('entidad');
        
        switch($tabla){
            case "usuario"  :  
                $obj_usuario_c    = new Usuario_class(); 
                $tabla = $obj_usuario_c->tabla;
                
                $data = array("id_usuario"    => $id);                
                $obj    = new Usuario_model(); 
                $obj->set_registro_arr($data);                 

                $errores = $this->validar_eli_usuario($obj);
                //$vista="nvo_usuario"  ;
                break;
            case "planta"   : 
                $obj_planta_c    = new Planta_class(); 
                $tabla = $obj_planta_c->tabla;
                
                $data = array("id_planta"    => $id);                
                $obj    = new Planta_model(); 
                $obj->set_registro_arr($data);                 

                $errores = $this->validar_eli_planta($obj);
                break; 
            case "capacidad"   : 
                //$obj_usuario_c    = new Capacidad_class(); 
                //$tabla = $obj_usuario_c->tabla;
                
                $data = array("id_capacidad"    => $id);                
                $obj = new Capacidad_model(); 
                $obj->set_registro_arr($data);                 

                $errores = $this->validar_eli_capacidad($obj);
                break; 
            default:
                //$vista="usuario";
                break;
        } 
           
        if(count($errores) == 0){
            $obj_crud       = new Crud_model();   
            $obj_crud->tabla = $tabla;         
            //var_dump($data);
            //echo " <br /> ".$tabla;
            $condicion = array("condicion" => $data);
            
            //$data = array("condicion" => array("id_usuario"    => $id));
            //
            $resultado = $obj_crud->delete_registro($condicion);
            //$this->arr_msg = $exito;
            //echo "resultado=".$resultado;
        }else{
            $resultado = 0;
            //$this->arr_msg = $errores;
            //echo "id_error=".$errores[0];
        }
        
        echo $resultado;
        
        //var_dump($this->arr_msg);
        
        //$this->data["msg"] = $this->crear_msg($this->arr_msg);
        //$this->crear($tipo);
    }
   
    
    
   public function crear_msg($arreglo){
       $div = "<div>";
       if(count($arreglo) > 0){
           foreach($arreglo as $valor){
               $div .= "".$valor."<br />";
           }
       }
       $div .= "</div>";
       return $div;
   } 
    
    
   public function validar_ins_usuario($obj_usuario){
       $resultado = array();       
       
       /*
       // Revisa usuario
       $obj_usuario_c  = new Usuario_planta_class();
       $registros = $this->buscar_registro($obj_usuario_c, "user", $obj_usuario->user,"full");
                
       if(count($registros)>0){
            array_push($resultado,  $this->mensajes[0]);  // Usuario existente
       }   
       */
       // Revisa usuario de Sap
       $obj_usuario_c  = new Usuario_planta_class();
       $registros = $this->buscar_registro($obj_usuario_c, "id_sap", $obj_usuario->id_sap,"full");
                
       if(count($registros)>0){
            array_push($resultado,  $this->mensajes[1]);  // Usuario SAP existente
       }         
       
       return $resultado;                
   }
   
   public function validar_ins_capacidad($obj_capacidad){
       $resultado = array();       
       
       // Revisa id_planta
       $obj_planta_c  = new Planta_class();
       $registros = $this->buscar_registro($obj_planta_c, "id_planta", $obj_capacidad->id_planta);
                
       if(count($registros)<1){
            array_push($resultado,  $this->mensajes[20]);  // Id Planta inexistente
       }   
       
       return $resultado;                
   }
   
   public function validar_ins_planta($obj_planta){
       $resultado = array();       
       /*
       // Revisa id_planta
       $obj_capacidad_c  = new Planta_class();
       $registros = $this->buscar_registro($obj_capacidad_c, "id_planta", $obj_capacidad->id_planta);
                
       if(count($registros)<1){
            array_push($resultado,  $this->mensajes[20]);  // Id Planta inexistente
       }   
       */
       return $resultado;                
   }
   
   
   
    public function validar_eli_usuario($obj_usuario){
       $resultado = array();       
       
       // Revisa usuario
       
       if($obj_usuario->id_usuario == $this->id_usuario){
            array_push($resultado,  $this->mensajes[10]);  // No puede autoeliminarse
       }   
       
       return $resultado;                
   }
   
   
   public function validar_eli_planta($obj_planta){
       $resultado = array();       
       
       return $resultado;                
   }
   
   public function validar_eli_capacidad($obj_capacidad){
       $resultado = array();       
       
       return $resultado;                
   }
   
   
   public function buscar_registro($obj_c,$campo,$valor,$tipo = null){                     
       //var_dump($obj_c);
       //return;
       
       //$registros = $this->buscar_registro($obj_capacidad_c, "id_planta", $obj_capacidad->id_planta);
       
       if($tipo == null){
           $obj_c->get_registro_campo($campo, $valor);
       }else if($tipo == "full"){
           $obj_c->get_registro_campo_full($campo, $valor);
       }
       $arreglo = $obj_c->arreglo;      
       $obj_crud        = new Crud_model();   
       $obj_crud->tabla = $obj_c->tabla;           
       $registros = $obj_crud->get_registros($arreglo);
       
       return $registros;
   }
    
   
  
    
    
    public function usuario($id_usuario=null)
    {
        if (!$this->data['ver_mant_usu']){
            $pag_sigte = "index.php/dashboard/";                
            redirect(base_url().$pag_sigte); 
        }
        
        $this->data['entidad'] = "usuario"; 
        if($id_usuario == null){
            $this->arr_usuarios = $this->get_usuarios();
            $this->crear_tabla_usuarios($this->arr_usuarios);
            $this->data['tabla_html']    = $this->tabla_html;
            $this->load->view('index',  $this->data    );
        }else{            
            $this->arr_usuarios = $this->get_usuarios($id_usuario);
            $this->crear_tabla_usuario($this->arr_usuarios);
            $this->data['tabla_html']    = $this->tabla_html;
            $this->data['btn']  =    "";
            $tipo = 'usuario';
            $this->data['btn2'] = '<a class="btn btn-success btn-small" href="'.base_url().'index.php/administracion/index/'.$tipo.'" id="btn_volver">Volver</a>';
            //$this->data['btn2'] = '<a class="btn btn-success btn-small" href="javascript:history.back(1);" id="btn_volver">Volver</a>';
            $this->load->view('index',  $this->data    );
        }
     }
    
    public function get_usuarios($id_usuario = null){
         //$obj_usuario_c = new Usuario_class();
         $obj_usuario_c = new Usuario_planta_class();
         $tabla = $obj_usuario_c->tabla;
         
         
         
         //var_dump($this->arr_perfil);
         $modo = null;
         if($this->arr_perfil){
             foreach($this->arr_perfil as $valor){
                 if($valor['id_tipo'] == 1){
                     $modo = "admin";
                     //echo "bla";
                 }
                 if($valor['id_tipo'] == 2){
                     $modo = "admin_plan";
                     //echo "bla";
                 }
             }
         }
         if($modo==null){
            $obj_usuario_c->get_usuario($this->id_planta,$id_usuario);
         }else if($modo == "admin"){
            $obj_usuario_c = new Usuario_class();
            $tabla = $obj_usuario_c->tabla;
            $obj_usuario_c->excepcion = -1;
            $obj_usuario_c->get_usuario(null,$id_usuario);
            
            $arreglo = $obj_usuario_c->arreglo;         
                  
            $obj_crud = new Crud_model();   
            $obj_crud->tabla = $tabla;
            $registros = $obj_crud->get_registros($arreglo);
            
         }else if($modo == "admin_plan"){
             $obj_usuario_c = new Usuario_planta_class();
             $obj_usuario_c->excepcion = 1;
             $obj_usuario_c->get_registro_campo_full(null, null, $this->id_planta);        
         }
         
         
         $arreglo = $obj_usuario_c->arreglo;         
                  
         $obj_crud = new Crud_model();   
         $obj_crud->tabla = $tabla;
         $registros = $obj_crud->get_registros($arreglo);
         
         $arreglo_obj = array();
         $i = 0;        
        foreach ($registros as $valor) {
            $arreglo_obj[$i] = new Usuario_model();   
            
            
            // Ingreso de perfiles en objeto usuario
            
            $obj_perfil_c = new Perfil_class();
            $obj_perfil_c->get_registro_campo("id_usuario", $valor['id_usuario']);        
            $arreglo_perfil = $obj_perfil_c->arreglo;
            
            $obj_crud_perfil = new Crud_model();   
            $obj_crud_perfil->tabla = $obj_perfil_c->tabla;
            $registros_perfil = $obj_crud_perfil->get_registros($arreglo_perfil);
            
            $x = 0;
            foreach($registros_perfil as $valor2){
                $arr_obj[$x] = new Perfil_model();
                $arr_obj[$x]->set_registro_arr($valor2);
                $x++;
            }
            
            if(isset($arr_obj)){
                $valor['perfil'] = $arr_obj;//$registros_perfil;
            }
            
            // Ingreso de plantas en objeto usuario
            
            $obj_usuario_planta_c = new Usuario_planta_class();
            $obj_usuario_planta_c->get_registro_campo("id_usuario", $valor['id_usuario']);        
            $arreglo_usuario_planta = $obj_perfil_c->arreglo;
            
            $obj_crud_usuario_planta            = new Crud_model();   
            $obj_crud_usuario_planta->tabla     = $obj_usuario_planta_c->tabla;
            $registros_usuario_planta           = $obj_crud_usuario_planta->get_registros($arreglo_usuario_planta);
            
            $y = 0;
            foreach($registros_usuario_planta as $valor3){
                $arr_obj_u_p[$y] = new Usuario_planta_model();
                $arr_obj_u_p[$y]->set_registro_arr($valor3);
                $y++;
            }
            
            if(isset($arr_obj_u_p)){
                $valor['plantas'] = $arr_obj_u_p; //$registros_perfil;
            }
            
            
            
            $arreglo_obj[$i]->set_registro_arr($valor);            
            $i++;
        } 
        return $arreglo_obj; 
     }
     
     public function trans_arreglo_str($arreglo,$tabla){
         $str = "";
         switch ($tabla){
            case "planta": 
            foreach($arreglo as $valor){
                $str .= "".$valor->id_planta.":'".$valor->nombre."',"; 
            }
            break;
            case "perfil": 
            foreach($arreglo as $valor){
                $str .= "{value:".$valor->id_perfil.":'".$valor->id_tipo."',"; 
            }
            break;
         }
         return substr($str, 0, -1);
     }
     
      public function crear_tabla_usuario($arreglo){         
         $this->table->set_heading(array('Campo','Valor'));

         $this->arr_plantas = $this->get_plantas();
         //var_dump($this->arr_plantas);
         $str_arr = $this->trans_arreglo_str($this->arr_plantas,"planta");
         
         
         //$str_arr_per = $this->trans_arreglo_str($arreglo[0]->perfil,"perfil");
         
         //echo $str_arr_per;
         
         foreach ($arreglo as $value) {
            $valor_nombre           = $this->crear_vinculo_act("nombre", $value->id_usuario, $value->nombre);
            $valor_apaterno         = $this->crear_vinculo_act("ape_paterno", $value->id_usuario, $value->ape_paterno);
            $valor_amaterno         = $this->crear_vinculo_act("ape_materno", $value->id_usuario, $value->ape_materno);
            $valor_user             = $this->crear_vinculo_act("user", $value->id_usuario, $value->user);
            $valor_email            = $this->crear_vinculo_act("email", $value->id_usuario, $value->email);
            $value->desencriptar_clave();
            //$valor_clave            = $this->crear_vinculo_act("clave", $value->id_usuario, $value->clave);
            $valor_clave            = $this->crear_vinculo_act("clave", $value->id_usuario, $value->clave,null,null,null,null,1);
            $valor_id_sap           = $this->crear_vinculo_act("id_sap", $value->id_usuario, $value->id_sap);
            //$valor_id_planta        = $this->crear_vinculo_act("id_planta", $value->id_usuario, $value->nom_planta,"","select","{".$str_arr."}");
            
            //$this->arr_plantas = $this->get_plantas();            
            //var_dump($value->plantas);
            
            $valor_id_planta        = $this->crear_checkbox_plantas($value->plantas);
            
            
            //$valor_perfiles         = $this->crear_vinculo_act("valido", $value->id_usuario, $value->nom_planta,"","select","{".$str_arr."}");
            $this->arr_tipos_usu = $this->get_tipos_usu();            
            //var_dump($this->arr_tipos_usu);            
            $valor_perfil   = $this->crear_checkbox_tipos_usu($value->perfil);

            //$valor_perfil   .= '<a class="act_per btn btn-success btn-small" href="javascript:void(0);">Actualizar permisos</a>';
         }  
         
         //var_dump($arreglo[0]->perfil);
         
         
         $this->table->add_row(array("Usuario", $valor_id_sap));
         $this->table->add_row(array("Clave", $valor_clave));
         $this->table->add_row(array("Email", $valor_email));
         $this->table->add_row(array("Nombre", $valor_nombre));
         $this->table->add_row(array("A.Paterno", $valor_apaterno));
         
         $this->table->add_row(array("A.Materno", $valor_amaterno));  
         $this->table->add_row(array("Planta", $valor_id_planta));
         $this->table->add_row(array("Perfil", $valor_perfil));
         //$this->table->add_row(array("Usuario SAP", $valor_id_sap));
          
         $this->tabla_html = $this->table->generate();
     }
          
     
     public function crear_tabla_usuarios($arreglo){         
         $this->table->set_heading(array('Nro','Usuario', 'Nombre','A. Paterno','Email','Clave', 'Editar','Eliminar'));
         $i=1;
         $clase ="";
         $tabla = "usuario";
         
         //var_dump($arreglo);
         if($arreglo){
            foreach ($arreglo as $value) {
               $valor_nombre       = $this->crear_vinculo_act("nombre", $value->id_usuario, $value->nombre, $clase);
               $valor_apaterno     = $this->crear_vinculo_act("ape_paterno", $value->id_usuario, $value->ape_paterno, $clase);
               //$valor_user         = $this->crear_vinculo_act("user", $value->id_usuario, $value->user, $clase);
               $valor_email        = $this->crear_vinculo_act("email", $value->id_usuario, $value->email, $clase);
               $value->desencriptar_clave();
               $valor_clave        = $this->crear_vinculo_act("clave", $value->id_usuario, $value->clave, $clase,null,null,null,1);
               $valor_id_sap       = $this->crear_vinculo_act("id_sap", $value->id_usuario, $value->id_sap, $clase);


               $img_ver        = "<a href='#' style='text-align:center'><img src='".base_url()."/assets/img/ver.png'></a>";
               $img_editar     = "<a href='".base_url()."index.php/administracion/index/usuario/".$value->id_usuario."' style='text-align:center'><center><img src='".base_url()."/assets/img/edit.png'></center></a>";
               $img_eliminar   = "<a href='#' onclick='eliminar(\"".$tabla."\",".$value->id_usuario.");' style='text-align:center'><center><img src='".base_url()."/assets/img/delete.png'></center></a>";

               $this->table->add_row(array($i, $valor_id_sap,$valor_nombre,$valor_apaterno,$valor_email,$valor_clave,$img_editar,$img_eliminar));
               $i++;
            }   
         }  
         
         $this->tabla_html = $this->table->generate();
     }
     
     public function crear_vinculo_act($campo,$id,$valor,$clase="",$tipo=null, $fuente=null, $tabla = null, $display=null){
         $vin_fin = '</a>';
         $vin_pre = '<a href="#" '.$display.' class="editable editable-click '.$clase.'" data-name="'.$campo.'" data-pk="'.$id.'"';
         if($tipo != null){
             $vin_pre .= ' data-type="'.$tipo.'" ';
         }
         if($fuente != null){
             $vin_pre .= ' data-source="'.$fuente.'" ';
         }
         if($tabla != null){
             $vin_pre .= ' data-params="{entidad:'.$tabla.'}" ';
         }  
         if($display != null){
             $vin_pre .= ' data-display="false" ';
         }
         
         $vin_pre .=">";
         
         $cadena  = $vin_pre.$valor.$vin_fin;
         return $cadena;
     }
     
     
     public function editar(){
         $tabla = $this->input->post('entidad');
         $campo = $this->input->post('name');
         $valor = $this->input->post('value');
         $id    = $this->input->post('pk');
         $resultado = array();
              
         
         
         
         if(    (isset($tabla)  )&&(    isset($campo)  )&&(    isset($valor)  )&&(    isset($id)  )  ){
                         
             $obj_usuario   = new Usuario_model();
             $obj_usuario_c = new Usuario_planta_class();
             
             $tipo = "full";
             
             switch($campo){
                 case "clave"   :  $valor     = $obj_usuario->encriptar_clave($valor);   break;
                 case "user"   :
                     $registros = $this->buscar_registro($obj_usuario_c, $campo, $valor,$tipo);                
                     if(count($registros)>0){
                        array_push($resultado,  $this->mensajes[0]);  // Usuario existente
                         //$resultado = $this->mensajes[0];
                     } 
                     break;
                 case "id_sap"   :
                     $registros = $this->buscar_registro($obj_usuario_c, $campo, $valor,$tipo);                
                     if(count($registros)>0){
                        array_push($resultado,  $this->mensajes[1]);  // Usuario sap existente
                        // $resultado = $this->mensajes[1];
                     } 
                     break;
                 case "email"   :
                     //echo $campo;
                     if(!filter_var($valor,FILTER_VALIDATE_EMAIL)){
                         array_push($resultado,  $this->mensajes[2]);  // Email inválido 
                     } 
                     break;    
                 
             }
             
             if(count($resultado)==0){
                 $obj_crud = new Crud_model();
                 $obj_crud->tabla = $tabla;
                 $resultado[0] = $this->actualizar($obj_crud, $id, $campo, $valor);
                 
             }
             
         }
         echo $resultado[0];
     }  
     
     
      public function guardar_log($id_usuario,$objetivo_tipo,$objetivo_id,$id_interaccion,$id_planta){
         $obj_historial_c = new Historial_class();
         $tabla = $obj_historial_c->tabla;
         
         $arreglo = array(
                        "id_usuario"        => $id_usuario,
                        "objetivo_tipo"     => $objetivo_tipo,
                        "objetivo_id"       => $objetivo_id,
                        "id_interaccion"    => $id_interaccion,
                        "id_planta"         => $id_planta
                    );
         
         $obj_historial = new Historial_model();
         $obj_historial->set_registro_arr($arreglo);
         
         $data = $obj_historial->get_registros_arr();
         
         $obj_crud = new Crud_model();
         $obj_crud->tabla = $tabla;
         $respuesta = $obj_crud->add_registro($data);
         
         return $respuesta;
     }
     
     
     
     public function act_tipos_usu(){
         $perfil        = $this->input->post('tipos');
         $id_usuario    = $this->input->post('pk');
         
         if(    ($perfil!=null)  &&  ($id_usuario!=null)  ){
             $this->arr_usuarios = $this->get_usuarios($id_usuario);
             $arr_perfil = $this->arr_usuarios[0]->perfil;
             
             $obj_perfil_c = new Perfil_class();
             $tabla = $obj_perfil_c->tabla;
             
             //var_dump($arr_perfil);
             
             foreach($arr_perfil as $valor_reg){
                $obj_crud = new Crud_model();
                $obj_crud->tabla = $tabla;
                $campo = "valido";
                $valor = 0;
                $id_campo = "id_perfil";
                $this->actualizar($obj_crud, $valor_reg->id_perfil, $campo, $valor, $id_campo);                        
             }
             
             foreach($perfil as $valor_reg){
                $obj_crud = new Crud_model();
                $obj_crud->tabla = $tabla;           
                
                $arreglo = array(
                                "id_usuario" => $id_usuario,
                                "id_tipo"    => $valor_reg
                            );
                
                $obj = new Perfil_model();
                $obj->set_registro_arr($arreglo);
                
                //var_dump($obj);
                
                $respuesta = $this->insertar_registro($obj_crud, $obj);
             }
             
         }
         //var_dump($this->arr_usuarios[0]->perfil);
     }
     
     public function act_plantas(){
         $plantas       = $this->input->post('plantas');
         $id_usuario    = $this->input->post('pk');
         
         if(    ($plantas!=null)  &&  ($id_usuario!=null)  ){
             $this->arr_usuarios = $this->get_usuarios($id_usuario);
             $arr_plantas = $this->arr_usuarios[0]->plantas;
             
             $obj_usuario_planta_c = new Usuario_planta_class();
             $tabla = $obj_usuario_planta_c->tabla;
             
             foreach($arr_plantas as $valor_reg){
                $obj_crud = new Crud_model();
                $obj_crud->tabla = $tabla;
                $campo = "valido";
                $valor = 0;
                $id_campo = "id_usuario_planta";
                $this->actualizar($obj_crud, $valor_reg->id_usuario_planta, $campo, $valor, $id_campo);                        
             }
             
             //var_dump($plantas);
                 
             
             foreach($plantas as $valor_reg){
                $obj_crud = new Crud_model();
                $obj_crud->tabla = $tabla;           
                
                $arreglo = array(
                                "id_usuario"    => $id_usuario,
                                "id_planta"     => $valor_reg
                                );
                
                $obj = new Usuario_planta_model();
                $obj->set_registro_arr($arreglo);
                                
                $respuesta = $this->insertar_registro($obj_crud, $obj);
             }
             
         }
         //var_dump($this->arr_usuarios[0]->perfil);
     }
     
     
     
     public function insertar_registro($obj_crud,$obj){                     
       $arreglo = $obj->get_registros_arr();  
       return $obj_crud->add_registro($arreglo);        
    }
     
     private function actualizar($obj_crud,$id,$campo,$valor,$id_campo = null){
            $tabla = $obj_crud->tabla;
            
            if($id_campo == null){
                $id_campo = "id_".$tabla;
            }
            $condicion     = array(
                                $id_campo => $id
                                );             
            $actualizacion = array(
                                $campo => $valor
                                );
            $arreglo = array(
                            "condicion" => $condicion,
                            "actualizacion" => $actualizacion    
                            );             
             
            return $obj_crud->update_registro($arreglo);
     }
     
    
    public function planta($id_planta=null)
    {
         if($id_planta == null){      
            
             
             
            if (!$this->data['ver_mant_pla']){
                $pag_sigte = "index.php/dashboard/";                
                redirect(base_url().$pag_sigte); 
            }
            
            
            
         }else{
            if($this->arr_plantas_usu){
                
                //var_dump($this->arr_plantas_usu);
                
                $paso = false;
                
                foreach($this->arr_plantas_usu as $valor){
                    if($valor['id_planta']==$id_planta){
                        $paso = true;
                    }
                }
            } 
             
             
             
             if ((!$this->data['ver_mant_cap']) || ($paso == false)){
                $pag_sigte = "index.php/dashboard/";                
                redirect(base_url().$pag_sigte); 
            }
         }    
        
        if($id_planta == null){
            $arr_plantas = $this->get_plantas();
            $this->crear_tabla_plantas($arr_plantas);

            $this->data['tabla_html'] = $this->tabla_html;
            $this->data['entidad'] = "planta";
            //$this->data['btn']  =    "";
            
            $this->load->view('index',  $this->data    ); 
            
        }else{            
            //$this->arr_plantas = $this->get_plantas($id_planta);
            //$this->data['parametros'] = $id_planta;
            
            //$this->session->set_userdata('temp_id_planta',$id_planta);
            $this->data['id_planta'] = $id_planta;
            $this->arr_capacidades = $this->get_capacidades($id_planta);
            $this->crear_tabla_capacidad($this->arr_capacidades);
            $this->data['tabla_html']    = $this->tabla_html;
            $this->data['entidad'] = "capacidad";
            //$this->data['btn'] = '<a class="btn btn-success btn-small" href="javascript:history.back(1);" id="btn_volver">Volver</a>';
            
            //$this->data['btn']  =    "";
            $tipo = 'planta';
            $this->data['btn2'] = '<a class="btn btn-success btn-small" href="'.base_url().'index.php/administracion/index/'.$tipo.'" id="btn_volver">Volver</a>';
            
            
            
            $this->load->view('index',  $this->data    );
            //var_dump($this->arr_usuarios);
        }
        
     }
     
     public function get_cap_planta($id_planta){
         $desde = date("Y-m-d H:i");
         $obj_capacidad = new Capacidad_class();
         $obj_capacidad->get_capacidades_dh($desde, $id_planta);
         $arreglo = $obj_capacidad->arreglo;
         
         $obj_crud = new Crud_model();
         $obj_crud->tabla = $obj_capacidad->tabla;
         $registros = $obj_crud->get_registros($arreglo);
         if(count($registros)>0){
            return $registros[0]['capacidad'];
         }else{
            return 0; 
         }   
     }
     
     
     public function crear_tabla_plantas($arreglo){         
         $this->table->set_heading(array('Nro','Nombre','C&oacute;digo','Canal','Direcci&oacute;n', 'Direcci&oacute;n Ext.','Capacidad', 'Editar','Eliminar'));
         $i=1;
         $clase ="";
         $tabla = "planta";
         foreach ($arreglo as $value) {
            $valor_nombre           = $this->crear_vinculo_act("nombre", $value->id_planta, $value->nombre, $clase);
            $valor_codigo           = $this->crear_vinculo_act("id_interno", $value->id_planta, $value->id_interno, $clase);
            $valor_canal            = $this->crear_vinculo_act("canal", $value->id_planta, $value->canal, $clase);
            $valor_direccion        = $this->crear_vinculo_act("direccion", $value->id_planta, $value->direccion, $clase);
            $valor_direccion2       = $this->crear_vinculo_act("direccion2", $value->id_planta, $value->direccion2, $clase);
            
            
            
            //$valor_capacidad        = $this->crear_vinculo_act("nombre", $value->id_planta, $value->nombre, $clase);
            $valor_capacidad        ="<div>".$this->get_cap_planta($value->id_planta)."</div>";
            $img_editar     = "<a href='".base_url()."index.php/administracion/index/".$tabla."/".$value->id_planta."' style='text-align:center'><center><img src='".base_url()."/assets/img/edit.png'></center></a>";
            $img_eliminar   = "<a href='#' onclick='eliminar(\"".$tabla."\",".$value->id_planta.");' style='text-align:center'><center><img src='".base_url()."/assets/img/delete.png'></center></a>";
            
            $this->table->add_row(array($i, $valor_nombre,$valor_codigo,$valor_canal,$valor_direccion,$valor_direccion2,$valor_capacidad,$img_editar,$img_eliminar));
            $i++;
         }         
         
         $this->tabla_html = $this->table->generate();
     }
     
     
     public function crear_tabla_capacidad($arreglo){         
         $this->table->set_heading(array('Nro','Planta', 'Fecha de Inicio', 'Capacidad','Eliminar'));
         $i=1;
         $clase ="";
         $tabla = "capacidad";
         
         //var_dump($arreglo);
         
         foreach ($arreglo as $value) {
            $id = $value->id_capacidad; 
            $valor_fecha_inicio     = $this->crear_vinculo_act("fecha_inicio", $id, $value->fecha_inicio, $clase);
            //$valor_planta           = $this->crear_vinculo_act("id_planta", $id, $value->id_planta, $clase);
            $valor_planta           = "<div>".$value->nom_planta."</div>";
            $valor_capacidad        = $this->crear_vinculo_act("capacidad", $id, $value->capacidad, $clase);
            
            //$img_editar     = "<a href='".base_url()."index.php/administracion/index/".$tabla."/".$id."' style='text-align:center'><img src='".base_url()."/assets/img/edit.png'></a>";
            $img_eliminar   = "<a href='#' onclick='eliminar(\"".$tabla."\",".$id.");' style='text-align:center'><center><img src='".base_url()."/assets/img/delete.png'></center></a>";
            
            $this->table->add_row(array($i, $valor_planta,$valor_fecha_inicio,$valor_capacidad,$img_eliminar));
            $i++;
         }         
         
         $this->tabla_html = $this->table->generate();
     }
     
     
     public function calendario(){
       
        if (!$this->data['ver_mant_cal']){
            $pag_sigte = "index.php/dashboard/";                
            redirect(base_url().$pag_sigte); 
        }
        
        $arr_bloques = $this->get_tipos_blo();
        $this->data['tabla_tipos'] = $this->crear_tabla_tipos($arr_bloques);
        
        $this->data['calendario']   = Modules::run('calendario/calendario/index');   
        $this->load->view('calendario',  $this->data);            
     }
     
     
     public function get_tipos_blo(){ 
         $obj_tipo_bloque = new Tipo_bloque_class();
         
         $obj_tipo_bloque->get_tipos_blo();
         $arreglo = $obj_tipo_bloque->arreglo;
         
         $obj_crud = new Crud_model();
         $obj_crud->tabla = $obj_tipo_bloque->tabla;         
         return $registros = $obj_crud->get_registros($arreglo);        
     }    
     
     
     public function crear_tabla_tipos($arreglo){         
         $this->table->set_heading(array('Nro','Nombre', 'Color de fondo', 'Color de texto'));
         $i=1;
         $clase ="";
         foreach ($arreglo as $value) {
            //$valor_nombre           = $this->crear_vinculo_act("nombre", $value['id_tipo'], $value['nombre'], $clase);
            $valor_nombre           = $value['nombre'];
            $valor_color_fondo      = $this->crear_vinculo_col("color_fondo", $value['id_tipo'], $value['color_fondo'], $clase);
            $valor_color_texto      = $this->crear_vinculo_col("color_texto", $value['id_tipo'], $value['color_texto'], $clase);
            
            $this->table->add_row(array($i, $valor_nombre,$valor_color_fondo,$valor_color_texto));
            $i++;
         }         
         
         return $this->table->generate();
     }
     
     public function crear_vinculo_col($campo,$id,$valor,$clase=null,$tipo=null, $fuente=null, $tabla = null){
         $vin_fin = '</div>';
         $vin_pre = '<div class="input-append color" data-color-format="rgb" data-color="'.$valor.'" data-name="'.$campo.'" data-pk="'.$id.'"';
         if($tipo != null){
             $vin_pre .= ' data-type="'.$tipo.'" ';
         }
         if($fuente != null){
             $vin_pre .= ' data-source="'.$fuente.'" ';
         }
         if($tabla != null){
             $vin_pre .= ' data-entidad:"'.$tabla.'" ';
         }         
         
         $vin_pre .=">";
         
         $vin_pre .='<input onchange="get_colores(\''.$campo.'\');" id="color_'.$id.'_'.$campo.'" type="text" class="span2" value="'.$valor.'" /><span class="add-on" id="muestra_'.$id.'_'.$campo.'"> <i></i> </span>';
         
         $cadena  = $vin_pre.$vin_fin;
         return $cadena;
     }
     
     
     public function actualizar_bloques(){
         
            $cf[1]   = $this->input->post('cf_1');
            $cf[2]   = $this->input->post('cf_2');
            $cf[3]   = $this->input->post('cf_3');
            $cf[4]   = $this->input->post('cf_4');
            $cf[5]   = $this->input->post('cf_5');
            $cf[6]   = $this->input->post('cf_6');
            $cf[7]   = $this->input->post('cf_7');
            
            $ct[1]   = $this->input->post('ct_1');
            $ct[2]   = $this->input->post('ct_2');
            $ct[3]   = $this->input->post('ct_3');
            $ct[4]   = $this->input->post('ct_4');
            $ct[5]   = $this->input->post('ct_5');
            $ct[6]   = $this->input->post('ct_6');
            $ct[7]   = $this->input->post('ct_7');
            
            $obj_tipo_bloque = new Tipo_bloque_class();
            $tabla = $obj_tipo_bloque->tabla;
            
            $obj_crud = new Crud_model();
            $obj_crud->tabla = $tabla;
            
            $id_campo = "id_tipo";
            $campo = "color_fondo";
            
            for($i=1;$i<8;$i++){
                $condicion     = array(
                                    $id_campo => $i
                                    );             
                $actualizacion = array(
                                    $campo => $cf[$i]
                                    );
                $arreglo = array(
                                "condicion" => $condicion,
                                "actualizacion" => $actualizacion    
                                );        
                $res = $obj_crud->update_registro($arreglo);
            } 
            $campo = "color_texto";
            for($i=1;$i<8;$i++){
                $condicion     = array(
                                    $id_campo => $i
                                    );             
                $actualizacion = array(
                                    $campo => $ct[$i]
                                    );
                $arreglo = array(
                                "condicion" => $condicion,
                                "actualizacion" => $actualizacion    
                                );        
                $res = $obj_crud->update_registro($arreglo);
            } 
          echo $res;  
            
     }
     
     
}
/*
*end modules/login/controllers/index.php
*/  