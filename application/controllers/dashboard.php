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
class Dashboard extends MX_Controller{
        
        Private $dataHeader;
        Private $dataBody;
        Private $contenidoPagina;
        Private $id_usuario;
        
	public function __construct()
	{
		parent::__construct();
		
                $this->load->model('usuario_model');
                $this->load->model('tipo_usuario_model');
                $this->load->model('historial_model');
                
                $this->load->database();
                $this->dataHeader['title']  = 'Dashboard';
                $this->dataHeader['menu']   = '';
                $this->dataBody['msgError'] = '';
                $this->dataBody['cambiar_pass']   = Modules::run('recordar_clave/cambiar/index'); 
                $this->contenidoPagina = 'dashboard/index'; 
                
                $this->load->helper('url');
                $this->load->helper('form');    
                
                $this->load->library('form_validation');
                $this->load->library('session');
               
                $this->session->set_userdata('modo','user');
                
                
                $this->id_usuario   = $this->session->userdata('id_usuario'); 
                $this->guardar_sesion();
	}
        
        
        private function guardar_sesion($usuario = null){          
            
            if($this->id_usuario==null){
                $this->session->set_userdata('logged_in',false);
                $pag_sigte = "index.php/login/";  
                redirect(base_url().$pag_sigte); 
            }else{
                $usuario = $this->id_usuario;
            }
            
            // Recoge los datos de usuario
              $obj_usu    = new Usuario_planta_class();
              $tabla      = $obj_usu->tabla;
              
              $campo      = "usuario.id_usuario";
              $valor      = $usuario;
                                 
              $obj_usu->get_registro_campo_full($campo, $valor);
              $arreglo    = $obj_usu->arreglo; 
                    
              $obj_crud = new Crud_model();
              $obj_crud->tabla = $tabla;
              $registros = $obj_crud->get_registros($arreglo);
            
              if(isset($registros[0])){
                    $data['usuario'] = $registros[0];
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
                                            //'id_planta'     => $data['usuario']['id_planta'],
                                            //'nom_planta'    => $data['usuario']['nom_planta'],
                                    
                                            'plantas'       => $data['plantas'],
                                            'tipos'         => $data['tipos'],
                                            'permisos'      => $data['permisos'],
                                            
                                    
                                            'desfase'       => $data['desfase'],
                                            'horas_ini'     => $data['horas_ini'],
                                            'horas_tot'     => $data['horas_tot'],
                                            'logged_in'     => TRUE
                                        );                                
                                
                $this->session->set_userdata($newdata); 
              }
        }
        
        
        
        
        public function validar_sesion(){
            if( $this->session->userdata('logged_in')== FALSE  ){                                  
                $pag_sigte = "index.php/login/";                
                redirect(base_url().$pag_sigte); 
            }
        }
        
        
        
        public function ver_calendario(){
            $this->session->set_userdata('modo','user');
            $this->load->view('dashboard/calendario');             
        }
        
        
        public function creaPagina($tipo=0){
            $this->session->set_userdata('modo','user');
            switch($tipo){
                case 0:
                        $this->load->view('templates/header', $this->dataHeader);
                        $this->load->view('templates/menu', $this->dataHeader);
                        $this->load->view($this->contenidoPagina, $this->dataBody);                
                        $this->load->view('templates/footer');
                        break;
                case 1:
                        /*
                        $this->load->view('templates/header', $this->dataHeader);                        
                        $this->load->view($this->contenidoPagina, $this->dataBody);                
                        $this->load->view('templates/footer');
                         * */                         
                        $this->load->view('dashboard/calendario');
       
                        break;                
            }           
        } 
        
        public function index()
        {    
                //echo "bla=".$this->id_usuario;
                
                if($this->id_usuario==null){
                    $pag_sigte = "index.php/login/";                
                    redirect(base_url().$pag_sigte); 
                }
                
            
                $this->dataHeader['menu']       = Modules::run('menu/index/index');
                $this->dataBody['calendario']   = Modules::run('calendario/calendario/index');
                $this->dataBody['tabla_ov']     = Modules::run('tabla_ov/tabla_ov/index');
                $this->dataBody['historial']    = Modules::run('historial/historial/index');
                
                $this->contenidoPagina          = "dashboard/index";
                $this->dataBody['fecha_hoy']    = date("d/m/Y");                
                $this->creaPagina(); 
                      
        }
        
        
        
        /*
	public function view($slug)
	{
		$data['news_item'] = $this->news_model->get_news($slug);

                if (empty($data['news_item']))
                {
                        show_404();
                }

                $data['title'] = $data['news_item']['title'];

                $this->load->view('templates/header', $data);
                $this->load->view('news/view', $data);
                $this->load->view('templates/footer');
	}
       
        public function buscar()
        {
                $this->load->helper('url');
                $this->load->helper('form');
                $this->load->library('form_validation');
                
                $this->form_validation->set_rules('buscar', 'Direcci&oacute;n', 'required');
                
                $data['tecnicos'] = $this->tecnico_model->get_tecnico();                
                
                $cadenaTiempos = "";
                $cadenaIdInterno = "";
                $cadenaNombre = "";
                $cadenaEstado = "";
                $cadenaTipo = "";
                
                for($i=0; $i<count($data['tecnicos']); $i++){
                    $cadenaTiempos  .= $data['tecnicos'][$i]['tpo_termino'];
                    $cadenaIdInterno.= "'".$data['tecnicos'][$i]['id_interno']."'";
                    $cadenaNombre   .= "'".$data['tecnicos'][$i]['nombre']." ".$data['tecnicos'][$i]['ape_paterno']." ".$data['tecnicos'][$i]['ape_materno']."'";
                    $cadenaEstado   .= "'".$data['tecnicos'][$i]['estado']."'";
                    $cadenaTipo     .= "'".$data['tecnicos'][$i]['tipo']."'";
                    
                    if($i != (count($data['tecnicos'])-1) ){
                        $cadenaTiempos  .= ",";
                        $cadenaIdInterno .= ",";
                        $cadenaNombre   .= ",";
                        $cadenaEstado   .= ",";
                        $cadenaTipo   .= ",";
                    }
                }
                
                $data['cadenaTiempos']  = $cadenaTiempos; 
                $data['cadenaIdInterno']= $cadenaIdInterno; 
                $data['cadenaNombre']   = $cadenaNombre; 
                $data['cadenaEstado']   = $cadenaEstado; 
                $data['cadenaTipo']   = $cadenaTipo; 
                
                $data['title'] = 'Tecs';                
                $data['direccion'] = $this->input->post('buscar');
                
                
                if ($this->form_validation->run() === FALSE)
                {
                    
                    $this->load->view('templates/header', $data);
                    $this->load->view('templates/menu', $data);
                    $this->load->view('dashboard/index', $data);
                    $this->load->view('templates/footer');

                }else{
                    $this->load->view('templates/header', $data);
                    $this->load->view('templates/menu', $data);
                    $this->load->view('dashboard/buscar', $data);
                    $this->load->view('templates/footer');
                }
        }
         */
        
}

?>
