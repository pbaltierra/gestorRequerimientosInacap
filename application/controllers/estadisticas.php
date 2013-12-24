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
class Estadisticas extends MX_Controller{
        
        Private $dataHeader;
        Private $dataBody;
        Private $contenidoPagina;
        
        
	public function __construct()
	{
		parent::__construct();
		
                $this->load->model('usuario_model');
                $this->load->model('tipo_usuario_model');
                $this->load->model('historial_model');
                $this->load->model('crud_model');
                
                $this->load->database();
                $this->dataHeader['title']  = 'Estad&iacute;sticas';
                $this->dataHeader['menu']   = '';
                $this->dataBody['msgError'] = '';
                $this->dataBody['cambiar_pass']   = Modules::run('recordar_clave/cambiar/index');    
                
                $this->contenidoPagina = 'estadisticas/index'; 
                
                $this->load->helper('url');
                $this->load->helper('form'); 
                
                
                $this->load->library('form_validation');
                $this->load->library('session');
                
                
                $this->id_usuario   = $this->session->userdata('id_usuario'); 
                $this->guardar_sesion();
                
                
                $this->validar_sesion();
	}
        
        public function validar_sesion(){
            if( $this->session->userdata('logged_in')== FALSE  ){                                  
                $pag_sigte = "index.php/login/";                
                redirect(base_url().$pag_sigte); 
            }
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
        
        
        
        
        public function creaPagina($tipo=0){
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
                        
                        break;                
            }           
        } 
        
        public function index($pagina=null)
        {    
                $this->dataHeader['menu']       = Modules::run('menu/index/index');
                
                if($pagina == null){
                    $this->dataBody['modulo']      = Modules::run('reporte/reporte/index');
                }else if($pagina == "reportes"){
                    $this->dataBody['modulo']      = Modules::run('reporte/reporte/index');
                }else if($pagina == "graficos"){
                    $this->dataBody['modulo']      = Modules::run('grafico/grafico/index');
                }
                
               
                $this->creaPagina();       
                
        }
        
        public function reportes(){
            $this->dataHeader['menu']       = Modules::run('menu/index/index');
            $this->dataBody['modulo']      = Modules::run('reporte/reporte/index');
            $this->creaPagina(); 
        }
        
        public function graficos(){
            $this->dataHeader['menu']       = Modules::run('menu/index/index');
            $this->dataBody['modulo']      = Modules::run('grafico/grafico/index');
            $this->creaPagina(); 
        }
        
        
        /*
        public function crear_tabla(){
            
            $this->table->set_heading(array('Campo (Debe volver a iniciar sesión para ver los cambios)','Valor'));
            
            
            $obj_preferencia = new Preferencia_class();
            
            $id = "id_preferencia";
            $obj_preferencia->get_registro_campo($id, "1");
            $arreglo = $obj_preferencia->arreglo;
         
            $obj_crud = new Crud_model();
            $obj_crud->tabla = $obj_preferencia->tabla;
            $registros = $obj_crud->get_registros($arreglo);
            
            $valor_desfase  = $registros[0]['valor'];
            $des_desfase    = $registros[0]['descripcion'];
            
            //--------------------------
            
            $obj_preferencia->get_registro_campo($id, "2");
            $arreglo = $obj_preferencia->arreglo;
            
            $registros = $obj_crud->get_registros($arreglo);
            
            $valor_hora_ini = $registros[0]['valor'];
            $des_hora_ini   = $registros[0]['descripcion'];
            
            //--------------------------
            
            $obj_preferencia->get_registro_campo($id, "3");
            $arreglo = $obj_preferencia->arreglo;
            
            $registros = $obj_crud->get_registros($arreglo);
            
            $valor_horas = $registros[0]['valor'];
            $des_horas   = $registros[0]['descripcion'];
            
            
            //--------------------------
            
            $valor_desfase          = $this->crear_vinculo_act("valor", "1", $valor_desfase);
            $valor_hora_ini         = $this->crear_vinculo_act("valor", "2", $valor_hora_ini);
            $valor_horas            = $this->crear_vinculo_act("valor", "3", $valor_horas);
            
            $this->table->add_row(array($des_desfase, $valor_desfase));
            $this->table->add_row(array($des_hora_ini, $valor_hora_ini));
            $this->table->add_row(array($des_horas, $valor_horas));

            return $this->table->generate();
        }
        
       public function crear_vinculo_act($campo,$id,$valor,$clase=null,$tipo=null, $fuente=null, $tabla = null){
         $vin_fin = '</a>';
         $vin_pre = '<a href="#" class="editable editable-click '.$clase.'" data-name="'.$campo.'" data-pk="'.$id.'"';
         if($tipo != null){
             $vin_pre .= ' data-type="'.$tipo.'" ';
         }
         if($fuente != null){
             $vin_pre .= ' data-source="'.$fuente.'" ';
         }
         if($tabla != null){
             $vin_pre .= ' data-params="{entidad:'.$tabla.'}" ';
         }         
         
         $vin_pre .=">";
         
         $cadena  = $vin_pre.$valor.$vin_fin;
         return $cadena;
     }
         * 
         */
}

?>
