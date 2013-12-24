<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
 
class Reporte extends MX_Controller
{
    
    private $arr_plantas;
    private $arr_ovs;
    private $arr_clientes;
    private $arr_permisos;
    private $arr_tipos_ovs;
    private $data;
    private $tmpl;
    private $id_planta;
    private $id_usuario;
    
    public function __construct()
    {
        
        parent::__construct();
        
        $this->load->model('planta_model');
        $this->load->model('ov_model');
        $this->load->model('cliente_model');
        $this->load->model('crud_model');
        $this->load->model('tipo_ov_model');
        
        $this->load->library('session');
        //$this->load->library('libraries/tcpdf/TCPDF');
        
        $this->id_planta        = $this->session->userdata('id_planta');        
        $this->id_usuario       = $this->session->userdata('id_usuario');  
        
        $this->load->library('table');
        $this->tmpl = array ('table_open' => '<table class="table table-bordered table-hover table-condensed table-striped" >');
        
        
        //$obj_adm = new Administracion();
        
        
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
        
        
        $this->arr_plantas      = $this->get_plantas();
        $this->arr_ovs_sap      = $this->get_ovs(null,"ov.id_ovsap");        
        $this->arr_ovs          = $this->get_ovs();        
        $this->arr_clientes     = $this->get_clientes();
        $this->arr_tipos_ovs    = $this->get_tipos_ovs();
        
        
        $this->data['menu_plantas']         = $this->crear_combo_plantas($this->arr_plantas);
        $this->data['combo_tipos_ovs']      = $this->crear_combo_tipos_ovs($this->arr_tipos_ovs);
        $this->data['combo_clientes']       = $this->crear_combo_clientes($this->arr_clientes);
        $this->data['js_clientes']          = $this->crear_js_clientes($this->arr_clientes);
        $this->data['js_ovs']               = $this->crear_js_ovs($this->arr_ovs);
        $this->data['js_ovs_sap']           = $this->crear_js_ovs_sap($this->arr_ovs_sap);
        
        
        $this->data['combo_ovs']            = $this->crear_combo_ovs($this->arr_ovs);
        $this->data['combo_ids_sap']        = $this->crear_combo_ids_sap($this->arr_ovs_sap);
        
        
        $fecha_hoy = date("d/m/Y H:i");
        $this->data['des_prog'] = $this->crear_campo_fecha("des_prog_picker","des_prog_input",$fecha_hoy);
        $this->data['has_prog'] = $this->crear_campo_fecha("has_prog_picker","has_prog_input",$fecha_hoy);
        
        $this->data['des_crea'] = $this->crear_campo_fecha("des_crea_picker","des_crea_input",$fecha_hoy);
        $this->data['has_crea'] = $this->crear_campo_fecha("has_crea_picker","has_crea_input",$fecha_hoy);
        
        $this->data['des_entr'] = $this->crear_campo_fecha("des_entr_picker","des_entr_input",$fecha_hoy);
        $this->data['has_entr'] = $this->crear_campo_fecha("has_entr_picker","has_entr_input",$fecha_hoy);
        
        
        
        $this->data['tabla_fil_ver']    = $this->crear_tab_fil_ver();
        $this->data['tabla_fil_hor']    = $this->crear_tab_fil_hor();
        
        $this->load->view('reporte',  $this->data);
        
     }
     
     public function crear_texto_fecha(){
         $html      =   "";
         $html      .=  "";
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
     
     
     public function get_ovs($id_ov = null,$grupo=null){   
        $obj_ov_c   = new Ov_class();
        $tabla      = $obj_ov_c->tabla;
        
        $obj_ov_c->grupo = $grupo;
        
        $obj_ov_c->grupo = "ov.id_ov";
        $obj_ov_c->reporte();
        
        //$obj_ov_c->get_registro_campo("id_ov", $id_ov);
        $arreglo = $obj_ov_c->arreglo;
        
        //var_dump($arreglo);
        
        $obj_crud = new Crud_model();
        $obj_crud->tabla = $tabla;
        $registros = $obj_crud->get_registros($arreglo);
        $i = 0;    
        $arr_obj = array();
        foreach ($registros as $valor) {
            $arr_obj[$i] = new Ov_model();             
            $arr_obj[$i]->set_registro_arr($valor);
            $i++;
        } 
        //var_dump($arr_obj);
        return $arr_obj;  
    }
      
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
        
        
        //$obj_ov_c->fecha = date("Y-m-d H:i");
        //$obj_ov_c->get_clientes($this->id_planta);
        //$obj_ov_c->id_planta = $this->id_planta;
        $obj_ov_c->grupo = "ov.cliente";
        $obj_ov_c->reporte();
        
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
    
     
     public function crear_tab_fil_ver(){
        
         $this->table->set_heading(array('Filtro','Desde','Hasta'));
         $this->table->add_row(array("Fecha de programaci&oacute;n",$this->data['des_prog'],$this->data['has_prog']));
         $this->table->add_row(array("Fecha de creaci&oacute;n de orden",$this->data['des_crea'],$this->data['has_crea']));
         $this->table->add_row(array("Fecha entrega planificada",$this->data['des_entr'],$this->data['has_entr']));
         
         return $this->tabla_html = $this->table->generate();
        
     }
     
     public function crear_tab_fil_hor(){
         $this->table->set_heading(array('Planta','Tipo de orden','Cliente', 'Id. Sap', 'Orden de venta'));
         $this->table->add_row(array($this->data['menu_plantas'],$this->data['combo_tipos_ovs'],$this->data['combo_clientes'],$this->data['combo_ids_sap'],$this->data['combo_ovs']));
         
         return $this->tabla_html = $this->table->generate();
     }
     
    public function revisar_permisos($obj_acceso,$arr_permisos,$id_seccion,$criterio){
        //$resultado = false;
        $obj_acceso->id_seccion     = $id_seccion; 
        $obj_acceso->criterio       = $criterio;
        $obj_acceso->arr_permisos   = $arr_permisos;
        
        return $obj_acceso->verificar_permisos();
    } 

    public function crear_combo_plantas($arr_plantas)
    {          
       $html = "<center><select id='id_planta' name='id_planta' class='span3'>"; 
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
    
    private function crear_js_ovs($arr){
        
        $html = "";
        foreach($arr as $valor){
           $html .="{value:'";
           $html .= $valor->id_ov;
           //$html .= $valor->id_ovsap;
           $html .="',data:'".$valor->id_cliente."'},";
        }
        $html = substr($html, 0, -1);
        return $html;
    }
    
    private function crear_js_ovs_sap($arr){
        
        $html = "";
        foreach($arr as $valor){
           $html .="{value:'";
           //$html .= $valor->id_ov."(".$valor->id_ovsap.")";
           $html .= $valor->id_ovsap;
           $html .="',data:'".$valor->id_cliente."'},";
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
    
     private function crear_combo_ovs($arr)
    {  
       $html = '
            <center>
            <div id="searchfield2">
            <input type="text" name="id_ov" class="span2" id="id_ov">
            </div></center>';  
       
       return $html;
    }
    
    private function crear_combo_ids_sap($arr)
    {  
       $html = '
            <center>
            <div id="searchfield3">
            <input type="text" name="ov" class="span2" id="ov">
            </div></center>';  
       
       return $html;
    }
    
    private function crear_campo_fecha($id_picker,$id_input,$valor){
        $html = '<center>
                    <div id="'.$id_picker.'" class="time_picker input-append date">
                        <input type="text" name="'.$id_input.'" id="'.$id_input.'" value="'.$valor.'"></input>
                        <span class="add-on">
                          <i data-time-icon="icon-time" data-date-icon="icon-calendar"></i>
                        </span>
                    </div></center>  
                    ';
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
    
    
    public function format_fecha_TS($fecha){
        
        $arr_full   = explode(" ", $fecha);
        
        $arr_fecha  = explode("/", $arr_full[0]);
        
        return $arr_fecha[2]."-".$arr_fecha[1]."-".$arr_fecha[0]." ".$arr_full[1];
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
    
}
/*
*end modules/login/controllers/index.php
*/  