<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
 
class Reserva extends MX_Controller
{
    
    private $data;
    private $hora_TS;
    private $fecha_actual;
    private $arr_reservas;
    private $arr_ovs;
    private $arr_tipos_ovs;
    private $arr_permisos;
    private $arr_perfil;
    private $arr_clientes;
    private $cap_pla;
    private $cap_lib;
    private $id_usuario;
    private $id_planta;
    private $gmt;
    private $mensajes;

    public function __construct()
    {        
        parent::__construct();
        $this->load->helper('url');
        $this->load->helper('date');
        $this->load->library('session');
        
        $this->load->database();
        $this->load->model('crud_model');
        $this->load->model('ov_model');
        $this->load->model('historial_model');
        $this->load->model('capacidad_model');
        $this->load->model('tipo_ov_model');
        $this->load->model('reserva_model');
        
        //$this->load->controller('calendario');
        
        
        
        $this->validar_sesion();
        
        $this->arr_reservas     = array();
        $this->arr_ovs          = array();
        $this->arr_tipos_ovs    = array();
        $this->arr_clientes     = array();
        
        $this->hora_TS          = 0;
        $this->cap_pla          = 0;
        $this->cap_lib          = 0;
        $this->gmt              = -3;
        
        $this->id_usuario       = $this->session->userdata('id_usuario');
        $this->id_planta        = $this->session->userdata('id_planta');
        $this->arr_permisos     = $this->session->userdata('permisos');
        $this->arr_perfil       = $this->session->userdata('tipos');
        
        $this->mensajes[0]      = "Capacidad err&oacute;nea";
        
        $obj_acceso = new Acceso_class();
        $resultado = $this->revisar_permisos($obj_acceso,$this->arr_permisos,7,"c"); // 7: Reserva
        if($resultado)  $this->data['crear_reserva'] = true; else  $this->data['crear_reserva'] = false;
        
        $resultado = $this->revisar_permisos($obj_acceso,$this->arr_permisos,7,"u"); // 7: Reserva
        if($resultado)  $this->data['editar_reserva'] = true; else  $this->data['editar_reserva'] = false;
        
        $resultado = $this->revisar_permisos($obj_acceso,$this->arr_permisos,7,"d"); // 7: Reserva
        if($resultado)  $this->data['eliminar_reserva'] = true; else  $this->data['eliminar_reserva'] = false;
        
        //_________________________________________________________
        
        $resultado = $this->revisar_permisos($obj_acceso,$this->arr_permisos,12,"r"); // 12: desfase para vendedores
        if($resultado)  $this->data['desfase_ov'] = true; else  $this->data['desfase_ov'] = false;
        
        
        
    }
    public function revisar_permisos($obj_acceso,$arr_permisos,$id_seccion,$criterio){
        //$resultado = false;
        $obj_acceso->id_seccion     = $id_seccion; 
        $obj_acceso->criterio       = $criterio;
        $obj_acceso->arr_permisos   = $arr_permisos;
        
        return $obj_acceso->verificar_permisos();
    } 
    
    private function validar_sesion(){
            if( $this->session->userdata('logged_in')== FALSE  ){                                  
                $pag_sigte = "index.php/login/";                
                redirect(base_url().$pag_sigte); 
            }
    }
    
    
    
    public function index($hora_TS = null, $cap_pla=null)
    {
        $this->hora_TS = $hora_TS;
        $this->cap_pla = $cap_pla;
       
       //$this->get_ovs(null, null, 1);
       //$this->get_tipos_ov();       
       $this->get_ovs_aprogramar(); 
       $this->get_reservas();
       
       $html        = $this->crear_tabla();
       //$combo_cli   = $this->crear_combo_cli();
       
       $this->data['combo_cli'] = $this->crear_txt("cliente");//$this->crear_combo_cli();//
       $this->data['reservas']  = $html;
       $arr = $this->get_clientes($this->arr_ovs);
       //var_dump($arr);
       //$arr_cli = $this->get_ovs_aprogramar(null, 1,1);
       
       //var_dump($arr_clis);
       
       $this->data['js_cliente'] = $this->crear_js_clientes($arr);
       //$this->data['combo_cli'] = $combo_cli;
       $this->data['fecha']     = $this->fecha_actual;
       $this->data['cap_lib']   = $this->cap_lib;
       
       $hoy = time(); 
       $gmt = $this->gmt;
       $hoy_gmt = mktime(date("H",$hoy)+$gmt, date("i",$hoy), date("s",$hoy), date("m",$hoy), date("d",$hoy), date("Y",$hoy));
       
       //echo date("d/m/Y H:i",$hoy_gmt);
       
       if($hora_TS >= $hoy_gmt){
          $this->data['display_sol']   = "inline-block;";
       }else{
          $this->data['crear_reserva']   = false; 
          $this->data['editar_reserva']   = false; 
       }
       
       $this->data['entidad'] = "reserva";
            
       $this->load->view('reserva',$this->data);
     }
     
     

     
     
     public function get_reservas(){
            
        $hora_for = date("Y-m-d H:i:s", $this->hora_TS);
        
        $obj_reserva_c      = new Reserva_class();     
        $tabla              = $obj_reserva_c->tabla;
        
        $obj_reserva_c->get_reservas_hora($hora_for, $this->id_planta);
        $arreglo = $obj_reserva_c->arreglo;
       
        $obj_crud = new Crud_model();
        $obj_crud->tabla = $tabla;
        $lista_reservas = $obj_crud->get_registros($arreglo);
        
        $cap_acu    = 0;
        
        for($i=0;$i<count($lista_reservas);$i++){
                $cap_acu += $lista_reservas[$i]['capacidad'];
                         
                $lista_reservas[$i]['fecha'] = strtotime($lista_reservas[$i]['fecha']);

                $this->arr_reservas[$i] = new Reserva_model();             
                $this->arr_reservas[$i]->set_registro_arr($lista_reservas[$i]);
        }            
        //var_dump($this->arr_reservas);
        $this->cap_lib = $this->cap_pla - $cap_acu;
        
        if($this->cap_lib < 0){ $this->cap_lib  = 0; }
        
        //echo $this->cap_lib." ".$this->cap_pla." ".$cap_acu."<br/>";
    }
    
    public function revisar_fecha_entrega($fecha,$id_ov){
     $res = false;
     $arr_ov = $this->get_ovs($id_ov,1);
     
     if($arr_ov){
        if(count($arr_ov)>0){
            $fecha_en = strtotime($arr_ov[0]->fecha_entrega);     
            if($fecha_en>=$fecha){
                $res = true;
            }
        }
     }
     return $res;
    }
    
    
    
    
     public function get_ovs($id_ov = null, $arr=null, $procesada = null){            
        $obj_ov_c   = new Ov_class();
        $tabla      = $obj_ov_c->tabla;
                
        if($procesada != null){
            $obj_ov_c->procesada = 1;
        }
        
        $obj_ov_c->get_ovs($this->id_planta, $id_ov);
        
        $arreglo    = $obj_ov_c->arreglo;
        
        //var_dump($arreglo);
        
        $obj_crud = new Crud_model();
        $obj_crud->tabla = $tabla;
        
        $lista_ovs   = $obj_crud->get_registros($arreglo);
        
        //var_dump($lista_ovs);
        
        if($arr==null){        
            for($i=0;$i<count($lista_ovs);$i++){
                $this->arr_ovs[$i] = new Ov_model();             
                $this->arr_ovs[$i]->set_registro_arr($lista_ovs[$i]);
            }    
        }else{
            $arr= array();
            for($i=0;$i<count($lista_ovs);$i++){
                $arr[$i] = new Ov_model();             
                $arr[$i]->set_registro_arr($lista_ovs[$i]);
            }
            return $arr;
        }
    }
    
    public function get_reserva_id($id_reserva = null){   
        $arr_obj = array();
        $obj_c = new Reserva_class();
        $obj_c->get_registro_campo("id_reserva", $id_reserva);
        
        $obj_crud = new Crud_model();
        $obj_crud->tabla = $obj_c->tabla;
        
        $reg   = $obj_crud->get_registros($obj_c->arreglo);
                
        for($i=0;$i<count($reg);$i++){
            $arr_obj[$i] = new Reserva_model();             
            $arr_obj[$i]->set_registro_arr($reg[$i]);
        }
        return $arr_obj;
    }
    
    
    
    
    public function get_ovs_cliente($id_cliente){        
        $obj_ov_c   = new Ov_class();  
        $tabla      = $obj_ov_c->tabla;
        
        //$obj_ov_c->get_cliente($this->id_planta, $id_ov);
        //$arreglo = $obj_ov_c->arreglo;
        
        $obj_crud = new Crud_model();
        $obj_crud->tabla = $tabla;
        
        //$lista_ovs   = $obj_crud->get_registros($arreglo);
        
        $obj_ov_c->get_ovs($this->id_planta, null, null, null, $id_cliente);
        $arreglo = $obj_ov_c->arreglo;
        
        $lista_ovs   = $obj_crud->get_registros($arreglo);
        
        //var_dump($lista_ovs);
        //return;
                
        for($i=0;$i<count($lista_ovs);$i++){
            $this->arr_ovs[$i] = new Ov_model();             
            $this->arr_ovs[$i]->set_registro_arr($lista_ovs[$i]);
        } 
        
    }
    
    public function get_ovs_aprogramar($id_ov=null,$ret=null,$cli=null){
         $obj_ov_c = new Ov_class();
         $tabla = $obj_ov_c->tabla;
         
         if($cli != null){
             $obj_ov_c->grupo = "ov.cliente";
             $obj_ov_c->orden = array(array("campo" => "ov.cliente", "direccion" => "asc"));
         }
         $obj_ov_c->get_ovs($this->id_planta,$id_ov);
         $arreglo = $obj_ov_c->arreglo;
         
         
         
         
         $obj_crud = new Crud_model();
         $obj_crud->tabla = $tabla;
                  
         $lista_ovs = $obj_crud->get_registros($arreglo);
            
         //var_dump($lista_ovs);
         
         
        $i = 0;    
        $arr = array();
        foreach ($lista_ovs as $valor) {
            $arr_res = $this->get_reservas_ov($valor['id_ov'],1);
            $sum = $this->get_sum_reservas($arr_res);
            
            //echo $sum." ".$valor['capacidad']." ".$valor['id_ov']." ".$valor['id_tipo']."<br />";
            
            if($valor['capacidad']>0){
                //if($valor['id_cliente'] != null){
                    if(($sum < $valor['capacidad']) || ($valor['id_tipo']!=1)){
                        if($ret==null){
                            $this->arr_ovs[$i] = new Ov_model(); 
                            $this->arr_ovs[$i]->set_registro_arr($valor);
                        }else{
                            $arr[$i] = new Ov_model();
                            $arr[$i]->set_registro_arr($valor);
                        }
                        $i++;
                    }else{
                        echo "no entro <br />";
                    }
                //}    
            }else{
                    if($ret==null){
                        $this->arr_ovs[$i] = new Ov_model(); 
                        $this->arr_ovs[$i]->set_registro_arr($valor);
                    }else{
                        $arr[$i] = new Ov_model();
                        $arr[$i]->set_registro_arr($valor);
                    }
                    $i++;
            }    
        } 
        
        if($ret!=null){
            return $arr;
        }
        
        
        
     }
    
     
     private function get_sum_reservas($arr_res){
         $sum =0 ;
         foreach($arr_res as $reserva){
             $sum += $reserva->capacidad;
         }
         return $sum;
     }   
     
    public function get_clientes($arr_ovs){
        $arr_cli = array();
        $x = 0;
        //var_dump($arr_ovs);
        foreach($arr_ovs as $valor){
            $llave = true;
            foreach($arr_cli as $valor2){
                if($valor->id_interno == $valor2->id_interno){
                    $llave = false;
                }
            }
            if($llave){
                $arr_cli[$x] = $valor;
                $x++;
            }
        }  
        return $arr_cli;
    }
    
    
     
    
    /*
    public function get_clientes(){
        $obj_ov_c   = new Ov_class();
        $tabla      = $obj_ov_c->tabla;
        
        //$obj_ov_c->fecha = date("Y-m-d H:i");        
        $obj_ov_c->get_clientes($this->id_planta);
        $arreglo    = $obj_ov_c->arreglo;
        
        $obj_crud = new Crud_model();
        $obj_crud->tabla = $tabla;
        
        $arr_clientes   = $obj_crud->get_registros($arreglo);
        
        for($i=0;$i<count($arr_clientes);$i++){
            $this->arr_clientes[$i] = new Ov_model();          
            $this->arr_clientes[$i]->set_registro_arr($arr_clientes[$i]);
        }
        //var_dump($this->arr_tipos_ovs);
    }
    */
    
    public function get_reservas_ov($id_ov,$ret=null){
        $obj_reservas_c = new Reserva_class();     
        $tabla = $obj_reservas_c->tabla;
        
        $obj_reservas_c->get_reservas_ov($id_ov, $this->id_planta);
        $arreglo = $obj_reservas_c->arreglo;
        
        $obj_crud = new Crud_model();
        $obj_crud->tabla = $tabla;
        
        $lista_reservas = $obj_crud->get_registros($arreglo);
        
        if($ret==null){
            for($i=0;$i<count($lista_reservas);$i++){
                $this->arr_reservas[$i] = new Reserva_model();          
                $this->arr_reservas[$i]->set_registro_arr($lista_reservas[$i]);
            }
        }else{
            $arr = array();
            for($i=0;$i<count($lista_reservas);$i++){
                $arr[$i] = new Reserva_model();          
                $arr[$i]->set_registro_arr($lista_reservas[$i]);
            }
            return $arr;
        }
        //var_dump($this->arr_tipos_ovs);
    }
    
    
    
    public function crear_tabla(){
        $tab_enc = '
            <table class="table table-bordered table-hover tabla_reservas" >
            <tr>
                <th> Id </td>
                <th> OV </td>
                <th> Tipo </td>
                <th> Cliente </td>
                <th> Prioridad</td>
                <th> Piezas</td>
                <th> Kgs </td>
                <th> Vigas </td>';
        
         if($this->data['eliminar_reserva']){       
            $tab_enc .= '<th> Anular</td>';
         }
        
        $tab_enc .= '    </tr>';
        
        $tab_cue = "";
        
        //var_dump($this->arr_reservas);
        
        for(    $i=0;$i<count($this->arr_reservas);$i++     ){
            
            $provisorio = "Provisoria";
            
            if($this->arr_reservas[$i]->id_tipo == $provisorio){
               // $this->arr_reservas[$i]->id_tipo .= ' <img src="'.base_url().'/assets/img/ok.png"></img>';
            }
            
            switch($this->arr_reservas[$i]->prioridad){
                case 1: $prioridad = "Alta"; break;
                case 2: $prioridad = "Media"; break;
                case 3: $prioridad = "Baja"; break;
            }
            
            $id = $this->arr_reservas[$i]->id_reserva;
            
            $tab_cue .= '<tr id="res_'.$this->arr_reservas[$i]->id_reserva.'">';
                $tab_cue .= "<td>".$this->arr_reservas[$i]->id_reserva."</td>";
                $tab_cue .= "<td>".$this->arr_reservas[$i]->id_ov."</td>"; 
                $tab_cue .= "<td>".$this->arr_reservas[$i]->id_tipo;
                
                if($this->arr_reservas[$i]->id_ovsap !=null ){
                    $tab_cue .= ' ('.$this->arr_reservas[$i]->id_ovsap.')'; 
                }  
                
                $tab_cue .= "</td><td>".$this->arr_reservas[$i]->cliente."</td>";  
                $tab_cue .= "<td>".$prioridad."</td>"; 
                //$tab_cue .= "<td>".$this->arr_reservas[$i]->piezas."</td>";
                $tab_cue .= "<td>".$this->crear_vinculo_act("piezas", $id, $this->arr_reservas[$i]->piezas)."</td>";
                $tab_cue .= "<td>".$this->crear_vinculo_act("kilogramos", $id, $this->arr_reservas[$i]->kilogramos)."</td>";
                $tab_cue .= "<td>".$this->crear_vinculo_act("capacidad", $id, $this->arr_reservas[$i]->capacidad)."</td>";
                
                if($this->data['eliminar_reserva']){
                    $tab_cue .= '<td> <a href="javascript:void(0);" onclick="eliminar_reserva('.$this->arr_reservas[$i]->id_reserva.');"><center><img src="'.base_url().'/assets/img/delete.png" /></center></img></a></td>';
                }
            $tab_cue .= '</tr>'; 
        }
        $tab_pie = '</table>';
        return $tab_enc.$tab_cue.$tab_pie;
    }
    
    public function crear_vinculo_act($campo,$id,$valor,$clase=null,$tipo=null, $fuente=null, $tabla = null){
         $vin_fin = '</a>';
         $vin_pre = '<a href="javascript:void(0);" class="editable" data-name="'.$campo.'" data-pk="'.$id.'" ';
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
    
    
    private function crear_txt($nombre,$value=null)
    {  
       if ($value != null ){
           $value = " value='".$value."'";
       } 
       $html = '
            <div id="searchfield_'.$nombre.'">
            <input type="text" '.$value.' name="cam_'.$nombre.'" class="span3" id="cam_'.$nombre.'">
            </div>';  
       
       return $html;
    }
    
    private function crear_js_clientes($arr){
        //var_dump($arr);
        $html = "";
        foreach($arr as $valor){
           $html .="{value:'";
           $html .= $valor->cliente;
           if($valor->id_interno != null){
                $html .= " (".$valor->id_interno.")";
           } 
           $html .="',data:'".$valor->id_cliente."'},";
        }
        $html = substr($html, 0, -1);
        return $html;
    }
        
    
    public function crear_combo_cli(){
        $txt_enc        = '<select id="combo_cli" class="span3" onchange="cambiar_ov()">';
        $txt_cue        = '<option value="">Seleccionar</option>';        
        $obj_ov         = new Ov_model();
        
        //$this->get_clientes();    
        $arr_cli = $this->get_ovs_aprogramar(null, 1,1);
        $this->arr_clientes = $arr_cli;      
        
        for(    $i=0;$i<count($this->arr_clientes);$i++     ){
            $txt_cue .= '<option value="'.  $this->arr_clientes[$i]->id_ov.'">'.$this->arr_clientes[$i]->cliente.'</option>';
        }            
        $txt_pie = '</select>';
        return $txt_enc.$txt_cue.$txt_pie;
    }
    
    
     private function parsear_cli($cliente){
         $cliente   = explode(" (", $cliente);
         $cliente   = explode(")", $cliente[1]);
         return $cliente[0];
     }
    
    
     // Método ejecutado por ajax
    public function crear_combo_ov(){
        $id_cliente = $this->parsear_cli($this->input->post('id_cliente'));
        //echo $id_cliente;
        
        //return;
        $txt_enc        = '<select id="id_ov" class="span3" onchange="cambiar_datos_ov();">';
        //$txt_cue        = '';    
        $txt_cue        = '<option value="">Seleccionar</option>';      
         
        $this->get_ovs_cliente($id_cliente);
        
        for(    $i=0;$i<count($this->arr_ovs);$i++  ){            
            $txt_cue .= '<option value="'.$this->arr_ovs[$i]->id_ov.'">'.$this->arr_ovs[$i]->id_ov;
            if($this->arr_ovs[$i]->id_ovsap){
                $txt_cue .= ' ('.$this->arr_ovs[$i]->id_ovsap.')';
            }    
            $txt_cue .= '</option>';
        }            
        $txt_pie = '</select>';
        echo $txt_enc.$txt_cue.$txt_pie;
    }
    
    
    private function verificar_propietario($id_res){
         $arr = $this->get_reserva_id($id_res);
         if(count($arr)>0){
             if($arr[0]->id_usuario == $this->id_usuario){
                 return true;
             }
         }
         return false;
     }
    
    
    public function eliminar(){
        
        
        
        $id_reserva = $this->input->post('id_reserva');
        
        
        $modo = null;
         if($this->arr_perfil){
             foreach($this->arr_perfil as $valor){
                 if($valor['id_tipo'] == 5){
                     $modo = "prog_ven";
                     //echo "bla";
                 }
             }
         }
         
         if($modo == "prog_ven"){
            if(!$this->verificar_propietario($id_reserva)){
                echo -5;
                return;
            }         
         }
        
            if($id_reserva!=null){
                
                $obj_reserva_c  = new Reserva_class();
                $tabla          = $obj_reserva_c->tabla;
                
                $condicion = array( "id_reserva" => $id_reserva);
                $arreglo   = array( "condicion" => $condicion);
                $obj_crud           = new Crud_model();
                $obj_crud->tabla    = $tabla;  
                
                $arr_res = $this->get_reserva_id($id_reserva);  
                $resto = 1;
                if($arr_res){
                    if(count($arr_res)>0){
                        //var_dump($arr_res);
                        //var_dump($arr_res[0]->id_ov);
                        $this->get_ovs($arr_res[0]->id_ov);
                        //var_dump($this->arr_ovs);
                        $this->get_reservas_ov($this->arr_ovs[0]->id_ov);
                    }
                }    
                $resto = $this->calcular_cap_faltante();
                
                //var_dump($resto);
                //return;
                //var_dump($this->arr_reservas);
                if($resto <= 0){
                    if($this->arr_ovs[0]->estado == 1){
                        $obj_ov_c       = new Ov_class();
                        $obj_crud_ov    = new Crud_model();
                        $obj_crud_ov->tabla = $obj_ov_c->tabla;
                        $campo = "estado";
                        $valor = 0;
                        if($this->arr_ovs){
                            if(count($this->arr_ovs)>0){
                                $this->actualizar($obj_crud_ov, $this->arr_ovs[0]->id_ov, $campo, $valor, "id_ov");
                            }
                        }            
                    }
                }
                //var_dump($resto);
                //return;
                
                
                $respuesta = $obj_crud->delete_registro($arreglo);
                
                if($respuesta == 1){
                /**********************************/
                $objetivo_tipo = 2; // 1: ov, 2: reserva
                $objetivo_id = $id_reserva;
                $id_interaccion = 3; // : 1 crear, 2 editar, 3 eliminar 
                $id_usuario = $this->id_usuario;
                $id_planta = $this->id_planta;
                        
                $respuesta = $this->guardar_log($id_usuario,$objetivo_tipo,$objetivo_id,$id_interaccion,$id_planta);
                
                
                
                /*
                $resto = $this->calcular_cap_faltante();
                if($resto - $res_data['capacidad']== 0){
                    //var_dump($this->arr_ovs);
                    if($this->arr_ovs[0]->id_tipo == 1){
                        $obj_ov_c       = new Ov_class();
                        $obj_crud_ov    = new Crud_model();
                        $obj_crud_ov->tabla = $obj_ov_c->tabla;
                        $campo = "estado";
                        $valor = 1;
                        if($this->arr_ovs){
                            if(count($this->arr_ovs)>0){
                                $this->actualizar($obj_crud_ov, $this->arr_ovs[0]->id_ov, $campo, $valor, "id_ov");
                            }
                        }            
                    }
                }
                */
                
                
                
                /**********************************/
                }
                
                
            }else{
               $respuesta = -1;
                
            }
       echo $respuesta;     
    }
    
    
     public function editar(){
         //$tabla = $this->input->post('entidad');
         
         if(!$this->data['editar_reserva']){
             echo 0;
             return;
         }
         
         
         
         
         
         $campo = $this->input->post('name');
         $valor = $this->input->post('value');
         $id    = $this->input->post('pk');
         $resultado = array();
         $tabla = "reserva"; 
         
         
         
         
         $modo = null;
         if($this->arr_perfil){
             foreach($this->arr_perfil as $valor2){
                 if($valor2['id_tipo'] == 5){
                     $modo = "prog_ven";
                     //echo "bla";
                 }
             }
         }
         
         if($modo == "prog_ven"){
            if(!$this->verificar_propietario($id)){
                echo -5;
                return;
            }         
         }
         
         
         
         if(    (isset($tabla)  )&&(    isset($campo)  )&&(    isset($valor)  )&&(    isset($id)  )  ){
             
             switch($campo){
                 //case "clave"   :  $valor     = $obj_usuario->encriptar_clave($valor);   break;
                 case "capacidad"   :                     
                     $fecha     = strtotime($this->get_fecha_reserva($id));
                     $cap_dis   = $this->revisar_capacidad(0, $fecha);                
                     
                     $cap_ant = $this->get_reserva_id($id);
                     
                     if(isset($cap_ant[0]->capacidad)){
                        $cap_dis = ($cap_ant[0]->capacidad + $this->cap_lib)-$valor;
                     }else{
                        $cap_dis = -1;
                     }
                     
                     //echo $cap_dis; 
                     //return;
                     
                     if($cap_dis<0){
                        array_push($resultado,  $this->mensajes[0]);  // Capacidad errónea
                         //$resultado = $this->mensajes[0];
                     } 
                     break;
             }
             
             if(count($resultado)==0){
                 $obj_crud = new Crud_model();
                 $obj_crud->tabla = $tabla;
                 //echo $valor;
                 //return ;
                 
                 $resultado[0] = $this->actualizar($obj_crud, $id, $campo, $valor);
                 
                   //**********************************/
                   $objetivo_tipo = 2; // 1: ov, 2: reserva
                   $objetivo_id = $id;
                   $id_interaccion = 2; // : 1 crear, 2 editar, 3 eliminar 
                   $id_usuario = $this->id_usuario;
                   $id_planta  = $this->id_planta;

                   $respuesta = $this->guardar_log($id_usuario,$objetivo_tipo,$objetivo_id,$id_interaccion,$id_planta);

                   //**********************************/
                
             }
             
         }
         echo $resultado[0];
     }  
     
     
     private function get_fecha_reserva($id_reserva){
         $fecha = null;
         $obj_reserva_c      = new Reserva_class();
         $obj_reserva_c->get_registro_campo("id_reserva", $id_reserva);
         $obj_crud           = new Crud_model();
         $obj_crud->tabla    = $obj_reserva_c->tabla;
         $reg = $obj_crud->get_registros($obj_reserva_c->arreglo);
         if(count($reg)>0){
            $fecha = $reg[0]['fecha'];
         }   
         //echo $fecha;
         return $fecha;
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
    
    
    
    
    // Método ejecutado por ajax    
    public function insertar(){
        
        $id_ov      = $this->input->post('id_ov');
        $prioridad  = $this->input->post('prioridad');
        $capacidad  = $this->input->post('capacidad');
        $piezas     = $this->input->post('piezas');
        $kilogramos = $this->input->post('kilogramos');
        $fecha      = $this->input->post('fecha');
         
        $respuesta = 0;
        
                
        if($capacidad!=null){
            try{
                $capacidad = intval($capacidad);
                
                if($capacidad <0){
                    $capacidad *=-1;
                }
                
            }catch(Exception $ex){
                $capacidad = null;
            }
        }
        
         //$arr_fecha = explode("/", $fecha);
         //$fecha_entrega = $arr_fecha[2]."-".$arr_fecha[1]."-".$arr_fecha[0];
        
        $fecha_env =$fecha;
        $fecha_hoy = now();
        // Regla de desfase       
         if($this->data['desfase_ov']){ 
             $fecha_hoy = mktime(date("H"),date("i"),date("s"), date("m"), date("d")+$this->session->userdata('desfase'), date("Y"));
         }
         
         // Regla para que no ingresen reservas anteriores a la fecha actual          
        if($fecha_env < $fecha_hoy){ echo -4; return; } // Fecha incorrecta
        
         // Regla para que no ingresen reservas posteriores a la fecha de entrega          
        //if(!$this->revisar_fecha_entrega($fecha_env, $id_ov)){ echo -4; return; } // Fecha incorrecta
        
        //var_dump($this->revisar_fecha_entrega($fecha_env, $id_ov));
        //return;
        
        try {
            
            $fecha_creacion = date("Y-m-d H:i",now());
            
            $id_usuario = $this->id_usuario;
            $id_planta  = $this->id_planta;     
            
            
            if(($capacidad!=null) && ($fecha!=null) && ($id_ov!=null)){
                $capacidad = $this->revisar_capacidad($capacidad,$fecha);                
                if(!$this->revisar_cap_ov($capacidad,$id_ov)){
                    $capacidad = null;
                }
            }
            
            
            
            if( ($id_ov!=null) && ($prioridad!=null) && ($capacidad!=null) && ($piezas!=null) && 
                ($kilogramos!=null) && ($id_usuario!=null) && ($fecha!=null)  ){
                
            //$arr_res    = $this->get_reservas_ov($id_ov,1);
            
                
                $fecha = date("Y-m-d H:i",$fecha);
                
                
                $obj_res    = new Reserva_model();
                $res_data   = array(
                                'prioridad'             => $prioridad,
                                'id_usuario'            => $id_usuario,                            
                                'fecha_creacion'        => $fecha_creacion,
                                'id_ov'                 => $id_ov,
                                /*'id_planta'             => $id_planta,*/
                                'capacidad'             => $capacidad,
                                'kilogramos'            => $kilogramos,
                                'piezas'                => $piezas,
                                'fecha'                 => $fecha                            
                                );
                
                $obj_res->set_registro_arr($res_data);
                $data = $obj_res->get_registros_arr();
                
                $obj_reserva_c  = new Reserva_class();
                $tabla          = $obj_reserva_c->tabla;
            
                $obj_crud           = new Crud_model();
                $obj_crud->tabla    = $tabla;  
                $obj_crud->add_registro($data);                
                //$obj_res->add_registro();
                
                $respuesta = 1; // Satisfactoria
            }else{
                if($capacidad == null){
                    $respuesta = -3; // Cap incorrecta
                }else{
                    $respuesta = -1; // Faltan datos
                }
                
            }
        }catch(Exception $ex){
            $respuesta = -2; // Error
        }
        
        if($respuesta == 1){
         /**********************************/
                $objetivo_tipo = 2; // 1: ov, 2: reserva
                $objetivo_id = $obj_crud->id_ultimo;
                $id_interaccion = 1; // : 1 crear, 2 editar, 3 eliminar 
                $id_usuario = $this->id_usuario;
                $id_planta = $this->id_planta;
                        
                $respuesta = $this->guardar_log($id_usuario,$objetivo_tipo,$objetivo_id,$id_interaccion,$id_planta);
                
                $resto = $this->calcular_cap_faltante();
                if($resto - $res_data['capacidad']<= 0){
                    //var_dump($this->arr_ovs);
                    if($this->arr_ovs[0]->id_tipo == 1){
                        $obj_ov_c       = new Ov_class();
                        $obj_crud_ov    = new Crud_model();
                        $obj_crud_ov->tabla = $obj_ov_c->tabla;
                        $campo = "estado";
                        $valor = 1;
                        if($this->arr_ovs){
                            if(count($this->arr_ovs)>0){
                                $this->actualizar($obj_crud_ov, $this->arr_ovs[0]->id_ov, $campo, $valor, "id_ov");
                            }
                        }            
                    }
                }
                
                //var_dump($resto);
                //var_dump($this->arr_reservas);
                
         /**********************************/
        }
        
        echo $respuesta;
    }
    
    
    
    
    public function guardar_log($id_usuario,$objetivo_tipo,$objetivo_id,$id_interaccion, $id_planta){
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
    
    public function revisar_cap_ov($capacidad,$id_ov){
        $obj_ov = new Ov_model();
        $obj_ov->set_registro_arr($this->generar_datos_ov($id_ov, null));
        
        //echo $obj_ov->capacidad." ".$capacidad." ";
        
        if($obj_ov->capacidad >= $capacidad){
            return true;
        } 
        return false;
        //var_dump($obj_ov);       
    }
    
    public function revisar_capacidad($capacidad,$fecha){
        
        
        $fecha_for = date("Y-m-d H:i",$fecha);        
        $obj_capacidad_c = new Capacidad_class();
        $tabla = $obj_capacidad_c->tabla;
        
        $obj_capacidad_c->get_capacidades_dh($fecha_for, $this->id_planta);//get_capacidades_planta($this->id_planta);
        $arreglo = $obj_capacidad_c->arreglo;
        
        $obj_crud = new Crud_model();
        $obj_crud->tabla = $tabla;
        $capacidad_reg = $obj_crud->get_registros($arreglo);
        
        $obj_capacidad = new Capacidad_model();
        if(isset($capacidad_reg[0])){
            $obj_capacidad->capacidad = $capacidad_reg[0]["capacidad"];
        }else{
            $obj_capacidad->capacidad = 0;
        }
        $this->cap_pla = $obj_capacidad->capacidad;   
        $this->hora_TS = $fecha;
        $this->get_reservas();        
        //echo $capacidad." ".$this->cap_lib."<br />";
        if($capacidad <= $this->cap_lib){
            return $capacidad;
        }
        
        return null;                
    }
    
    public function generar_datos_ov($id_ov,$json=1){
        $this->get_ovs($id_ov);        
        //$this->get_tipos_ov();
        //for($i=0;$i<count($this->arr_tipos_ovs);$i++){
        
        //var_dump($this->arr_ovs[0]);
        //return;
        
        if(isset($this->arr_ovs[0])){
            $arreglo[0] = $this->arr_ovs[0]->get_registros_arr(1);
            //var_dump($arreglo);
            //return;
            /*
            foreach($this->arr_tipos_ovs as $valor){

                    if($arreglo[0]['id_tipo'] == $valor->id_tipo){
                        $arreglo[0]['tipo_nombre'] = $valor->nombre;
                    }
            }    
            */            
            $this->get_reservas_ov($id_ov);
            $cap_reservada = $this->calcular_cap_reservada();
            $arr = $cap_reservada;

            $arreglo[0]['capacidad'] -= $arr["cap"]; 
            if($arreglo[0]['capacidad']<0)  $arreglo[0]['capacidad'] = 0;  

            $arreglo[0]['piezas'] -= $arr["piezas"]; 
            if($arreglo[0]['piezas']<0)  $arreglo[0]['piezas'] = 0 ; 

            $arreglo[0]['kilogramos'] -= $arr["kilogramos"]; 
            if($arreglo[0]['kilogramos']<0)  $arreglo[0]['kilogramos'] = 0  ;        
        }
        /*
        $arreglo[0]['cap_reservada']        = $arr["cap"];
        $arreglo[0]['piezas_reservadas']    = $arr["piezas"];
        $arreglo[0]['kilogramos_reservados']= $arr["kilogramos"];
        
         * //var_dump($this->arr_reservas);
         */
        if($json == 1){
            echo json_encode($arreglo);        
        }else{
            return $arreglo[0];
        }  
        //var_dump($arreglo);
    }
    
    public function calcular_cap_reservada(){
        $cap            = 0;
        $piezas         = 0;
        $kilogramos     = 0;
        for($i=0;$i<count($this->arr_reservas);$i++){
            $cap        += $this->arr_reservas[$i]->capacidad;
            $piezas     += $this->arr_reservas[$i]->piezas;
            $kilogramos += $this->arr_reservas[$i]->kilogramos;                   
        }
   
        $arreglo = array(
                        "cap"       => $cap,
                        "piezas"    => $piezas,
                        "kilogramos"=> $kilogramos
                        );
        return $arreglo;
    }
    
    public function calcular_cap_faltante(){
        $resto          = 0;
        $cap            = 0;
        for($i=0;$i<count($this->arr_reservas);$i++){
            $cap        += $this->arr_reservas[$i]->capacidad;                
        }
        
        if($this->arr_ovs){
            if(count($this->arr_ovs)>0){
                $resto = $this->arr_ovs[0]->capacidad - $cap;
            }
        }
        return $resto;
    }

}
/*
*end modules/login/controllers/index.php
*/  