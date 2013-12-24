<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
 
class Grafico extends MX_Controller
{
    
    private $arr_plantas;
    //private $arr_ovs;
    private $arr_clientes;
    private $arr_permisos;
    private $arr_capacidades;
    private $arr_vendedores;
    private $data;
    private $tmpl;
    
    
    private $cont_ovs_pro;
    private $cont_ovs_con;
    private $cont_ovs_nco;
    private $cont_ovs_anu;
    
    private $porc;
    
    private $hrs_disp;
    private $id_vendedor;
    private $id_planta;
    private $id_usuario;
    
    private $divs;
    private $divs2;
    
    private $graf3_eje_x;
    
    public function __construct()
    {
        
        parent::__construct();
        
        $this->load->model('planta_model');
        $this->load->model('ov_model');
        $this->load->model('cliente_model');
        $this->load->model('crud_model');
        $this->load->model('tipo_ov_model');
        $this->load->model('capacidad_model');
        
        $this->load->library('session');
        //$this->load->library('libraries/tcpdf/TCPDF');
        
        $this->id_planta        = $this->session->userdata('id_planta');        
        $this->id_usuario       = $this->session->userdata('id_usuario'); 
        
        $this->id_vendedor      = 0;
        $this->hrs_disp         = 8;
        
        $this->load->library('table');
        $this->tmpl = array ('table_open' => '<table class="table table-bordered table-hover table-condensed table-striped tabla_graficos" >');
        
        $this->arr_vendedores = null;
        //$obj_adm = new Administracion();
        $this->graf3_eje_x = null;
        $this->porc = array();
        $this->divs = "";
        $this->divs2 = "";
    }
    
    public function index()
    {   /*
        $data['nombre']         = $this->session->userdata('nombre')." ".$this->session->userdata('ape_paterno');
        $this->arr_plantas      = $this->session->userdata('plantas');
        $this->arr_tipos        = $this->session->userdata('tipos');
        
        
        $this->arr_permisos     = $this->session->userdata('permisos');
        /*
        $obj_acceso = new Acceso_class();
        $resultado = $this->revisar_permisos($obj_acceso,$this->arr_permisos,1,"r"); // 1: Mantenedores
        if($resultado) $data['ver_mant'] = true; else $data['ver_mant'] = false;
        
        $resultado = $this->revisar_permisos($obj_acceso,$this->arr_permisos,2,"r"); // 2: Estadisticas
        if($resultado) $data['ver_esta'] = true; else $data['ver_esta'] = false;
        
        $resultado = $this->revisar_permisos($obj_acceso,$this->arr_permisos,4,"r"); // 4: Mantenedor plantas
        if($resultado) $data['ver_mant_plan'] = true; else $data['ver_mant_plan'] = false;
        
        $data['menu_plantas']   = $this->crear_menu_plantas($this->arr_plantas);        
        $data['menu_tipos']     = $this->crear_menu_tipos($this->arr_tipos);
        
        $data['nom_planta']     = $this->session->userdata('nom_planta');
        $this->load->view('index',$data);
         * 
         */
        //$tmpl = array ('table_open' => '<table class="table table-bordered table-hover table-condensed table-striped" >');
        
        $this->table->set_template($this->tmpl);        
        
        $this->generar_datos();
        
        
        
        
        
        $this->generar_filtros();
          
        $this->load->view('grafico',  $this->data);
        
     }
     
     public function act_datos($tipo=1){
         $this->table->set_template($this->tmpl); 
         $this->generar_datos();
         echo $this->data['tabla_fil_ver'.$tipo];
     }
     
     
     public function generar_datos(){
        $id_vendedor    = $this->input->post('id_vendedor');
        $id_planta      = $this->input->post('id_planta');
        $id_cliente     = $this->input->post('id_cliente');
        
        $des            = $this->input->post('des_prog_input');
        $has            = $this->input->post('has_prog_input');
        
        if($des==$has){ $des = null; $has=null; }
        if($des!=null){ $des = $this->format_fecha_TS($des);    }
        if($has!=null){ $has = $this->format_fecha_TS($has);    }
        
       
        
        if($id_planta == "all"){
            $id_planta = null;            
        }
        
        //Pie
        
        $this->cont_ovs_con = count($this->get_ovs_cont($des,$has,$id_vendedor,$id_cliente,$id_planta,1,"ov.id_ovsap_old !=",""));
        $this->cont_ovs_pro = count($this->get_ovs_cont($des,$has,$id_vendedor,$id_cliente,$id_planta,2,null,null,"todas"))+$this->cont_ovs_con;
        
        $this->cont_ovs_nco = count($this->get_ovs_cont($des,$has,$id_vendedor,$id_cliente,$id_planta,2,null,null));
        $this->cont_ovs_anu = count($this->get_ovs_cont($des,$has,$id_vendedor,$id_cliente,$id_planta,2,"ov.valido",0,"todas"));
        
        $this->data['tabla_fil_ver1']           = $this->crear_tab_fil_ver(1);
        $this->data['tabla_fil_ver2']           = $this->crear_tab_fil_ver(2);
        
        //Area
        
        $desde = strtotime($des);
        $hasta = strtotime($des);
        $ini_mes_ant = mktime(0,0,0, date("m"), date("d")-7, date("Y"));
        //$ini_mes_ant = mktime(0,0,0, date("m"),date("d"), date("Y"));
        
        
        //echo "id_planta=".$id_planta;
        if($des       == null)      {  $des         = date("Y-m-d",$ini_mes_ant); }
        if($has       == null)      {  $has         = date("Y-m-d"); }
        
        //$id_vendedor = "Juan Pérez";
        //$id_planta = 2;
        $arr_ovs = $this->generar_ovs($des,$has,$id_planta,$id_vendedor);
        //var_dump($arr_ovs);
        $this->data['tabla_fil_ver5']   = $this->crear_tab_fil_ver_5($arr_ovs)." ".$this->divs;
        
        
        if($id_planta   == null)    {  $id_planta   = $this->id_planta; }
        
        
        $arr_capacidades = $this->get_capacidades($id_planta,$des,$has);
        
        $arr_cap    = $this->generar_capacidades($des,$has,$arr_capacidades,$id_planta,2,null,null);        
        $this->data['tabla_fil_ver3']   = $this->crear_tab_fil_ver_3($arr_cap)." ".$this->divs;
        
        
        if($des==null && $has == null){
            $limite = 7;
        }else{
            $limite = null;
        }
        
        //$arr_ovs = $this->get_ovs_cont($des, $has, $id_vendedor, $id_cliente,$id_planta,2,null,null,null,$limite);
        //($des=null,$has=null,$id_vendedor=null,$id_cliente=null,$id_planta=null,$id_tipo=null,$campo=null,$valor=null,$todas=null,$limite=null
        //var_dump($arr_ovs);
        
        //$this->data['tabla_fil_ver5']   = $this->crear_tab_fil_ver_5($arr_cap)." ".$this->divs;
        
        //$arr_v_sap  = $this->generar_capacidades($des,$has,$arr_capacidades);
               
     }
     
     
     public function generar_ovs($des_ori,$has_ori,$id_planta,$id_vendedor){
         $des = strtotime($des_ori);
         $has = strtotime($has_ori);
         $res = array();
         
         while($des <= $has){
            $fecha = date("Y-m-d",$des);
            $id_tipo = 2; 
            $cont_ovs_con = count($this->get_ovs_cont(null,null,$id_vendedor,null,$id_planta,1,"ov.id_ovsap_old !=","",null,null,$fecha));
            $cont_ovs_pro = count($this->get_ovs_cont(null,null,$id_vendedor,null,$id_planta,2,null,null,"todas",null,$fecha))+$cont_ovs_con;
            
            $cont_ovs_nco = count($this->get_ovs_cont(null,null,$id_vendedor,null,$id_planta,2,null,null,null,null,$fecha));
            $cont_ovs_anu = count($this->get_ovs_cont(null,null,$id_vendedor,null,$id_planta,2,"ov.valido",0,"todas",null,$fecha));
        
            array_push($res, array(
                                            "fecha"     => date("d-m",$des), 
                                            "ovs_con"   => $cont_ovs_con,
                                            "ovs_pro"   => $cont_ovs_pro,
                                            "ovs_nco"   => $cont_ovs_nco,
                                            "ovs_anu"   => $cont_ovs_anu,
                                            "id_planta"     => $id_planta,
                                            "id_vendedor"   => $id_vendedor 
                                        )
                                );
           //get_ovs_cont($des=null,$has=null,$id_vendedor=null,$id_cliente=null,$id_planta=null,$id_tipo=null,$campo=null,$valor=null,$todas=null,$limite=null,$fecha=null){
            $des = mktime(date("H",$des),date("i",$des),date("s",$des),date("m",$des),date("d",$des)+1,date("Y",$des));
         }
         return $res;         
     }
     
     
     
     
     
     public function generar_capacidades($des,$has,$arr_capacidades,$id_planta){
         
         $des = strtotime($des);
         $has = strtotime($has);
         
         $res = array();
                  
         while($des <= $has){
             $cap_actual = 0;
             if(count($arr_capacidades)>0){
                foreach($arr_capacidades as $valor){
                    $fecha_cap = strtotime($valor->fecha_inicio);
                    if($fecha_cap<=$des){
                        $cap_actual = $valor->capacidad;
                    }else{
                        break;
                    }
                } 
             }   
             $cap_dia   = $cap_actual*$this->hrs_disp;
             $suma_pro  = $this->calcular_ov($des,2,$id_planta);
             $suma_con  = $this->calcular_ov($des,1,$id_planta);
             $cap_dis   = $cap_dia-($suma_con+$suma_pro); 
             
             
             array_push(    $res, array("fecha"         => date("d-m",$des), 
                                        "capacidad"     => $cap_actual, 
                                        "cap_diaria"    => $cap_dia, 
                                        "vigas_pro"     => $suma_pro,
                                        "vigas_con"     => $suma_con,
                                        "cap_dis"       => $cap_dis
                                        )
                        ); 
             $des = mktime(date("H",$des),date("i",$des),date("s",$des),date("m",$des),date("d",$des)+1,date("Y",$des));
         }
         //var_dump($res);
         return $res;         
     }
     
     public function calcular_ov($dia,$id_tipo,$id_planta){
        $obj_ov_c   = new Ov_class();
        $tabla      = $obj_ov_c->tabla;
        
        $obj_ov_c->campos       = "reserva.capacidad";
        $obj_ov_c->id_planta    = $id_planta; 
        $obj_ov_c->fecha        = date("Y-m-d",$dia);
        
        $obj_ov_c->reporte(null, null, $id_tipo);
        
        $arreglo = $obj_ov_c->arreglo;
        
        $obj_crud = new Crud_model();
        $obj_crud->tabla = $tabla;
        $registros = $obj_crud->get_registros($arreglo,"sum");
        
        $suma = 0;
        foreach ($registros as $valor) {
            if(isset($valor['capacidad'])){
               $suma += $valor['capacidad'];
            }
        }
        return $suma;
     }
     
     
     
     
     public function get_capacidades($id_planta,$desde,$hasta){        
            $registros = array();
            $obj_capacidad_c  = new Capacidad_class();
            
            
            $obj_capacidad_c->get_capacidades_dh($desde, $id_planta);            
            $arreglo    = $obj_capacidad_c->arreglo; 
            
            $obj_crud       = new Crud_model();   
            $obj_crud->tabla = $obj_capacidad_c->tabla;            
            
            $registros = $obj_crud->get_registros($arreglo);
            
            $i = 0;
            $arr_obj= null;
            foreach ($registros as $valor) {
               $arr_obj[$i] = new Capacidad_model();             
               $arr_obj[$i]->set_registro_arr($valor);
               $i++;
            }
            
            $obj_capacidad_c->get_capacidades_dh($desde, $id_planta, $hasta);            
            $arreglo    = $obj_capacidad_c->arreglo; 
            
            $registros = $obj_crud->get_registros($arreglo);
            
            $i = 1;
            //$arr_obj= null;
            foreach ($registros as $valor) {
               $arr_obj[$i] = new Capacidad_model();             
               $arr_obj[$i]->set_registro_arr($valor);
               $i++;
            } 
            //var_dump($arr_obj);
            return $arr_obj;              
    }
     
     
     public function generar_filtros(){
        $tipo = "Usuario_model";
        $this->arr_vendedores   = $this->conv_obj($tipo, $this->get_vendedores());
                
        
        $this->arr_plantas      = $this->get_plantas();
        $this->arr_clientes     = $this->get_clientes();
                
        $this->data['menu_plantas1']         = $this->crear_combo_plantas($this->arr_plantas,1);
        $this->data['menu_plantas2']         = $this->crear_combo_plantas($this->arr_plantas,2);
        $this->data['menu_plantas3']         = $this->crear_combo_plantas($this->arr_plantas,3);
        $this->data['menu_plantas5']         = $this->crear_combo_plantas($this->arr_plantas,5);
        
        //$this->data['combo_tipos_ovs']      = $this->crear_combo_tipos_ovs($this->arr_tipos_ovs);
        $this->data['txt_vendedor']         = $this->crear_txt("vendedor");
        $this->data['txt_vendedor5']         = $this->crear_txt("vendedor5");
        $this->data['txt_cliente']           = $this->crear_txt("cliente");
        
        $this->data['js_clientes']          = $this->crear_js_clientes($this->arr_clientes);
        $this->data['js_vendedores']        = $this->crear_js_vendedores($this->arr_vendedores);
        
        
        $fecha_hoy = date("d/m/Y H:i");
        $this->data['des'] = $this->crear_campo_fecha("des_prog_picker","des_prog_input1",$fecha_hoy);
        $this->data['has'] = $this->crear_campo_fecha("has_prog_picker","has_prog_input1",$fecha_hoy);
        $this->data['des2'] = $this->crear_campo_fecha("des_prog_picker","des_prog_input2",$fecha_hoy);
        $this->data['has2'] = $this->crear_campo_fecha("has_prog_picker","has_prog_input2",$fecha_hoy);
        $this->data['des3'] = $this->crear_campo_fecha("des_prog_picker","des_prog_input3",$fecha_hoy);
        $this->data['has3'] = $this->crear_campo_fecha("has_prog_picker","has_prog_input3",$fecha_hoy);
        $this->data['des5'] = $this->crear_campo_fecha("des_prog_picker","des_prog_input5",$fecha_hoy);
        $this->data['has5'] = $this->crear_campo_fecha("has_prog_picker","has_prog_input5",$fecha_hoy);
        
        $this->data['tabla_fil_hor1']       = $this->crear_tab_fil_hor(1);
        $this->data['tabla_fil_hor2']       = $this->crear_tab_fil_hor(2);
        $this->data['tabla_fil_hor3']       = $this->crear_tab_fil_hor(3);
        $this->data['tabla_fil_hor5']       = $this->crear_tab_fil_hor(5);
     }
     
     
     public function get_ovs_cont($des=null,$has=null,$id_vendedor=null,$id_cliente=null,$id_planta=null,$id_tipo=null,$campo=null,$valor=null,$todas=null,$limite=null,$fecha=null){
        $obj_ov_c   = new Ov_class();
        $obj_ov_c->campos = "ov.id_ov";        
        $obj_ov_c->id_planta    = $id_planta;
        $obj_ov_c->id_tipo      = $id_tipo;
        $obj_ov_c->desde        = $des;
        $obj_ov_c->hasta        = $has;
        $obj_ov_c->limite       = $limite;
        $obj_ov_c->fecha        = $fecha;
        
        $obj_ov_c->grafico($campo, $valor,$id_vendedor,$id_cliente);
        
        //var_dump($obj_ov_c->arreglo);
        
        $obj_crud = new Crud_model();
        $obj_crud->tabla = $obj_ov_c->tabla;
        if($todas=="todas"){
            return $obj_crud->get_registros($obj_ov_c->arreglo,null,"full");           
        }else{
            return $obj_crud->get_registros($obj_ov_c->arreglo);
        }    
     }        
     
     
     
     public function get_vendedores($id_vendedor=null,$id_planta=null){
        $obj_usuario_planta_c      = new Usuario_planta_class();
                
        $obj_usuario_planta_c->get_registro_campo_full("id_usuario", $id_vendedor, $id_planta);
        
        $obj_crud = new Crud_model();
        $obj_crud->tabla = $obj_usuario_planta_c->tabla;
        return $obj_crud->get_registros($obj_usuario_planta_c->arreglo);          
     }   
     
     
     public function conv_obj($tipo,$registros){
        $i=0;
        
        foreach ($registros as $valor) {
           
            switch ($tipo){
                case "Usuario_class": $obj = new Usuario_class(); break;
                case "Usuario_model": $obj = new Usuario_model(); break;
            }
             
            $arr_obj[$i] = $obj;
            
            $arr_obj[$i]->set_registro_arr($valor);
            $i++;
            
        }    
        return $arr_obj;
     }
     
     
     public function get_tipos_ovs($id_tipo = null){   
        $obj_tipo_ov_c   = new Tipo_ov_class();
        $tabla      = $obj_tipo_ov_c->tabla;
        
        $obj_tipo_ov_c->get_registro_campo("id_tipo", $id_tipo);
        $arreglo = $obj_tipo_ov_c->arreglo;
        
        $obj_crud = new Crud_model();
        $obj_crud->tabla = $tabla;
        $registros = $obj_crud->get_registros($arreglo);
        $i = 0;             
        foreach ($registros as $valor) {
            $arr_obj[$i] = new Tipo_ov_model();             
            $arr_obj[$i]->set_registro_arr($valor);
            $i++;
        }        
        return $arr_obj;  
    }
     
    /*
     public function get_ovs($id_ov = null,$grupo=null){   
        $obj_ov_c   = new Ov_class();
        $tabla      = $obj_ov_c->tabla;
        
        $obj_ov_c->grupo = $grupo;
        
        $obj_ov_c->get_registro_campo("id_ov", $id_ov);
        $arreglo = $obj_ov_c->arreglo;
        
        //var_dump($arreglo);
        
        $obj_crud = new Crud_model();
        $obj_crud->tabla = $tabla;
        $registros = $obj_crud->get_registros($arreglo);
        $i = 0;             
        foreach ($registros as $valor) {
            $arr_obj[$i] = new Ov_model();             
            $arr_obj[$i]->set_registro_arr($valor);
            $i++;
        } 
        //var_dump($arr_obj);
        return $arr_obj;  
    }
    */  
    public function get_plantas($id_planta=null){ 
            $arr_obj = array();
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
   
   // Obtiene clientes de la tabla clientes 
   /*
    public function get_clientes($id_cliente=null){ 
            $arr_obj = array();
            $obj_clientes_c  = new Cliente_class();
            $obj_clientes_c->get_registro_campo("id_cliente", $id_cliente);
            $arreglo    = $obj_clientes_c->arreglo;            
            
            $obj_crud       = new Crud_model();   
            $obj_crud->tabla = $obj_clientes_c->tabla;            
            
            $registros = $obj_crud->get_registros($arreglo);
            $i = 0;             
            foreach ($registros as $valor) {
               $arr_obj[$i] = new Cliente_model();             
               $arr_obj[$i]->set_registro_arr($valor);
               $i++;
            } 
            //var_dump($arr_obj);
            return $arr_obj;              
    }
    */
    
    // Obtiene clientes de las ovs
    
    public function get_clientes(){       
        $obj_ov_c   = new Ov_class();
        $tabla      = $obj_ov_c->tabla;
        $arr_obj = array();
        
        //$obj_ov_c->fecha = date("Y-m-d H:i");
        $obj_ov_c->get_clientes($this->id_planta);
        $arreglo    = $obj_ov_c->arreglo;
        
        $obj_crud = new Crud_model();
        $obj_crud->tabla = $tabla;
        
        $registros = $obj_crud->get_registros($arreglo);
            $i = 0;             
            foreach ($registros as $valor) {
               $arr_obj[$i] = new Ov_model();             
               $arr_obj[$i]->set_registro_arr($valor);
               $i++;
            } 
       //var_dump($arr_obj);
       return $arr_obj;     
    }
    
     
     public function crear_tab_fil_ver($cod=1){
        
         $this->table->set_heading(array('Datos','Valores','Porcentaje'));
         
                $dato[0] = "Ordenes provisorias creadas";
                $dato[1] = "Ordenes provisorias confirmadas";
                $dato[2] = "Ordenes provisorias no confirmadas";
                $dato[3] = "Ordenes provisorias anuladas";

                $valor[0] = $this->cont_ovs_pro;
                $valor[1] = $this->cont_ovs_con;
                $valor[2] = $this->cont_ovs_nco;
                $valor[3] = $this->cont_ovs_anu; 
         
         
         $red= 1;
         
         if($this->cont_ovs_pro>0){
            $this->porc[0] = round(    ($this->cont_ovs_pro/$this->cont_ovs_pro) * 100,$red);
            $this->porc[1] = round(    ($this->cont_ovs_con/$this->cont_ovs_pro) * 100,$red);
            $this->porc[2] = round(    ($this->cont_ovs_nco/$this->cont_ovs_pro) * 100,$red);
            $this->porc[3] = round(    ($this->cont_ovs_anu/$this->cont_ovs_pro) * 100,$red);
         }else{
            $this->porc[0] = 0;
            $this->porc[1] = 0;
            $this->porc[2] = 0;
            $this->porc[3] = 0; 
         }
         
         
           
         $cell[0] = array('data' => $this->porc[0], 'id' => 'v_0_'.$cod, 'data-valor' => $this->porc[0]);
         $cell[1] = array('data' => $this->porc[1], 'id' => 'v_1_'.$cod, 'data-valor' => $this->porc[1]);
         $cell[2] = array('data' => $this->porc[2], 'id' => 'v_2_'.$cod, 'data-valor' => $this->porc[2]);
         $cell[3] = array('data' => $this->porc[3], 'id' => 'v_3_'.$cod, 'data-valor' => $this->porc[3]);
         
         
         for($i=0;$i<count($dato);$i++){
            $this->table->add_row(array($dato[$i],$valor[$i],  $cell[$i]));
         }
         
         
         
         return $this->table->generate();
        
     }
     
     
     
     public function conv_arr($arreglo){
         $cad   = "";
         
         foreach($arreglo as $valor){
             //if($valor!=""){
             $cad .= $valor."|";
             //}             
         }
         $cad = substr($cad, 0, -1);
         $cad .= ""; 
         return $cad;
     }
     
     
     public function crear_tab_fil_ver_3($arr_cap){
         /*
          $res, array(                  "fecha"         => date("d-m-Y",$des), 
                                        "capacidad"     => $cap_actual, 
                                        "cap_diaria"    => $cap_dia, 
                                        "vigas_pro"     => $suma_pro,
                                        "vigas_con"     => $suma_con,
                                        "cap_dis"       => $cap_dis
                                        )
         */
         $cod = 3;
         
         $this->divs = "";
         
         $arr_head[0] = "";
         $i=1;
         foreach($arr_cap as $valor){
            //$cell = array('data' => $valor['fecha'], 'id' => 'c'.$fila.'_'.($i-1).'_'.$cod, 'data-valor' => $valor['cap_diaria']); 
            $arr_head[$i] = $valor['fecha'];
            $i++;
         }
         //$this->graf3_eje_x = $arr_head;
        
         $this->table->set_heading($arr_head);
         
         //for($i=1;$i<count($arr_head);$i++){
         //   $arr_nvo[$i-1] = $arr_head[$i]; 
         //}
         //var_dump($arr_nvo);
         //return;
         
         $this->divs .= "<div id='arr_col' data-col=\"".$this->conv_arr($arr_head)."\"></div>";
         //$this->divs .= "<div id='arr_col2' data-col=\"".$this->conv_arr($arr_nvo)."\"></div>";
         
         $dato[0] = "Capacidad total";
         $dato[1] = "Vigas SAP";
         $dato[2] = "Vigas provisorias";
         $dato[3] = "Capacidad disponible";
         
         $this->divs .= "<div id='arr_ley' data-col=\"".$this->conv_arr($dato)."\"></div>";
         
         
         $arr[0] = $dato[0];
         $i=1;
         $fila = 0;
         foreach($arr_cap as $valor){
             //$cell = array('data' => $valor['cap_diaria'], 'id' => 'v_'.$fila.'_'.($i-1).'_'.$cod, 'data-valor' => $valor['cap_diaria']);
             $arr[$i] =  $valor['cap_diaria'];
             $i++;
         }
        
         $this->table->add_row($arr);  
         $arr[0]="";
         $this->divs .= "<div id='arr_cap_dia' data-col=\"".$this->conv_arr($arr)."\"></div>";
         
         $arr[0] = $dato[1];
         $i=1;
         $fila = 1;
         foreach($arr_cap as $valor){
             //$cell = array('data' => $valor['vigas_con'], 'id' => 'v_'.$fila.'_'.($i-1).'_'.$cod, 'data-valor' => $valor['vigas_con']);
             $arr[$i] =  $valor['vigas_con'];
             $i++;
         }
         $this->table->add_row($arr); 
         $arr[0]="";
         $this->divs .= "<div id='arr_vig_con' data-col=\"".$this->conv_arr($arr)."\"></div>";
         
         $arr[0] = $dato[2];
         $i=1;
         $fila = 2;
         foreach($arr_cap as $valor){
             //$cell = array('data' => $valor['vigas_pro'], 'id' => 'v_'.$fila.'_'.($i-1).'_'.$cod, 'data-valor' => $valor['vigas_pro']);
             $arr[$i] =  $valor['vigas_pro'];
             $i++;
         }
         $this->table->add_row($arr); 
         $arr[0]="";
         $this->divs .= "<div id='arr_vig_pro' data-col=\"".$this->conv_arr($arr)."\"></div>";
         
         $arr[0] = $dato[3];
         $i=1;
         $fila = 3;
         foreach($arr_cap as $valor){
             //$cell = array('data' => $valor['cap_dis'], 'id' => 'v_'.$fila.'_'.($i-1).'_'.$cod, 'data-valor' => $valor['cap_dis']);
             $arr[$i] =  $valor['cap_dis'];
             $i++;
         }
         
         $this->table->add_row($arr);
         $arr[0]="";
         $this->divs .= "<div style='disp' id='arr_cap_dis' data-col=\"".$this->conv_arr($arr)."\"></div>";
         
         return $this->table->generate();
     }
     
     
     
     
     public function crear_tab_fil_ver_5($arr_ov){
         /*
         array_push($res, array(
                                            "fecha"     => $fecha,
                                            "ovs_con"   => $cont_ovs_con,
                                            "ovs_pro"   => $cont_ovs_pro,
                                            "ovs_nco"   => $cont_ovs_nco,
                                            "ovs_anu"   => $cont_ovs_anu,
                                            "id_planta"     => $id_planta,
                                            "id_vendedor"   => $id_vendedor 
                                        )
                                );
         */
         $cod = 5;
         
         //var_dump($arr_ov);
         
         $this->divs = "";
         
         $arr_head[0] = "";
         $i=1;
         foreach($arr_ov as $valor){
             
            if(isset($valor['fecha'])){
                $arr_head[$i] = $valor['fecha'];
            } 
            $i++;
         }
         
         
         $this->table->set_heading($arr_head);
                  
         $this->divs .= "<div id='arr_col5' data-col=\"".$this->conv_arr($arr_head)."\"></div>";
         //$this->divs .= "<div id='arr_col2' data-col=\"".$this->conv_arr($arr_nvo)."\"></div>";
         
         $dato[0] = "Provisorias creadas";
         $dato[1] = "Provisorias confirmadas";
         $dato[2] = "Provisorias anuladas";
         $dato[3] = "No confirmadas";
         
         $this->divs .= "<div id='arr_ley5' data-col=\"".$this->conv_arr($dato)."\"></div>";
         
         
         $arr[0] = $dato[0];
         $i=1;
         $fila = 0;
         foreach($arr_ov as $valor){
             //$cell = array('data' => $valor['cap_diaria'], 'id' => 'v_'.$fila.'_'.($i-1).'_'.$cod, 'data-valor' => $valor['cap_diaria']);
            if(isset($valor['ovs_pro'])){
                $arr[$i] =  $valor['ovs_pro'];
            }    
             $i++;
         }
        
         $this->table->add_row($arr);  
         $arr[0]="";
         $this->divs .= "<div id='arr_ovs_pro' data-col=\"".$this->conv_arr($arr)."\"></div>";
         
         $arr[0] = $dato[1];
         $i=1;
         $fila = 1;
         foreach($arr_ov as $valor){
             //$cell = array('data' => $valor['vigas_con'], 'id' => 'v_'.$fila.'_'.($i-1).'_'.$cod, 'data-valor' => $valor['vigas_con']);
             if(isset($valor['ovs_con'])){
                $arr[$i] =  $valor['ovs_con'];
             }   
             $i++;
         }
         $this->table->add_row($arr); 
         $arr[0]="";
         $this->divs .= "<div id='arr_ovs_con' data-col=\"".$this->conv_arr($arr)."\"></div>";
         
         $arr[0] = $dato[2];
         $i=1;
         $fila = 2;
         foreach($arr_ov as $valor){
             //$cell = array('data' => $valor['vigas_pro'], 'id' => 'v_'.$fila.'_'.($i-1).'_'.$cod, 'data-valor' => $valor['vigas_pro']);
             if(isset($valor['ovs_anu'])){
                $arr[$i] =  $valor['ovs_anu'];
             }   
             $i++;
         }
         $this->table->add_row($arr); 
         $arr[0]="";
         $this->divs .= "<div id='arr_ovs_anu' data-col=\"".$this->conv_arr($arr)."\"></div>";
         
         $arr[0] = $dato[3];
         $i=1;
         $fila = 3;
         foreach($arr_ov as $valor){
             //$cell = array('data' => $valor['cap_dis'], 'id' => 'v_'.$fila.'_'.($i-1).'_'.$cod, 'data-valor' => $valor['cap_dis']);
             if(isset($valor['ovs_nco'])){
                $arr[$i] =  $valor['ovs_nco'];
             }   
             $i++;
         }
         
         $this->table->add_row($arr);
         $arr[0]="";
         $this->divs .= "<div style='disp' id='arr_ovs_nco' data-col=\"".$this->conv_arr($arr)."\"></div>";
         
         return $this->table->generate();
     }
     
     
     
     
     
     
     
     
     public function crear_tab_fil_hor($tipo=1){
         
         $btn_actualizar = '<a class="btn btn-success btn-small pull-right" onclick="act_info('.$tipo.');" href="javascript:void(0);" id="btn_generar'.$tipo.'" style="margin-right: 5px;">Actualizar</a>';
         switch($tipo){
            default:
                
                $this->table->set_heading(array('Planta','Vendedor','Desde','Hasta',''));
                $this->table->add_row(array($this->data['menu_plantas'.$tipo],$this->data['txt_vendedor'],$this->data['des'],$this->data['has'],$btn_actualizar));
                break;
            case "2":
                
                $this->table->set_heading(array('Planta','Cliente','Desde','Hasta',''));
                $this->table->add_row(array($this->data['menu_plantas'.$tipo],$this->data['txt_cliente'],$this->data['des'.$tipo],$this->data['has'.$tipo],$btn_actualizar));
                break;
            
            case "3":
                $btn_actualizar = '<a class="btn btn-success btn-small pull-right" onclick="act_info3();" href="javascript:void(0);" id="btn_generar'.$tipo.'" style="margin-right: 5px;">Actualizar</a>';
                $this->table->set_heading(array('Planta','Desde','Hasta',''));
                $this->table->add_row(array($this->data['menu_plantas'.$tipo],$this->data['des'.$tipo],$this->data['has'.$tipo],$btn_actualizar));
                break;
            
            case "5":
                $btn_actualizar = '<a class="btn btn-success btn-small pull-right" onclick="act_info5();" href="javascript:void(0);" id="btn_generar'.$tipo.'" style="margin-right: 5px;">Actualizar</a>';
                $this->table->set_heading(array('Planta','Vendedor','Desde','Hasta',''));
                $this->table->add_row(array($this->data['menu_plantas'.$tipo],$this->data['txt_vendedor'.$tipo],$this->data['des'.$tipo],$this->data['has'.$tipo],$btn_actualizar));
                break;
            
         }
         return $this->table->generate();
     }
     
    public function revisar_permisos($obj_acceso,$arr_permisos,$id_seccion,$criterio){
        //$resultado = false;
        $obj_acceso->id_seccion     = $id_seccion; 
        $obj_acceso->criterio       = $criterio;
        $obj_acceso->arr_permisos   = $arr_permisos;
        
        return $obj_acceso->verificar_permisos();
    } 

    public function crear_combo_plantas($arr_plantas,$tipo=1)
    {          
       $html = "<center><select id='id_planta".$tipo."' name='id_planta".$tipo."' class='span3'>"; 
       $html .="<option value='all'>Todas</option>";
       foreach($arr_plantas as $valor){
           $html .="<option value='$valor->id_planta'>";
           $html .= $valor->nombre;
           $html .="</option>";
       }
       $html .= "</select></center>";
       return $html;
    }
    
    public function crear_combo_tipos_ovs($arr)
    {          
       $html = "<center><select id='id_tipo' name='id_tipo' class='span2'>"; 
       $html .="<option value='all'>Todos</option>";
       foreach($arr as $valor){
           $html .="<option value='$valor->id_tipo'>";
           $html .= $valor->nombre;
           $html .="</option>";
       }
       $html .= "</select></center>";
       return $html;
    }
    
    
    private function crear_js_clientes($arr){
        
        $html = "";
        foreach($arr as $valor){
           $html .="{value:'";
           $html .= $valor->cliente;
           $html .="',data:'".$valor->id_ov."'},";
        }
        $html = substr($html, 0, -1);
        return $html;
    }
    
    private function crear_js_vendedores($arr){
        
        $html = "";
        foreach($arr as $valor){
           $html .="{value:'";
           $html .= $valor->nombre." ".$valor->ape_paterno;
           //$html .= $valor->id_ovsap;
           $html .="',data:'".$valor->id_usuario."'},";
        }
        $html = substr($html, 0, -1);
        return $html;
    }
    
       
    
    public function crear_combo_clientes($arr)
    {  
        $html = '
            <center>
            <div id="searchfield">
            
            <input type="text" name="cliente" class="span3" id="cliente">
            </div></center>';
        
       /* 
       $html = "<center><select id='clientes' class='span3'>"; 
       foreach($arr as $valor){
           $html .="<option id='$valor->id_cliente'>";
           $html .= $valor->nombre;
           $html .="</option>";
       }
       $html .= "</select></center>";
        * 
        */
       return $html;
    }
    
     private function crear_txt($nombre)
    {  
       $html = '
            <center>
            <div id="searchfield_'.$nombre.'">
            <input type="text" name="id_'.$nombre.'" class="span2" id="id_'.$nombre.'">
            </div></center>';  
       
       return $html;
    }
    
    
    
    private function crear_campo_fecha($id_picker,$id_input,$valor){
        $html = '<center>
                    <div id="'.$id_picker.'" class="time_picker input-append date">
                        <input class="span2" type="text" name="'.$id_input.'" id="'.$id_input.'" value="'.$valor.'"></input>
                        <span class="add-on">
                          <i data-time-icon="icon-time" data-date-icon="icon-calendar"></i>
                        </span>
                    </div></center>  
                    ';
        return $html;
    }
    
    public function format_fecha_TS($fecha){
        
        $arr_full   = explode(" ", $fecha);
        
        $arr_fecha  = explode("/", $arr_full[0]);
        
        return $arr_fecha[2]."-".$arr_fecha[1]."-".$arr_fecha[0]." ".$arr_full[1];
     }
     
    /*
    
    private function crear_combo_ids_sap($arr)
    {  
       $html = '
            <center>
            <div id="searchfield3">
            <input type="text" name="ov" class="span2" id="ov">
            </div></center>';  
       
       return $html;
    } 
    
    public function generar_reporte($crear=null){
        
        
        $tmpl2 = array ('table_open' => '<table class="table table-bordered table-hover table-condensed table-striped tabla_resultados" border="1" >');
        $this->table->set_template($tmpl2);
        
        $id_planta  = $this->input->post('id_planta');
        $id_tipo    = $this->input->post('id_tipo');
        $cliente    = $this->input->post('cliente');
        $ov         = $this->input->post('ov'); 
        $id_ov      = $this->input->post('id_ov'); 
        
        //echo $id_tipo;
        //return;
        
        if (    ($this->input->post('des_prog_input') != "") && ($this->input->post('des_crea_input') != "") && ($this->input->post('des_entr_input') != "")){
        
            $fech_pro_des = $this->format_fecha_TS($this->input->post('des_prog_input'));
            $fech_pro_has = $this->format_fecha_TS($this->input->post('has_prog_input'));
            $fech_cre_des = $this->format_fecha_TS($this->input->post('des_crea_input'));
            $fech_cre_has = $this->format_fecha_TS($this->input->post('has_crea_input'));        
            $fech_ent_des = $this->format_fecha_TS($this->input->post('des_entr_input'));
            $fech_ent_has = $this->format_fecha_TS($this->input->post('has_entr_input'));    


            if($id_planta == "all") { $id_planta    = null; }
            if($id_tipo == "all")   { $id_tipo      = null; }
            if($cliente == "")      { $cliente      = null; }
            if($id_ov == "")        { $id_ov        = null; }

            if($fech_pro_des == $fech_pro_has){
                $fech_pro_des = null;
                $fech_pro_has = null;
            }

            if($fech_cre_des == $fech_cre_has){
                $fech_cre_des = null;
                $fech_cre_has = null;
            }

            if($fech_ent_des == $fech_ent_has){
                $fech_ent_des = null;
                $fech_ent_has = null;
            }

            //$fech_pro_des = "2013-11-09";


            $arr_fechas = array(    "prog"  => array("desde" => $fech_pro_des, "hasta" => $fech_pro_has),
                                    "crea"  => array("desde" => $fech_cre_des, "hasta" => $fech_cre_has),
                                    "entr"  => array("desde" => $fech_ent_des, "hasta" => $fech_ent_has)    
                                );


            //$id_ov      = 109;
            //$ov = "0";

            $obj_ov_c = new Ov_class();

            $obj_ov_c->id_planta = $id_planta;
            $obj_ov_c->reporte($id_ov,$cliente,$id_tipo,$ov,$arr_fechas);

            //var_dump($obj_ov_c->arreglo);

            $obj_crud = new Crud_model();
            $obj_crud->tabla = $obj_ov_c->tabla;

            $reg = $obj_crud->get_registros($obj_ov_c->arreglo);
            if(count($reg)>0){
                $tabla = $this->crear_tab_res($reg);
            }else{
                $tabla = "No existen reservas asociadas";
            }     




            if($crear==null){
                echo $tabla;
            }else{        
                $obj_pdf = $this->set_pdf();  
                $this->agrega_cont_pdf($obj_pdf, $tabla);
                $nombre_pdf="reporte_".date("YdmHis").".pdf";
                $this->crear_pdf($obj_pdf,$nombre_pdf);
            }
            
            
        }
        
        
    }
    
    
    
    
    
    
    public function crear_tab_res($reg){
        
       
        
       $this->table->set_heading(array( 'NRO.',
                                        'FECHA PROGRAMACION',
                                        'NRO. PEDIDO',
                                        'POS.', 
                                        'TIPO PEDIDO', 
                                        'FECHA CREACION ORDEN', 
                                        'FECHA ENTREGA', 
                                        'CODIGO CLIENTE', 
                                        'CLIENTE',
                                        'CODIGO MATERIAL',
                                        'MATERIAL',
                                        'KILOS',
                                        'VIGAS',
                                        'PIEZAS'));
        $i=1;
        
        foreach($reg as $valor){
            
            if($valor['id_tipo'] == 1){ 
                $id_pedido =  $valor['id_ovsap'];
            }else{
                $id_pedido =  "p".$valor['id_ov'];
            }
        
            
            $this->table->add_row(array($i,
                                        $valor['res_fecha'],
                                        //$valor['id_ovsap'],
                                        $id_pedido,
                                        $valor['posicion'],
                                        $valor['tipo_nombre'],
                                        $valor['fecha_creacion'],
                                        $valor['fecha_entrega'],
                                        $valor['cli_id'],
                                        $valor['cli_nombre'],
                                        $valor['id_material'],
                                        $valor['material'],
                                        $valor['res_kilos'],
                                        $valor['res_vigas'],
                                        $valor['res_piezas']                                        
                                        )
                                    );
            $i++;
        }
       
        return $this->table->generate(); 
       
    }
    
    
    public function set_pdf(){
        // create new PDF document
        
        $orientacion    = "L";
        $titulo         = "Reporte de Planificación Inteligente";
        $subtitulo      = "BBOSCH";
        $autor          = 'Planificación Inteligente'; 
        $encabezado     = $subtitulo;
        
        $tamano         = 7;   
        
        
        $pdf = new TCPDF($orientacion, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

        // set document information
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor($autor);
        $pdf->SetTitle($titulo);
        $pdf->SetSubject($subtitulo);
        $pdf->SetKeywords('TCPDF, PDF, example, test, guide');

        // set default header data
        
        
        
        
        $pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, $titulo, $encabezado);

        // set header and footer fonts
        $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
        $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

        // set default monospaced font
        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

        // set margins
        $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
        $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
        $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

        // set auto page breaks
        $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
        $pdf->SetFontSize($tamano);

        // set image scale factor
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

        // set some language-dependent strings (optional)
        //if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
        //    require_once(dirname(__FILE__).'/lang/eng.php');
        //    $pdf->setLanguageArray($l);
        //}
        
        return $pdf;
    }
    
    
    public function agrega_cont_pdf($pdf,$html){
        // add a page
        $pdf->AddPage();
        // output the HTML content
        $pdf->writeHTML($html, true, false, true, false, '');
        // reset pointer to the last page
        $pdf->lastPage();

        // - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
    }
    
    public function crear_pdf($pdf,$nombre){        
        //Close and output PDF document
        $pdf->Output($nombre, 'I');
    }
    */
}
/*
*end modules/login/controllers/index.php
*/  