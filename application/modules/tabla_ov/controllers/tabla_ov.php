<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
 
class Tabla_ov extends MX_Controller
{
    
    private $data;
    private $id_usuario;
    private $id_planta;
    private $dias_min;
    public  $arr_ovs;
    public  $arr_permisos;
    public  $tabla;
    public  $arr_reservas;
    private $arr_perfil;
    private $dias_sincro;
    private $auto_act;
    private $arr_ovs_mod;
    
    
    public function __construct()
    {        
        parent::__construct();
        $this->load->library('session');
        $this->load->helper('url');
        $this->load->helper('date');
        
        $this->validar_sesion();
        
        $this->load->helper('security');
        $this->load->library('encrypt');
        $this->key = do_hash("getkem2013", 'md5'); // MD5;        
        
        $this->load->database();
        
        $this->load->model('cliente_model'); 
        $this->load->model('ov_model');        
        $this->load->model('crud_model');
        $this->load->model('historial_model');
        $this->load->model('reserva_model');
        $this->load->model('usuario_model');
        
        $this->id_planta        = $this->session->userdata('id_planta');        
        $this->id_usuario       = $this->session->userdata('id_usuario'); 
        $this->arr_permisos     = $this->session->userdata('permisos'); 
        $this->arr_perfil       = $this->session->userdata('tipos'); 
        
        $this->arr_ovs_mod = array();
        
        $this->data['sincro']   = $this->session->userdata('sincro'); 
        
        $arr_sincro = $this->get_preferencia("id_preferencia", 6);
        $this->dias_sincro      = $arr_sincro[0]['valor']; 
        $arr_sincro = $this->get_preferencia("id_preferencia", 7);
        $this->auto_act = strtolower($arr_sincro[0]['valor']);        
        
        $this->dias_min         = 0;
        $this->arr_ovs          = array();
        $this->arr_reservas     = array();
        
        $this->tabla            = "";
        
        $obj_acceso = new Acceso_class();
        $resultado = $this->revisar_permisos($obj_acceso,$this->arr_permisos,6,"c"); // 6: OV
        if($resultado)  $this->data['crear_ov'] = true; else  $this->data['crear_ov'] = false;
        
        $resultado = $this->revisar_permisos($obj_acceso,$this->arr_permisos,6,"u"); // 6: OV
        $this->data['deshabilitar']    = "";
        if($resultado){  
            $this->data['editar_ov'] = true; }             
        else{  
            $this->data['editar_ov'] = false;
            $this->data['deshabilitar']    = "disabled";
        }
        
        $resultado = $this->revisar_permisos($obj_acceso,$this->arr_permisos,6,"d"); // 6: OV
        if($resultado)  $this->data['eliminar_ov'] = true; else  $this->data['eliminar_ov'] = false;
        
        $resultado = $this->revisar_permisos($obj_acceso,$this->arr_permisos,12,"r"); // 12: desfase para vendedores
        if($resultado)  $this->data['desfase_ov'] = true; else  $this->data['desfase_ov'] = false;
        
        $resultado = $this->revisar_permisos($obj_acceso,$this->arr_permisos,13,"u"); // 13: modificar ov confirmadas
        if($resultado)  $this->data['editar_ov_c'] = true; else  $this->data['editar_ov_c'] = false;
        
    }
    
    private function validar_sesion(){
            if( $this->session->userdata('logged_in')== FALSE  ){                                  
                $pag_sigte = "index.php/login/";                
                redirect(base_url().$pag_sigte); 
            }
    }
    
    public function index()
    {
       $this->get_ovs_aprogramar();
       $this->crear_tabla();
       $this->get_html();       
       
       $this->load->view('index',  $this->data);
     }
     
     public function revisar_permisos($obj_acceso,$arr_permisos,$id_seccion,$criterio){
        //$resultado = false;
        $obj_acceso->id_seccion     = $id_seccion; 
        $obj_acceso->criterio       = $criterio;
        $obj_acceso->arr_permisos   = $arr_permisos;
        
        return $obj_acceso->verificar_permisos();
    } 
     
    
    public function crear_combo_cli(){
        $txt_enc        = '<select id="combo_cli" class="span3">';
        $txt_cue        = '<option value="">Seleccionar</option>';        
        //$obj_ov         = new Ov_model();
        //$this->get_clientes();    
        //$arr_cli = $this->get_ovs_aprogramar(null, 1,1);
        //$this->arr_clientes = $arr_cli;      
        
        for(    $i=0;$i<count($arr_cli);$i++     ){
            $txt_cue .= '<option value="'.  $arr_cli[$i]->nombre.'">'.$arr_cli[$i]->nombre.'</option>';
        }            
        $txt_pie = '</select>';
        return $txt_enc.$txt_cue.$txt_pie;
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
        
        $html = "";
        foreach($arr as $valor){
           $html .="{value:'";
           $html .= $valor->nombre;
           $html .= " (".$valor->id_interno.")";
           
           $html .="',data:'".$valor->id_cliente."'},";
        }
        $html = substr($html, 0, -1);
        return $html;
    }
    
     public function ov(){
         $fecha = now();
         $fecha_entrega  = mktime(null,null,null,date("m",$fecha),date("d",$fecha)+$this->dias_min,date("Y",$fecha));  
         
         $arr = $this->get_clientes();
         
         $this->data['txt_cliente'] = $this->crear_txt("cliente");//$this->crear_combo_cli();//
         
         //var_dump($arr);
         
         $this->data['js_cliente'] = $this->crear_js_clientes($arr);
         //var_dump($this->data['js_cliente']);
         
         $this->data['fecha_format'] = date("d/m/Y",  $fecha_entrega);        
         $this->load->view('ov',$this->data);         
     }
     
     private function get_clientes(){
         $obj_ov_c = new Cliente_class();
         $obj_ov_c->get_registro_campo();
         $arreglo = $obj_ov_c->arreglo;
                  
         $obj_crud = new Crud_model();
         $obj_crud->tabla = $obj_ov_c->tabla;                  
         $registros = $obj_crud->get_registros($arreglo);
               
         $i = 0;
         foreach ($registros as $valor) {
            $arr_obj[$i] = new Cliente_model();             
            $arr_obj[$i]->set_registro_arr($valor);
            $i++;
         } 
         
         return $arr_obj;         
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
         //var_dump($this->arr_ovs);
         
         $this->data['cliente']         = $this->arr_ovs[0]->cliente;
         $this->data['capacidad']       = $this->arr_ovs[0]->capacidad;
         $this->data['kilogramos']      = $this->arr_ovs[0]->kilogramos;
         $this->data['piezas']          = $this->arr_ovs[0]->piezas;
         $this->data['id_ovsap']        = $this->arr_ovs[0]->id_ovsap;
         $this->data['id_tipo']         = $this->arr_ovs[0]->id_tipo;
         //var_dump($this->arr_ovs[0]);
         //return;
         $fecha = strtotime($this->arr_ovs[0]->fecha_entrega);
         $fecha_entrega  = mktime(null,null,null,date("m",$fecha),date("d",$fecha),date("Y",$fecha));  
         
         //$this->data['fecha_format']    = $this->arr_ovs[0]->fecha_entrega;     
         $this->data['fecha_format']    = date("d/m/Y",  $fecha_entrega);              
         $this->data['comentario']      = $this->arr_ovs[0]->comentario;
         
         
         
         $arr = $this->get_clientes();
         $this->data['txt_cliente'] = $this->crear_txt("cliente",$this->arr_ovs[0]->cliente);
         $this->data['js_cliente'] = $this->crear_js_clientes($arr);
         
         //$this->get_ovs_conf($this->arr_ovs[0]->cliente);
         $this->arr_ovs = null;
         $this->get_ovs_aprogramar(null, $this->data['cliente'],1);
         
         //var_dump($this->arr_ovs);
         
         $this->data['combo_sap']    = $this->crear_celda_sap($this->data['id_ovsap'], $this->arr_ovs);//$this->crear_combo_ovs($this->arr_ovs);
         
         $this->load->view('ov_view',$this->data);
     }     
     
     
    public function crear_celda_sap($id_sap,$arr_ovs){
      if($id_sap != null){
            $html     =   "<td>Id. SAP</td>";
            $html     .=  '<td style="width: 20px;">&nbsp;</td>';
            $html     .=  '<td><input id="id_sap" type="hidden" value="'.$id_sap.'" />'.$id_sap.'</td>';   
      }else{
            $html     =   "<td>Enlazar con SAP</td>";
            $html     .=  '<td style="width: 20px;">&nbsp;</td>';
            
            //var_dump($arr_ovs);
            
            $html     .=  '<td>'.$this->crear_combo_ovs($arr_ovs).'</td>';   
      }
      return $html;
    }      
     
     
     public function crear_combo_ovs($arr_ovs){
         $html = "<select id='id_ovsap' style='span2'>";
         $html  .= "<option selected>Seleccionar</option>";
         if($arr_ovs){
            foreach($arr_ovs as $valor){
                $html .= "<option value='".$valor->id_ov."'>";
                $html .= $valor->id_ovsap;
                $html .= "</option>";
            }
         }
         $html .= "</select>";
         return $html;
     }
     
     
      
     
          
     
     public function get_ovs_conf($cliente = null){
         $obj_ov_c = new Ov_class();
         $tabla = $obj_ov_c->tabla;
         
         $campo = "id_tipo";
         $valor = "1";
         
         if($cliente != null){
             $obj_ov_c->cliente = $cliente;
         }
         
         $obj_ov_c->id_planta = $this->id_planta;
         $obj_ov_c->get_registro_campo($campo, $valor);
         $arreglo = $obj_ov_c->arreglo;
         
         $obj_crud = new Crud_model();
         $obj_crud->tabla = $tabla;
                  
         $lista_ovs = $obj_crud->get_registros($arreglo);

        $i = 0;        
        foreach ($lista_ovs as $valor) {
            $this->arr_ovs[$i] = new Ov_model();             
            $this->arr_ovs[$i]->set_registro_arr($valor);
            $i++;
        } 
        //var_dump($obj_ov_c);
     }
     
     public function get_ovs($campo, $valor){
         //$arr = array();
         $obj_ov_c = new Ov_class();
         $tabla = $obj_ov_c->tabla;
         $obj_ov_c->get_registro_campo($campo, $valor);
         $arreglo = $obj_ov_c->arreglo;
         
         $obj_crud = new Crud_model();
         $obj_crud->tabla = $tabla;
                  
         $lista_ovs = $obj_crud->get_registros($arreglo);
        return $lista_ovs;
        //var_dump($obj_ov_c);
     }
     
     public function get_ovs_campo($campo, $valor){
         $obj_ov_c = new Ov_class();
         $tabla = $obj_ov_c->tabla;
         $obj_ov_c->get_registro_campo($campo, $valor);
         $arreglo = $obj_ov_c->arreglo;
         
         $obj_crud = new Crud_model();
         $obj_crud->tabla = $tabla;
                  
         $lista_ovs = $obj_crud->get_registros($arreglo);

        $i = 0;        
        foreach ($lista_ovs as $valor) {
            $this->arr_ovs[$i] = new Ov_model();             
            $this->arr_ovs[$i]->set_registro_arr($valor);
            $i++;
        } 
        //var_dump($obj_ov_c);
     }
     
     
     
     public function get_ovs_aprogramar($id_ov=null,$cliente=null,$tipo=null){
         $obj_ov_c = new Ov_class();
         $tabla = $obj_ov_c->tabla;
         
         if($tipo!=null){
             $obj_ov_c->id_tipo = $tipo;
         }
         
         
         
         $obj_ov_c->get_ovs($this->id_planta,$id_ov,$cliente);
         $arreglo = $obj_ov_c->arreglo;
         
         
         
         $obj_crud = new Crud_model();
         $obj_crud->tabla = $tabla;
                  
         $lista_ovs = $obj_crud->get_registros($arreglo);
         
         //var_dump($lista_ovs);
         
         
        $i = 0;        
        foreach ($lista_ovs as $valor) {
            $arr_res = $this->get_reservas_ov($valor['id_ov'],1);
            $sum = $this->get_sum_reservas($arr_res);   
            
            if($valor['capacidad']>0){
                if(($sum < $valor['capacidad']) || ($valor['id_tipo']!=1)){
                    $this->arr_ovs[$i] = new Ov_model(); 
                    $this->arr_ovs[$i]->set_registro_arr($valor);
                    $i++;
                }
            }else{
                    $this->arr_ovs[$i] = new Ov_model(); 
                    $this->arr_ovs[$i]->set_registro_arr($valor);
                    $i++;
            }    
        } 
        //var_dump($obj_ov_c);
     }
     
     private function get_sum_reservas($arr_res){
         $sum =0 ;
         foreach($arr_res as $reserva){
             $sum += $reserva->capacidad;
         }
         return $sum;
     }    
     
     
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
     
    
     public function generar_datos_ov($arr_ovs,$arr_res){
        //$this->get_ovs($id_ov);        
        
        
        if(isset($arr_ovs[0])){
            $arreglo[0] = $arr_ovs[0];
                  
            //$arr_res = $this->get_reservas_ov($id_ov,1);
            $arr = $this->calcular_cap_reservada($arr_res);
            
            $arreglo[0]['capacidad'] -= $arr["cap"]; 
            if($arreglo[0]['capacidad']<0)  $arreglo[0]['capacidad'] = 0;  

            $arreglo[0]['piezas'] -= $arr["piezas"]; 
            if($arreglo[0]['piezas']<0)  $arreglo[0]['piezas'] = 0 ; 

            $arreglo[0]['kilogramos'] -= $arr["kilogramos"]; 
            if($arreglo[0]['kilogramos']<0)  $arreglo[0]['kilogramos'] = 0  ;        
        }
        
        return $arreglo[0];
    }
    
    
     public function calcular_cap_reservada($arr_reservas){
        $cap            = 0;
        $piezas         = 0;
        $kilogramos     = 0;
        for($i=0;$i<count($arr_reservas);$i++){
            $cap        += $arr_reservas[$i]->capacidad;
            $piezas     += $arr_reservas[$i]->piezas;
            $kilogramos += $arr_reservas[$i]->kilogramos;                   
        }
   
        $arreglo = array(
                        "cap"       => $cap,
                        "piezas"    => $piezas,
                        "kilogramos"=> $kilogramos
                        );
        return $arreglo;
    }
    
     
     public function crear_tabla(){
         
         $this->tabla = '<table id="ordenes_vta" class="table table-bordered table-hover" >';         
         $this->tabla .='
            <tr> 
                <th> OV </th>
                <th> Tipo </th>
                <th> Cliente </th>
                <th> Entrega </th>
                <!-- th> Piezas</td>
                <th> Kgs </td-->
                <th> Vigas </th>
                <th> Detalle </th>
            </tr>';
         
         
         
         if(count($this->arr_ovs)>0){
             for($i=0;$i<count($this->arr_ovs);$i++){
                 
                $arr_res = $this->get_reservas_ov($this->arr_ovs[$i]->id_ov, 1);
                
                $arr_ov[0] = $this->arr_ovs[$i]->get_registros_arr();
                
                
                
                
                $arr_result = $this->generar_datos_ov($arr_ov, $arr_res);
                 //var_dump($arr_result);
                
                $obj_falta_ov = new Ov_model();
                $obj_falta_ov->set_registro_arr($arr_result);
                 
                $fecha_corta = strtotime($this->arr_ovs[$i]->fecha_entrega);
                $fecha_corta = date("d/m/Y", $fecha_corta); 
                
                $img_ver        = "<img src='".base_url()."/assets/img/ver.png'>";
                
                
                $this->arr_ovs_mod = $this->session->userdata('ovs_mod'); 
                $b = false;
                if($this->arr_ovs_mod){ 
                    if(count($this->arr_ovs_mod) > 0){
                        foreach($this->arr_ovs_mod as $arr_ovs_mod){
                            if($this->arr_ovs[$i]->id_ov == $arr_ovs_mod){
                                $b = true;
                            }
                        }
                    }
                }
                
                $estilo_b = "";
                if($b){
                  $estilo_b = 'style="font-weight:bold;"';  
                }                
                
                $this->tabla .='
                <tr '.$estilo_b.'> 
                   <td> '.$this->arr_ovs[$i]->id_ov.' </td>
                   <td> '.$this->arr_ovs[$i]->tipo_nombre.' ';
                
                if($this->arr_ovs[$i]->id_ovsap !=null ){
                    $this->tabla .= ' ('.$this->arr_ovs[$i]->id_ovsap.')'; 
                }       
                
                
                
                $cli_rec = $this->cortar_palabras($this->arr_ovs[$i]->cliente);

                $this->tabla .='</td>                        
                   <td> '.$cli_rec.' </td>
                   <td> '.$fecha_corta.' </td>
                   <!--td> '.$this->arr_ovs[$i]->piezas.'</td>
                   <td> '.$this->arr_ovs[$i]->kilogramos.' </td-->
                   <td> '.$this->arr_ovs[$i]->capacidad.' ('.$obj_falta_ov->capacidad.')</td>
                   <td><a href="javascript:void(0);" class="det_ov" data-id-tipo="'.$this->arr_ovs[$i]->id_tipo.'" data-id-ov="'.$this->arr_ovs[$i]->id_ov.'" ><center>'.$img_ver.'</center></td>    
                </tr>';
             }
         }
         
         $this->tabla .= '</table>';
          //var_dump($this->arr_ovs);
     }
     
     private function cortar_palabras($frase="frasedeejemplo",$lim=7,$seg=3){
         $texto = "";
         $palabras = explode(" ",$frase);
         foreach($palabras as $pal){
             if(strlen($pal)>$lim){
                $texto .= substr($pal, 0,$seg).". ";
             }else{
                $texto .= $pal." ";
             }        
         }         
         return $texto;
     }
     
     
     
     public function get_html(){
         $this->data['tabla'] = $this->tabla;
     }
     
     
     
      public function eliminar_ov(){    
         
         $respuesta = 0;
         
         $id_ov       = $this->input->post('id_ov');
                  
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
            if(!$this->verificar_propietario($id_ov)){
                echo -5;
                return;
            }         
         }
         
         
         
         if(    $id_ov!=null    ){
             
             $obj_ov_c  = new Ov_class();
             $tabla     = $obj_ov_c->tabla;             
             
             $obj_crud = new Crud_model();
             $obj_crud->tabla = $tabla;
             
             $condicion = array( "id_ov" => $id_ov);
             $arreglo   = array( "condicion" => $condicion);             
             $respuesta = $obj_crud->delete_registro($arreglo);
             
             //$respuesta = $this->actualizar($obj_crud, $id_ov, "id_tipo", "3", "id_ov");
             
             
             
                       
             
             
             if($respuesta) {$respuesta=1;
             
                $res = $this->eliminar_reservas($id_ov);
             
             
                
                /**********************************/
                $objetivo_tipo = 1; // 1: ov, 2: reserva
                $objetivo_id = $id_ov;
                $id_interaccion = 3; // : 1 crear, 2 editar, 3 eliminar 
                $id_usuario = $this->id_usuario;
                $id_planta  = $this->id_planta;
                        
                $respuesta = $this->guardar_log($id_usuario,$objetivo_tipo,$objetivo_id,$id_interaccion,$id_planta);
                
                /**********************************/
                }
             else 
                 {
                 $respuesta = -2;                 
                 }
         }else{
             $respuesta = -1;
         }
         
         echo $respuesta;
         
     }
     
public function eliminar_reservas($id_ov){
    $res = false;
    if($id_ov != null){
        $obj_reserva_c  = new Reserva_class();
        $tabla     = $obj_reserva_c->tabla;             
             
        $obj_crud = new Crud_model();
        $obj_crud->tabla = $tabla;
             
        //$condicion = array( "id_ov" => $id_ov);
        //$arreglo   = array( "condicion" => $condicion);             
        $res = $this->actualizar($obj_crud, $id_ov, "valido", "0", "id_ov");
    }        
    return $res;
}
     
     
     
     
     
     
     
     
         
     public function insertar_ov($sap=null){    
         
         $respuesta = 0;
         
         $cliente       = $this->input->post('cliente');
         
         if (!$this->validar_cliente($cliente)){echo -11; return;}         
         
         $id_cliente    = $this->parsear_cli("id",$cliente);
         $cliente       = $this->parsear_cli("nombre",$cliente);
         
         $capacidad     = $this->input->post('capacidad');
         $kilogramos    = $this->input->post('kilogramos');
         $piezas        = $this->input->post('piezas');
         $fecha_entrega = $this->input->post('fecha_entrega');
         $comentario    = $this->input->post('comentario');
         
         $arr_fecha = explode("/", $fecha_entrega);
         $fecha_entrega = $arr_fecha[2]."-".$arr_fecha[1]."-".$arr_fecha[0]." ".date("H").":".date("i");
         
         $fecha_env = strtotime($fecha_entrega);
         $fecha_hoy = now();
         
         // Regla de desfase       
         if($this->data['desfase_ov']){ 
             $fecha_hoy = mktime(date("H")-1,date("i"),date("s"), date("m"), date("d")+$this->session->userdata('desfase'), date("Y"));
         }
         // Regla para que no ingresen ov anteriores a la fecha actual          
         //if($fecha_env < $fecha_hoy){ echo -4; return; }
         
         // Regla para que no ingresen clientes desconocidos  
         
         
         
         //echo $id_cliente; 
         //return;
         
         $id_cliente = $this->verificar_cliente($id_cliente);
         if(!$id_cliente){ echo -11; return; }
         
         
         
         if(    ($cliente!=null) && ($capacidad!=null) && ($kilogramos!=null) && ($piezas!=null) && ($fecha_entrega!=null)    ){
             
             $obj_ov_c  = new Ov_class();
             $tabla     = $obj_ov_c->tabla;             
             
             $obj_ov = new Ov_model();
             $cliente = str_replace("%20"," ",$cliente);
             
             $data = array(
                    'id_usuario'            => $this->id_usuario,
                    'capacidad'             => $capacidad,
                    'kilogramos'            => $kilogramos,
                    'piezas'                => $piezas,
                    'id_planta'             => $this->id_planta,
                    'valido'                => 1,
                    'id_tipo'               => 2,
                    'id_cliente'            => $id_cliente,
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
             
             if($respuesta) {$respuesta=1;
                
                /**********************************/
                $objetivo_tipo = 1; // 1: ov, 2: reserva
                $objetivo_id = $obj_crud->id_ultimo;
                $id_interaccion = 1; // : 1 crear, 2 editar, 3 eliminar 
                $id_usuario = $this->id_usuario;
                $id_planta  = $this->id_planta;
                        
                $respuesta = $this->guardar_log($id_usuario,$objetivo_tipo,$objetivo_id,$id_interaccion,$id_planta);
                
                /**********************************/
                }
             else 
                 {
                 $respuesta = -2;                 
                 }
         }else{
             $respuesta = -1;
         }
         
         echo $respuesta;
         
     }
     
     
     private function validar_cliente($cliente){
        $ret = false;
        $findme   = ' (';
        $pos    = strpos($cliente, $findme);
        
        $findme   = ')';
        $pos2   = strpos($cliente, $findme);
        
        if(($pos !== false)&&($pos2!=false)){
            $ret = true;
        }
        return $ret;        
     }
     
     
     private function parsear_cli($tipo="id", $cliente){
         $cliente   = explode(" (", $cliente);
         if($tipo == "id"){            
            $cliente   = explode(")", $cliente[1]);         
         }
         return $cliente[0];
     }
     
     
     public function editar_ov($obj_ov_par=null){    
         
         $respuesta = 0;
         $res_ovsap = 0;
         
         if($obj_ov_par == null){
            //$cliente       = $this->input->post('cliente');
            $capacidad     = $this->input->post('capacidad');
            $kilogramos    = $this->input->post('kilogramos');
            $piezas        = $this->input->post('piezas');
            $fecha_entrega = $this->input->post('fecha_entrega');
            $comentario    = $this->input->post('comentario');
            $id_ov         = $this->input->post('id_ov');
            $id_ovsap      = $this->input->post('id_ovsap');
            $ov_tipo       = $this->input->post('ov_tipo');
            
            //$id_cliente    = $this->parsear_cli("id",$cliente);
            //$cliente       = $this->parsear_cli("nombre",$cliente);
            
            
            $arr_fecha = explode("/", $fecha_entrega);
            $fecha_entrega = $arr_fecha[2]."-".$arr_fecha[1]."-".$arr_fecha[0];
         }else{
            //$cliente       = $obj_ov_par->cliente;
            $capacidad     = $obj_ov_par->capacidad;
            $kilogramos    = $obj_ov_par->kilogramos;
            $piezas        = $obj_ov_par->piezas;
            $fecha_entrega = $obj_ov_par->fecha_entrega;
            $id_ovsap      = $obj_ov_par->id_ovsap;
            $ov_tipo       = $obj_ov_par->id_tipo;
            //$id_cliente    = $obj_ov_par->id_cliente;
            $id_ov         = $obj_ov_par->id_ov;
         }
         
         // valores por defecto (cliente, id_cliente)
         $arr_def = $this->get_ovs("id_ov", $id_ov);
         
         //var_dump($arr_def);         
         //return;
         
         
         
         
         $fecha_env = strtotime($fecha_entrega);
         $fecha_hoy = now();
         
          // Regla de desfase       
         if($this->data['desfase_ov']){ 
             $fecha_hoy = mktime(date("H")-1,date("i"),date("s"), date("m"), date("d")+$this->session->userdata('desfase'), date("Y"));
         }
         // Regla para que no ingresen ov anteriores a la fecha actual
         /*
         if($obj_ov_par == null){
            if($fecha_env < $fecha_hoy){ echo -4; return; }
         }
         */
         
         // Regla para que no ingresen clientes desconocidos         
         //if(!$this->verificar_cliente(null, $cliente)){ echo -11; return; }
         
         
         
         $modo = null;
         if($this->arr_perfil){
             foreach($this->arr_perfil as $valor){
                 if($valor['id_tipo'] == 5){
                     $modo = "prog_ven";
                     //echo "bla";
                 }
             }
         }
         //var_dump($this->arr_perfil);
         //echo $modo;
         //return;
         
         if($modo == "prog_ven"){
            if(!$this->verificar_propietario($id_ov)){
                echo -5;
                return;
            }         
         }
         
         
         if(    ($capacidad!=null) && ($kilogramos!=null) && ($piezas!=null) && ($fecha_entrega!=null)    ){
             
             //var_dump($this->arr_ovs);
             
             if($id_ovsap != null){
                $this->get_ovs_campo("id_ov", $id_ovsap);
                $res_ovsap = count($this->arr_ovs);
                if($res_ovsap>0){
                    $id_ov_ant  = $this->arr_ovs[0]->id_ov;
                    $id_sap_ant = $this->arr_ovs[0]->id_ovsap;
                }                
             }
             //return;
             $obj_ov_c  = new Ov_class();
             $tabla     = $obj_ov_c->tabla;             
             
             
             //$cliente = str_replace("%20"," ",$cliente);
             if($obj_ov_par == null){
                $data = array(
                       'id_ov'                 => $id_ov,
                       'id_usuario'            => $this->id_usuario,
                       'capacidad'             => $capacidad,
                       'kilogramos'            => $kilogramos,
                       'piezas'                => $piezas,
                       'id_planta'             => $this->id_planta,
                       'valido'                => 1,
                       'id_tipo'               => 2,
                       'cliente'               => $arr_def[0]['cliente'],
                       'id_cliente'            => $arr_def[0]['id_cliente'], 
                       'fecha_entrega'         => $fecha_entrega,

                       'comentario'            => $comentario                     
                );
             }else{
                 $data = $obj_ov_par->get_registros_arr();
                 
             }
                          
             
             if($res_ovsap >0){             
                if($id_ovsap != null){
                    $data['id_tipo']   = 1;
                    $data['id_ovsap']  = $id_sap_ant;                 
                }
             }
             
             if(    ($ov_tipo == 1) && ($this->data['editar_ov_c']) ){
                $data['id_tipo']   = 1; 
                $data['id_ovsap']  = $id_ovsap; 
             }
             
             //var_dump($data);
             //return;
             
             $obj_ov = new Ov_model();
             $obj_ov->set_registro_arr($data);
             $new_data = $obj_ov->get_registros_arr();
             
             $obj_crud = new Crud_model();
             $obj_crud->tabla = $tabla;
             
             
             $condicion = array("id_ov" => $id_ov);
             
             $arreglo = array(
                                "actualizacion" => $new_data,
                                "condicion"     => $condicion
                                );
             
             $respuesta = $obj_crud->update_registro($arreglo);
             //echo "respuesta=".$respuesta;
             //return;
             
             if($respuesta) {
                 
                 
                if($res_ovsap>0){
                   if($id_ovsap != null){
                       $respuesta = $this->reemplazar_ov($id_ov_ant,$id_ov);
                       if(!$respuesta){ return -3;}
                       $respuesta = $this->anular_ov($id_ov_ant,$id_ov); 
                       if(!$respuesta){ return -4;}
                   }
                }  
                
                
                /**********************************/
                $objetivo_tipo = 1; // 1: ov, 2: reserva
                $objetivo_id = $id_ov;
                $id_interaccion = 2; // : 1 crear, 2 editar, 3 eliminar 
                $id_usuario = $this->id_usuario;
                $id_planta  = $this->id_planta;
                        
                $respuesta = $this->guardar_log($id_usuario,$objetivo_tipo,$objetivo_id,$id_interaccion,$id_planta);
                if(!$respuesta){ return -5;}
                /**********************************/
                $respuesta = 1;
                }
             else 
                 {
                 $respuesta = -2;                 
                 }
         }else{
             $respuesta = -1;
         }
         //var_dump($respuesta);
         if($obj_ov_par==null){
            echo $respuesta;
         }else{
            return $respuesta;
         }   
     }
     
     private function verificar_propietario($id_ov){
         $this->get_ovs_campo("id_ov", $id_ov);
         if(count($this->arr_ovs)>0){
             if($this->arr_ovs[0]->id_usuario == $this->id_usuario){
                 return true;
             }
         }
         return false;
     }
     
     
     private function reemplazar_ov($id_ov_ant,$id_ov_nva){  
         
         $respuesta = false;
         $obj_ov_c  = new Ov_class();
         
         $obj_ov_c->get_registro_campo("id_ov", $id_ov_ant);
         $arreglo = $obj_ov_c->arreglo;
         $obj_crud          = new Crud_model();
         $obj_crud->tabla   = $obj_ov_c->tabla;
         $reg = $obj_crud->get_registros($arreglo);
                
         if(count($reg)>0){
                $obj_ov = new Ov_model();
                $obj_ov->set_registro_arr($reg[0]);
                $obj_ov->id_ov          = $id_ov_nva;
                $obj_ov->id_usuario     = $this->id_usuario;
                $obj_ov->id_ovsap_old   = $id_ov_ant;
                $actualizacion = $obj_ov->get_registros_arr();
                
                //var_dump($actualizacion);
                //return;
                
                
                $condicion = array(
                                    "id_ov" => $id_ov_nva
                                    
                                    );
                
                $arreglo = array(
                                "actualizacion" => $actualizacion,
                                "condicion"     => $condicion
                                );
                
                $obj_crud          = new Crud_model();
                $obj_crud->tabla   = $obj_ov_c->tabla;
                
                
                
                $respuesta = $obj_crud->update_registro($arreglo);
                
         }        
         return $respuesta;
     }
     
     
     private function anular_ov($id_ov_ant,$id_ov_nva){         
         $obj_ov        = new Ov_class();
         $obj_reserva   = new Reserva_class();
         $obj_crud      = new Crud_model();
          
         $obj_crud->tabla = $obj_reserva->tabla;         
         $res = $this->actualizar($obj_crud, $id_ov_ant, "id_ov", $id_ov_nva, "id_ov");
         
                
         $obj_crud->tabla = $obj_ov->tabla;         
         $res = $this->actualizar($obj_crud, $id_ov_ant, "valido", 0, "id_ov");
         
         return $res;
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
     
     public function sincronizar_sap($auto=null){
        
        $this->session->unset_userdata('ovs_mod');
        if($auto == null){
            $fin = $this->input->post('fin'); 
        }else{
            $fecha_ts = mktime(date("H"), date("i"), date("s"), date("m"), date("d")+$this->dias_sincro, date("Y"));
            $fin = date("d/m/Y",$fecha_ts);
        }
        $res = array();
        
        if($fin==null){
            $this->data['fecha_ini'] = date("d/m/Y");             
            $this->data['fecha_ter'] = date("d/m/Y", mktime(date("H"), date("i"), null, date("m"), date("d")+1, date("Y"))); 
        
            $this->load->view('sincro',$this->data);
            return 0;
        }else{
            
            $org_vta = $this->recoge_org_vta($this->id_planta);
            
            //var_dump($org_vta);
            
            $res += $this->revisar_fechas($fin,$org_vta);
            $this->session->set_userdata('ovs_mod',  $this->arr_ovs_mod);
            //var_dump($this->arr_ovs_mod);
            echo 1;
            //echo json_encode($res);
            return 1;
            
        }    
     }
     
     
     
     public function revisar_fechas($fecha,$org_vta){
         
         $ovs_nvas  = 0;
         $errores   = 0;
         $cli_nvos  = 0;
         $res = 0;
         
         $arr_fecha = explode("/", $fecha);
         
         $hoy       = time();
         $fecha_TS  = mktime(null, null, null, $arr_fecha[1], $arr_fecha[0]+1, $arr_fecha[2]);
         
         while($hoy < $fecha_TS){
             $fecha_entrega = date("Ymd",$hoy);
             $fecha_hoy_TS  = date("Y-m-d");
             //$fecha_entrega = "20120102";
             $arr_ovs_sap = $this->recoger_ov_sap($fecha_entrega, $org_vta);
             
             //var_dump($arr_ovs_sap);
             //return;
             if($arr_ovs_sap['T_PEDVTA']){
                $res = $this->comparar_ov($arr_ovs_sap['T_PEDVTA'],$fecha_hoy_TS);
             }
             if(($res == 4) || ($res == 2)){
                 $ovs_nvas++;
             }
             
             if($res == 4){ $cli_nvos++; }
             if($res == 3){ $errores++; }

             //echo $fecha_entrega." ".$res."<br/>";
             $hoy = mktime(null, null, null, date("m",$hoy), date("d",$hoy)+1, date("Y",$hoy));
         }
         
         //var_dump($this->arr_ovs_mod);
         
         $resultado = array("ovs"       => $ovs_nvas,
                            "clientes"  => $cli_nvos,
                            "errores"   => $errores,
                            "resultado" => 1
                            );
         return $resultado;
                 
     }
     
            /*
            VBELN	Identificador de pedido de venta
            POSNR	Tipo de pedido
            VKORG	Planta
            VTWEG	División
            SPART	?
            KUNNR	Identificador de cliente
            NAME1	Nombre de cliente
            VDATU	Fecha de entrega
            PIEZAS	Piezas
            VIGAS	Vigas
            MATNR	Código de material
            MAKTX	Descripción del material
            KWMENG	Kilogramos
            */
     
     
     
     public function comparar_ov($arr_ovs,$fecha){
         
         $msje[0] = 1; // Sincronizacion exitosa sin cambios
         $msje[1] = 2; // Sincronizacion con OVs nuevas
         $msje[2] = 3; // Error
         $msje[3] = 4; // Cliente nuevo
         
         $respuesta = $msje[0]; 
         $cambio = false;
         
         if($arr_ovs){
            foreach($arr_ovs as $valores){
             
             $reg = $this->verificar_ov($valores['VBELN']);
             //var_dump($reg);
             //echo $valores['VBELN']." reg:".$reg."<br />";
             
             //return;
             
             $data_nvo = array(
                                'id_usuario'            => -1,
                                'capacidad'             => $valores['VIGAS'],
                                'kilogramos'            => $valores['KWMENG'],
                                'piezas'                => $valores['PIEZAS'],
                                'id_planta'             => $this->id_planta,
                                'valido'                => 1,
                                'id_tipo'               => 1,
                                'id_ovsap'              => $valores['VBELN'],
                                'cliente'               => $valores['NAME1'],
                                'id_material'           => $valores['MATNR'],
                                'material'              => $valores['MAKTX'], 
                                'posicion'              => $valores['POSNR']                    
                                );  
             
             $obj_valor = new Ov_model();
             $obj_valor->set_registro_arr($data_nvo);
             
             
             if($reg){
                
                $arr_ovs = $this->get_ovs("id_ovsap", $valores['VBELN']);
                //var_dump($arr_ovs);
                
                
                foreach($arr_ovs as $lista_ov_sap){
                    $obj_ov = new Ov_model();
                    $obj_ov->set_registro_arr($lista_ov_sap);
                    
                    $fecha_sap = $this->format_fecha_sap($obj_ov->fecha_entrega, 1);
                    
                    //echo $fecha_sap." ".$valores['VDATU']."<br/>";
                    
                    
                    
                    if(
                            ($obj_ov->kilogramos != $obj_valor->kilogramos) || 
                            ($obj_ov->piezas != $obj_valor->piezas) ||
                            ($obj_ov->capacidad != $obj_valor->capacidad)
                            //($fecha_sap != $valores['VDATU'])
                        )
                    {
                        //echo "hay cambios ";
                        
                        
                        
                        $cambio = true;
                        $agregar = true;
                        //var_dump($obj_valor);
                        //var_dump($obj_ov);
                        $this->arr_ovs_mod = $this->session->userdata('ovs_mod'); 
                        //var_dump($this->arr_ovs_mod);
                        //return;
                         if(!$this->arr_ovs_mod){                                                    
                            $this->arr_ovs_mod = array();
                         }  
                         
                         //var_dump($this->arr_ovs_mod);
                         
                            foreach($this->arr_ovs_mod as $arr_ovs_mod){
                              if($obj_ov->id_ov == $arr_ovs_mod){
                                   $agregar = false;
                              }                     
                            }
                            if($agregar){
                                array_push($this->arr_ovs_mod, $obj_ov->id_ov); 
                                $this->session->set_userdata('ovs_mod',$this->arr_ovs_mod);
                            }    
                        
                        if(    ($cambio == true)   && ($this->auto_act == "si")   ){
                            $obj_ov->capacidad  = $valores['VIGAS'];
                            $obj_ov->piezas     = $valores['PIEZAS'];
                            $obj_ov->kilogramos = $valores['KWMENG'];
                            //$obj_ov->fecha_entrega = $this->conv_fecha_sap($valores['VDATU']);
                            $res = $this->editar_ov($obj_ov);
                            //echo $res;
                        }
                    }
                }    
             }
             
             //return;
             
             
             if(    !$reg    ){ // Si no existe la agrega
                 
                 $respuesta = $msje[1];
                 
                 //var_dump($this->verificar_cliente($valores['KUNNR']));
                 //return;
                 
                 $res = $this->verificar_cliente($valores['KUNNR']);
                 if(    !$res    ){
                     $respuesta = $msje[3];
                     $obj_cliente       = new Cliente_model();
                     $obj_cliente_c     = new Cliente_class();
                     $arreglo = array(
                                        "id_interno"    => $valores['KUNNR'],
                                        "nombre"        => $valores['NAME1'],
                                        "alias"         => $valores['NAME1']
                                        );
                     
                     $obj_cliente->set_registro_arr($arreglo);                     
                     $res = $this->agregar_obj($obj_cliente, $obj_cliente_c);
                     
                  }
                                    
                  $fecha_nva = $this->conv_fecha_sap($valores['VDATU']);
                  //echo $fecha_nva."<br/>"; 
                  //return;
                  
                    $data = array(
                                'id_usuario'            => -1,
                                'capacidad'             => $valores['VIGAS'],
                                'kilogramos'            => $valores['KWMENG'],
                                'piezas'                => $valores['PIEZAS'],
                                'id_planta'             => $this->id_planta,
                                'valido'                => 1,
                                'id_tipo'               => 1,
                                'id_ovsap'              => $valores['VBELN'],
                                'cliente'               => $valores['NAME1'],
                                'id_cliente'            => $res,
                                'fecha_entrega'         => $fecha_nva,
                                'id_material'           => $valores['MATNR'],
                                'material'              => $valores['MAKTX'], 
                                'posicion'              => $valores['POSNR']                    
                                );      
                    
                    //var_dump($data);
                    $res = $this->ins_ov($data);   
                     
                     
                    if($res == 0){
                        $respuesta = $msje[2]; 
                    }  
                 
             }
             
           }
         }
         return $respuesta;
     } 
     
     public function agregar_obj($obj,$obj_c){
         $arreglo = $obj->get_registros_arr();
         
         $obj_crud = new Crud_model();
         $obj_crud->tabla = $obj_c->tabla;
         $res = $obj_crud->add_registro($arreglo);
         
         if($res){
             return $obj_crud->id_ultimo;
         }else{
             return false;
         }
         
     }
     
     public function verificar_ov($id_ovsap){
         $obj_ov = new Ov_class();
         $campo = "id_ovsap";
         $valor = $id_ovsap;
         
         $obj_ov->id_planta = $this->id_planta;
         
         $obj_ov->get_registro_campo($campo, $valor);
         $arreglo = $obj_ov->arreglo;
         
         //var_dump($arreglo);
         //return;
         
         
         $obj_crud = new Crud_model();
         $obj_crud->tabla = $obj_ov->tabla;
         $reg = $obj_crud->get_registros($arreglo);
         //var_dump($reg);
         //return;
         if(count($reg)>0){
             return true;
         }else{
             return false;
         }         
    }
     
     public function verificar_cliente($id_cliente,$nombre=null){
         $obj_cliente = new Cliente_class();
         
         if($nombre == null){
            $campo = "id_interno";
            $valor = $id_cliente;
         }else{
            $campo = "nombre";
            $valor = $nombre;
         }
         $obj_cliente->get_registro_campo($campo, $valor);
         $arreglo = $obj_cliente->arreglo;
         
         $obj_crud = new Crud_model();
         $obj_crud->tabla = $obj_cliente->tabla;
         $reg = $obj_crud->get_registros($arreglo);
         //var_dump($reg);
         //return;
         if(count($reg)>0){
             //return true;
             return $reg[0]["id_cliente"];
         }else{
             return false;
         }         
    }
     
     
     
     public function ins_ov($data){
             $obj_ov_c  = new Ov_class();
             $tabla     = $obj_ov_c->tabla;             
             
             $obj_ov = new Ov_model();
             
             $obj_ov->set_registro_arr($data);
             $new_data = $obj_ov->get_registros_arr();
             
             $obj_crud = new Crud_model();
             $obj_crud->tabla = $tabla;
             return $obj_crud->add_registro($new_data);             
             
     }      
     
     
     
     
     
     public function recoge_org_vta($id_planta){
         $obj_planta = new Planta_class();
         $obj_planta->get_planta($id_planta);         
         $arreglo = $obj_planta->arreglo;
         
         $obj_crud = new Crud_model();
         $obj_crud->tabla = $obj_planta->tabla;
         $regs = $obj_crud->get_registros($arreglo);
         return $regs[0]["id_interno"];
         //return 2000;
     }
     
     public function format_fecha_sap($fecha,$tipo=null){
         if($tipo==null){
            $arr_fecha = explode("/", $fecha);
            return $arr_fecha[2]."".$arr_fecha[1]."".$arr_fecha[0];
         }else if($tipo == 1){
            $fecha = explode(" ", $fecha);
            $arr_fecha = explode("-", $fecha[0]);
            return $arr_fecha[0]."".$arr_fecha[1]."".$arr_fecha[2];
         }
     }
     
     public function conv_fecha_sap($fecha){
         $fecha_format = substr($fecha, 0,4)."/".substr($fecha, 4,2)."/".  substr($fecha, 6, 2);
         return $fecha_format;
     }
        
     
     public function get_usuario($campo,$valor){
          $arr = array();
          $obj_usu    = new Usuario_class();
          $tabla      = $obj_usu->tabla;

          $obj_usu->get_registro_campo($campo, $valor);
          $arreglo    = $obj_usu->arreglo; 

          $obj_crud = new Crud_model();
          $obj_crud->tabla = $tabla;
          $registros = $obj_crud->get_registros($arreglo);     
          
          
          $i=0;
          foreach($registros as $valor){
              $arr[$i] = new Usuario_model();
              $arr[$i]->set_registro_arr($valor); 
              $i++;
          }
          //var_dump($arr);
          return $arr;
     }
     
     public function get_preferencia($campo=null,$valor=null){
            $obj_preferencia = new Preferencia_class();
            
            //$id = "id_preferencia";
            $obj_crud = new Crud_model();            
            $obj_crud->tabla = $obj_preferencia->tabla;
            
            $obj_preferencia->get_registro_campo($campo,$valor);
            $arreglo = $obj_preferencia->arreglo;            
            
            $registros = $obj_crud->get_registros($arreglo);
            return $registros;
     }
                 
     public function recoger_ov_sap($fecha_entrega=null, $org_vta=null){
           //$this->session->set_userdata('sincro','sap');
           //return $this->arreglo_simulado(); 
           
           $resultado = array();
           
           $arr_sn = $this->get_preferencia("id_preferencia", 4);
           $arr_sc = $this->get_preferencia("id_preferencia", 5);
           
           //$serverName = "/H/190.151.56.63/S/32000/H/10.82.246.18"; 
           $sysNumber  = "00";
           //$sapClient  = "300"; // 100: Des / 200: Pruebas / 300: Prod
           $serverName = $arr_sn[0]['valor'];
           $sapClient  = $arr_sc[0]['valor'];

           
           //$this->session->set_userdata('sincro',$serverName);
           //return;
           
           $sapUser      = "";//$sapUser      = "lguzman";//$_GET['sapUser'];
           $userPassword = "";//$userPassword = "seba1605";//$_GET['userPassword'];
           
           $arr_usu = $this->get_usuario("id_usuario", $this->id_usuario);
           
           if(count($arr_usu)>0){           
                $sapUser      = $arr_usu[0]->id_sap;
                $userPassword =  $this->encrypt->decode($this->session->userdata('cla_enc'), $this->key);
           }
           
           $login       = array(
                                   "logindata"=>array(
                                   "ASHOST"=> $serverName		// application server
                                   ,"SYSNR"=> $sysNumber		// system number
                                   ,"CLIENT"=> $sapClient		// client
                                   ,"USER"=> $sapUser			// user
                                   ,"PASSWD"=> $userPassword            // password
                                   )
                                   ,"show_errors"=>true                 // let class printout errors
                                   ,"debug"=>false);                      // detailed debugging information
           
           
           $sap = new saprfc($login);                   
           //Login
           /* $result=$sap->callFunction("Z_REC_RFC_WD_LOGIN_USUARIO",
                       array(	array("IMPORT","I_USUARIO","$sapUser"),
                                array("IMPORT","I_PASSWORD","$userPassword"),
                                array("EXPORT","E_SALIDA","$respuesta")));
           */

           //$org_vta = 2000;     // id planta
           $canal   = 20;       // planta 2000 tiene division por canales
           
           if($fecha_entrega == null){  $fecha_entrega = "20120101";    }  
           
           $rfcfunction = "Z_REC_RFC_WD_PEDVTA";
           $params      = array(	
                                array("IMPORT","I_VKORG",$org_vta),
                                //array("IMPORT","I_VTWEG",$canal),
                                array("IMPORT","I_DATE",$fecha_entrega),
                                array("TABLE","T_PEDVTA",$resultado)
                                ); 
           
           $sap->callFunction($rfcfunction,$params);
           
           if ($sap->getStatus() == SAPRFC_OK) 
               {
                   //echo " 1 <br/>";        
                   $this->session->set_userdata('sincro','sap');
                   $resultado = $sap->call_function_result;
               } else {                    	
                   //$sap->printStatus();        
               }
           $sap->logoff();
           
           return $resultado;
           /*
            VBELN	Identificador de pedido de venta
            POSNR	Tipo de pedido
            VKORG	Planta
            VTWEG	División
            SPART	?
            KUNNR	Identificador de cliente
            NAME1	Nombre de cliente
            VDATU	Fecha de entrega
            PIEZAS	Piezas
            VIGAS	Vigas
            MATNR	Código de material
            MAKTX	Descripción del material
            KWMENG	Kilogramos
            */
      }
          
      public function arreglo_simulado(){
            return  array(
              "T_PEDVTA" => array(   array(
                                            "VBELN"=>"0000046803",
                                            "POSNR"=>"30",
                                            "VKORG"=>"2000",
                                            "VTWEG"=>"20",
                                            "SPART"=>"00",
                                            "KUNNR"=>"0000995914",
                                            "NAME1"=>"SOCIEDAD INDUSTRIAL FAMEC S.A",
                                            "VDATU"=>"20140102",
                                            "PIEZAS"=>"10000",
                                            "VIGAS"=>"10000",
                                            "MATNR"=>"GS12002012",
                                            "MAKTX"=>"PORTACONDUCTORES",
                                            "KWMENG"=>"10000.000"),
                                     
                                     array(
                                            "VBELN"=>"0000046804",
                                            "POSNR"=>"30",
                                            "VKORG"=>"2000",
                                            "VTWEG"=>"20",
                                            "SPART"=>"00",
                                            "KUNNR"=>"0000995914",
                                            "NAME1"=>"SOCIEDAD INDUSTRIAL FAMEC S.A",
                                            "VDATU"=>"20140102",
                                            "PIEZAS"=>"10000",
                                            "VIGAS"=>"10000",
                                            "MATNR"=>"GS12002012",
                                            "MAKTX"=>"PORTACONDUCTORES2",
                                            "KWMENG"=>"10000.000"),
                  
                                     array(
                                            "VBELN"=>"0000046805",
                                            "POSNR"=>"30",
                                            "VKORG"=>"2000",
                                            "VTWEG"=>"20",
                                            "SPART"=>"00",
                                            "KUNNR"=>"0000995915",
                                            "NAME1"=>"SOCIEDAD INDUSTRIAL FAMEC2 S.A",
                                            "VDATU"=>"20140102",
                                            "PIEZAS"=>"10000",
                                            "VIGAS"=>"10000",
                                            "MATNR"=>"GS12002012",
                                            "MAKTX"=>"PORTACONDUCTORES3",
                                            "KWMENG"=>"10000.000")
                  
                                  ));
     }
}
/*
*end modules/login/controllers/index.php
*/  