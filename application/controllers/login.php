<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of news
 *
 * @author Tharsis
 */
class Login extends MX_Controller{
    
        Private $dataHeader;
        Private $dataBody;
        Private $contenidoPagina;
        
        //Private $msgError;
        

	public function __construct()
	{
		parent::__construct();
		$this->load->model('usuario_model');
                $this->load->model('crud_model');
                $this->load->model('tipo_usuario_model');
                $this->load->database();
                $this->load->helper('url');
                $this->dataHeader['title']  = 'Acceso';
                $this->dataHeader['menu']   = '';
                $this->dataBody['msgError'] = '';
                //$this->dataBody['mod_recordar'] = "";
                
                $this->load->helper('security');
                $this->load->library('encrypt');
                $this->key = do_hash("getkem2013", 'md5'); // MD5;
                
                $this->dataBody['mod_recordar'] = Modules::run('recordar_clave/recordar/index');
                
                
                $this->contenidoPagina = 'pages/login'; 
                
                //$this->load->helper('security');
                $this->load->helper('url');
                $this->load->helper('form');                
                $this->load->library('form_validation');
                $this->load->library('session');
                
                
                
                
                //include APPPATH . 'libraries/usuario_planta_class.php';
                
                //$this->load->library('algo/usuario_planta');
                
                
	}
        
        public function index($id_planta=null)
        {    
            if( $this->session->userdata('logged_in')== FALSE  ){                  
                $this->contenidoPagina = "pages/login";
                $tipo = 1;
            }else{
                if($id_planta != null){
                    
                    //echo "laplanta:".$id_planta;
                    
                    $nom_planta = $this->validar_cambio_planta($id_planta);
                    if($nom_planta){
                        
                        $newdata = array(   
                                            'id_planta'     => $id_planta,
                                            'nom_planta'    => $nom_planta                                      
                                        );
                        $this->session->set_userdata($newdata);
                    }
                }
                //return;
                $pag_sigte = "index.php/dashboard/";                
                redirect(base_url().$pag_sigte); 
            }
            //phpinfo();
            $this->creaPagina($tipo);          
        }
        
        
        public function validar_cambio_planta($id_planta){
            $res = false;
            $arr_plantas = $this->session->userdata('plantas');
            //var_dump($arr_plantas);
            foreach($arr_plantas as $valor){
                if($id_planta == $valor['id_planta']){
                    return $valor['nom_planta'];
                }
            }
            return $res;
        }
        
        
                
        
        public function creaPagina($tipo=0){
            switch($tipo){
                case 0:
                        $this->load->view('templates/header', $this->dataHeader);
                        $this->load->view('templates/menu', $this->dataHeader);
                        $this->load->view($this->contenidoPagina, $this->dataBody);                
                        $this->load->view('templates/footer');
                        break;
                case 1:
                        $this->load->view('templates/header', $this->dataHeader);                        
                        $this->load->view($this->contenidoPagina, $this->dataBody);                
                        $this->load->view('templates/footer');
                        break;                
            }           
        } 
        
        
  
        
        public function validar()
        {
                $tipo = 1;
            
                $this->form_validation->set_rules('nickname', 'Usuario', 'required');
                $this->form_validation->set_rules('clave', 'Clave', 'required|min_length[3]|max_length[20]|strip_tags');
                
                                               
                if (($this->form_validation->run() != FALSE) || ($user!=null))
                {              
                    $usu_s_planta = 0;
                    
                    // Recoge los datos de usuario
                    $obj_usu    = new Usuario_planta_class();
                    $tabla      = $obj_usu->tabla;
                    
                    
                    $campo      = "usuario.id_sap";
                    $valor      = $this->input->post('nickname');
                    
                    
                    $obj_usu->get_registro_campo_full($campo, $valor);
                    $arreglo    = $obj_usu->arreglo; 
                    
                    $obj_crud = new Crud_model();
                    $obj_crud->tabla = $tabla;
                    $registros = $obj_crud->get_registros($arreglo);
                    
                    if(count($registros)<1){  // Usuarios que no estan asignados a una planta 
                        $usu_s_planta = 1;
                        $obj_usu    = new Usuario_class();
                        $tabla      = $obj_usu->tabla;

                        $obj_usu->get_registro_campo($campo, $valor);
                        $arreglo    = $obj_usu->arreglo; 

                        $obj_crud = new Crud_model();
                        $obj_crud->tabla = $tabla;
                        $registros = $obj_crud->get_registros($arreglo);
                    }
                    
                    
                    if(count($registros)>0){                        
                      
                           $pass_almacenada = $this->encrypt->decode($registros[0]['clave']);
                           
                           $pass_env = do_hash($this->input->post('clave'), 'md5'); // MD5 
                          
                           
                           if(   $pass_env ==  $registros[0]['clave']     ){
                               
                               
                               $data['usuario'] = $registros[0];
                               
                               
                               if($usu_s_planta!=1){
                               
                               // Recoge los datos de plantas asociadas al usuario
                                $campo      = "usuario_planta.id_usuario";
                                $valor      = $data['usuario']['id_usuario'];                    
                                $obj_usu->grupo     = "id_usuario_planta";
                                $obj_usu->campos    = "planta.id_planta, planta.nombre nom_planta";

                                $obj_usu->get_registro_campo_full($campo, $valor);
                                $arreglo    = $obj_usu->arreglo; 

                                $obj_crud = new Crud_model();
                                $obj_crud->tabla = $tabla;
                                $registros = $obj_crud->get_registros($arreglo);
                                $data['plantas'] = $registros;
                               }
                               
                                // Recoge los tipos de usuario asociados al usuario
                                $obj_per    = new Perfil_class();
                                $tabla      = $obj_per->tabla;

                                $campo      = "usuario.id_usuario";
                                $valor      = $data['usuario']['id_usuario'];                    
                                $obj_per->grupo     = "perfil.id_perfil";
                                $obj_per->campos    = "perfil.id_perfil, perfil.id_tipo,tipo_usuario.nombre";

                                $obj_per->get_registro_campo_full($campo, $valor);
                                $arreglo    = $obj_per->arreglo; 

                                $obj_crud = new Crud_model();
                                $obj_crud->tabla = $tabla;
                                $registros = $obj_crud->get_registros($arreglo);
                                $data['tipos'] = $registros;


                                // Recoge los permisos de cada tipo de usuario asociado al usuario
                                $obj_per    = new Perfil_class();
                                $tabla      = $obj_per->tabla;

                                $campo      = "usuario.id_usuario";
                                $valor      = $data['usuario']['id_usuario'];                    
                                $obj_per->grupo     = "permiso.id_permiso";
                                $obj_per->campos    = "permiso.id_permiso, tipo_usuario.nombre, permiso.id_seccion, seccion.nombre,permiso.c,permiso.r,permiso.u,permiso.d";

                                $obj_per->get_registro_campo_full($campo, $valor);
                                $arreglo    = $obj_per->arreglo; 

                                $obj_crud = new Crud_model();
                                $obj_crud->tabla = $tabla;
                                $registros = $obj_crud->get_registros($arreglo);
                                $data['permisos'] = $registros;



                                // Recoge preferencias
                                $obj_pre    = new Preferencia_class();
                                $tabla      = $obj_pre->tabla;

                                $obj_pre->get_registro_campo("id_preferencia", 1);
                                $arreglo    = $obj_pre->arreglo; 

                                $obj_crud = new Crud_model();
                                $obj_crud->tabla = $tabla;
                                $registros = $obj_crud->get_registros($arreglo);
                                $data['desfase'] = $registros[0]['valor'];

                                $obj_pre->get_registro_campo("id_preferencia", 2);
                                $arreglo    = $obj_pre->arreglo; 
                                $registros = $obj_crud->get_registros($arreglo);
                                $data['horas_ini'] = $registros[0]['valor'];


                                $obj_pre->get_registro_campo("id_preferencia", 3);
                                $arreglo    = $obj_pre->arreglo; 
                                $registros = $obj_crud->get_registros($arreglo);
                                $data['horas_tot'] = $registros[0]['valor'];
                               
                               
                               //$data['tipos_usuarios'] = $this->tipo_usuario_model->get_registro("id_tipo",$data['usuario']['id_tipo']);
                                $newdata = array(
                                            'id_usuario'    => $data['usuario']['id_usuario'],
                                            'nombre'        => $data['usuario']['nombre']." ".$data['usuario']['ape_paterno'],
                                            'id_planta'     => $data['usuario']['id_planta'],
                                            'nom_planta'    => $data['usuario']['nom_planta'],
                                            'cla_enc'       => $this->encrypt->encode($this->input->post('clave'), $this->key),
                                    
                                            'plantas'       => $data['plantas'],
                                            'tipos'         => $data['tipos'],
                                            'permisos'      => $data['permisos'],                                            
                                            'sincro'        => "", 
                                    
                                            'desfase'       => $data['desfase'],
                                            'horas_ini'     => $data['horas_ini'],
                                            'horas_tot'     => $data['horas_tot'],
                                            'logged_in'     => TRUE
                                        );
                                
                                        //var_dump($newdata);
                                        //return;
                                
                                $this->session->set_userdata($newdata);
                                
                                $pag_sigte = "index.php/dashboard/";  
                                
                                
                                
                                $sinc = Modules::run('tabla_ov/tabla_ov/sincronizar_sap','1');
                                //echo $this->session->userdata('sincro');   ;
                                
                                //return;
                                redirect(base_url().$pag_sigte);  
                             
                           }else{
                             $this->generarError(1);    // Clave y usuario no coinciden
                           }                     
                    }else{
                        $this->generarError(2);         // Usuario inexistente
                    }
                }else{
                    $this->generarError();              // Campos en blanco
                }
                
                $this->creaPagina($tipo);                
        }
        
        
        
        
        public function generarError($tipo=0){
            switch($tipo){
                case 0:
                    $msgError = "Los campos usuario y contrase&ntilde;a son requeridos";
                    break;
                case 1:
                    $msgError = "Los datos no coinciden";
                    break;
                case 2:
                    $msgError = "El usuario no existe";
                    break;
                default:
                    $msgError = "";
                    break;
            }    
            
            $this->dataBody['msgError'] ='<div class="alert">
                            <button type="button" class="close" data-dismiss="alert">&times;</button>
                            <strong>¡Atenci&oacute;n! </strong>'.$msgError.' 
                        </div>
                        ';
            
        }
        
        
        public function logout(){  
            $this->session->unset_userdata('id_usuario');
            $this->session->unset_userdata('nombre');
            $this->session->unset_userdata('id_planta');
            $this->session->unset_userdata('nom_planta');
            $this->session->unset_userdata('cla_enc');
            $this->session->unset_userdata('sincro');
            
            $this->session->unset_userdata('plantas');
            $this->session->unset_userdata('tipos');
            $this->session->unset_userdata('permisos');
            
            $this->session->unset_userdata('desfase');
            $this->session->unset_userdata('horas_ini');
            $this->session->unset_userdata('horas_tot');
                        
            $this->session->unset_userdata('logged_in');
            redirect(base_url());       
            
        }
        
}

?>
