<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
 
class Recordar extends MX_Controller
{
    
    private $email_emisor;
    private $nom_emisor;
    private $email_receptor;
    private $mensaje;
    private $titulo;
    private $key;
    //private $obj_usu;
    private $msj_final;
    private $msj_nousu;
    
    public function __construct()
    {
        parent::__construct();
        $this->load->helper('url');
        $this->load->helper('security');
        $this->load->library('email');
        
        $this->load->model('crud_model');
        $this->load->model('usuario_model');
        
        $this->load->library('encrypt');
        $this->key = do_hash("getkem2013", 'md5'); // MD5;
        
        $this->email_emisor = "pruebas.getkem@gmail.com";
        $this->nom_emisor   = "Planificación Inteligente";
        $this->titulo       = "Nueva clave de Planificación Inteligente";
        $this->mensaje      = "<a href='#'>link</a>";
        $this->email_receptor = "noreply@bbosch.cl";
        
        $this->msj_final    = "Hemos enviado un email con los datos solicitados";
        $this->msj_nousu    = "El email ingresado no es v&aacute;lido";
        $this->msj_req      = "El campo es requerido";
    }
    
    public function index()
    {
        $this->load->view('index');
     }
     
     public function gen_nva_cla(){
         $email     = $this->input->post('email');
         $email = "pruebas.getkem@gmail.com";       
         $cod = 0;
         $res = "";
         
         if($email!=null){
            $arr_obj_usu   = $this->recoger_usuario($email);
            
            if(count($arr_obj_usu)>0){
                $this->email_receptor = $email;
                $nva_clave = $this->generar_clave();
                
                
                if($this->act_clave($arr_obj_usu[0],$nva_clave)){//}
                //$link = $this->generar_link($arr_obj_usu[0]);
                //echo $link;
                //return;
                
                    $this->mensaje = $this->generar_mensaje($nva_clave);
                    $this->set_correo();   
                    $this->enviar_correo(); 
                    $res = $this->msj_final;
                    $cod = 1;
                }                   
            }else{
                $res = $this->msj_nousu;
            }            
         }else{
            $res = $this->msj_req; 
         }
         
         $arr = array("cod" =>$cod, "res"=>$res);
         echo json_encode($arr);
     }
     
     private function act_clave($obj_usu, $nva_cla){
        $cla_md5    = do_hash($nva_cla, 'md5'); // MD5 
        $obj_crud   = new Crud_model();
        $obj_usu_c  = new Usuario_class();
        $obj_crud->tabla = $obj_usu_c->tabla;
        return $this->actualizar($obj_crud, $obj_usu->id_usuario, "clave", $cla_md5, "id_usuario") ;
     }
     
    
    private function generar_clave($length=7,$uc=TRUE,$n=TRUE,$sc=true){
        $source = 'abcdefghijklmnopqrstuvwxyz';
        if($uc==1) $source .= 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        if($n==1) $source .= '1234567890';
        if($sc==1) $source .= '|@#~$%()=^*+[]{}-_';
        if($length>0){
            $rstr = "";
            $source = str_split($source,1);
            for($i=1; $i<=$length; $i++){
                //mt_srand((double)microtime() * 1000000);
                $num = mt_rand(1,count($source));
                $rstr .= $source[$num-1];
            }

        }
        return $rstr;
    } 
    
    private function generar_link($obj_usu){
        $fech_lim   = mktime(date("H"), date("i"), date("s"), date("m"), date("d")+1, date("Y"));
        $id_usuario = $this->encrypt->encode($obj_usu->id_usuario,  $this->key);
        $fech_lim   = $this->encrypt->encode($fech_lim,  $this->key);
        
        $link = base_url()."index.php/login/validar/".$id_usuario."/".$fech_lim;
        return $link;
    }
    
     
     
    private function generar_mensaje($nva_clave){
        //echo $nva_cla." ".$cla_md5;
        //return;        
        $html = "";
        $html .= "<br /><br />";
              
        
        $html .= "Clave nueva:<br />";
        $html .= "<b>".$nva_clave."</b>";        
        $html .= "<br /><br />";
        $html .= "<small>Nota: Por favor comun&iacute;quese con el administrador si usted no ha solicitado un cambio de clave.</small>";        
        $html .= "<br /><br />";
        $html .= "Planificaci&oacute;n Inteligente";
        $html .= "<br />";
        $html .= "BBOSCH";
        return $html;
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
    
    
     
    private function recoger_usuario($email){
         $arr = array();
         $obj_usuario_c = new Usuario_class();
         $tabla = $obj_usuario_c->tabla;
         
         $obj_usuario_c->get_registro_campo("email", $email);
         $arreglo = $obj_usuario_c->arreglo;         
                  
         $obj_crud = new Crud_model();   
         $obj_crud->tabla = $tabla;
         $reg = $obj_crud->get_registros($arreglo);
         if(count($reg)>0){
             $arr[0] = new Usuario_model();
             $arr[0]->set_registro_arr($reg[0]); 
         }
         return $arr;
    }      
    
    private function enviar_correo(){
            
            if($this->email->send())
            {
                //echo 'Correo enviado';
                return true;
            }
            else
            {
                show_error($this->email->print_debugger());
                return false;
            }
    }
    
   
    private function set_correo(){
            $this->email->from($this->email_emisor, $this->nom_emisor);
            $this->email->to($this->email_receptor);
            $this->email->subject($this->titulo);
            $this->email->message($this->mensaje);
    }
   
    
     
    private function revisar_permisos($obj_acceso,$arr_permisos,$id_seccion,$criterio){
       
        $obj_acceso->id_seccion     = $id_seccion; 
        $obj_acceso->criterio       = $criterio;
        $obj_acceso->arr_permisos   = $arr_permisos;
        
        return $obj_acceso->verificar_permisos();
    } 
    
    
     private function generar_error($tipo=0){
            
         $msg="";
         switch($tipo){
                case 0:
                    $msgError = "El campo es requerido";
                    break;
                case 1:
                    $msgError = "No es un email v&aacute;lido";
                    break;
                case 2:
                    $msgError = "El email no existe";
                    break;
                default:
                    $msgError = "";
                    break;
            }    
            
            $msg ='<div class="alert">
                            <button type="button" class="close" data-dismiss="alert">&times;</button>
                            <strong>¡Atenci&oacute;n! </strong>'.$msgError.' 
                        </div>
                        ';
            return $msg;
            
        }
    
}
/*
*end modules/login/controllers/index.php
*/  