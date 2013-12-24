<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
 
class Calendario extends MX_Controller
{
    
    private $data;
    private $fecha_actual;
    private $fecha_hasta;
    
    private $obj_dia;
    //private $obj_capacidad;
    
    private $id_usuario;
    private $id_planta;
    private $arr_tipos_blo;
    
    public $arr_reservas;
    public $arr_ovs;
    public $arr_capacidades;
    public $arr_bloqueados;
    
    public $num_dias;
    public $hra_num_inicio;
    
    public $modo;
    

    public function __construct()
    {        
        parent::__construct();
        $this->load->helper('url');
        $this->load->database();
        
        $this->load->model('capacidad_model');
        $this->load->model('ov_model');
        $this->load->model('reserva_model');
        $this->load->model('tipo_bloque_model');
        $this->load->model('bloqueado_model');        
        $this->load->model('crud_model');
        $this->load->model('mensaje_model');
        
        
        $this->load->library('bloque_class');
        $this->load->library('dia_class');
        $this->load->library('session');
        $this->load->library('ov_class');
        
        
        $this->num_dias         = 14;
        $this->hra_num_inicio   = $this->session->userdata('horas_ini');
        $this->arr_reservas     = array();
        $this->arr_ovs          = array();
        $this->arr_capacidades  = array();
        $this->arr_bloqueados   = array();
        
        $this->id_planta        = $this->session->userdata('id_planta');        
        $this->id_usuario       = $this->session->userdata('id_usuario');  
        
        //$this->obj_capacidad    = $this->set_capacidad();
        $this->data['mensaje'] = "";
        $this->obj_dia          = new Dia_class();
        
        $this->arr_tipos_blo    = array();
        
        $this->modo =   "user"; // user o admin
        
    }
    public function index($dias=null,$fecha=null)
    {
             
        $this->modo = $this->session->userdata('modo');
                
        $this->arr_tipos_blo = $this->get_tipos_blo();//$obj_tb->get_registro();
    
        if($fecha != null){
           $this->fecha_actual  = mktime(null,null,null,date("m",$fecha),date("d",$fecha)+$dias,date("Y",$fecha));  
        }else{
           $this->fecha_actual  = mktime(null,null,null,date("m"),date("d"),date("Y")); 
        }
         
        $this->fecha_hasta      = mktime(null,null,null,date("m",$this->fecha_actual),date("d",$this->fecha_actual)+$this->num_dias,date("Y",$this->fecha_actual));
        
        $this->get_capacidades();
        $this->get_ovs();
        $this->get_reservas();
        $this->get_bloqueados();
        
        
        $this->data['leyenda'] = $this->crear_leyenda();
        
                
        $this->crear_col_horas();
        $this->crear_dias();       
        
        $this->data['fecha']        = $this->fecha_actual;
        $this->data['fecha_format'] = date("d/m/Y",  $this->fecha_actual);
        
        $this->load->view('index',$this->data);
     }
     
     public function index_dma($d=null, $m=null, $a=null){
         try {
            if(    ($d!=null)  &&  ($m!=null)  &&  ($a!=null)    ){
                $d = intval($d);
                $m = intval($m);
                $a = intval($a);
                
                $fecha = mktime(null,null,null,$m,$d,$a);
                $this->index(0,$fecha);
            }
         }catch(Exception $ex){
                $this->index();
         }
         
     }

    
    public function crear_col_horas(){
            
        $this->obj_dia = new Dia_class;
        $this->obj_dia->set_encabezado("Horas");
        $this->obj_dia->clase_fondo = "cal_bloque cal_encabezado";
        $this->obj_dia->clase_marco = "row span1 cal_filas2 arriba";
        $this->obj_dia->generar_horas(); 
            
        $this->data["col_horas"] = $this->obj_dia->get_html("in");
    } 
    
    
    public function crear_dias(){
       $this->data["cols_dias"] = "";      
              
       for($i=0;$i<$this->num_dias;$i++){   
           $this->obj_dia = new Dia_class();
           
           $this->obj_dia->modo = $this->modo;
           
           $this->obj_dia->arr_tipos_blo = $this->arr_tipos_blo;
           
           $this->obj_dia->dia = mktime(null,null,null,date("m",$this->fecha_actual),date("d",$this->fecha_actual)+$i,date("Y",$this->fecha_actual));
            
           $this->obj_dia->cap_planta = $this->set_cap_planta($this->obj_dia->dia);
           
           
           $this->obj_dia->arr_bloqueados = $this->arr_bloqueados;
           
           //$this->obj_dia->arr_bloqueados = $arr_bloqueados;
           $this->obj_dia->set_reservas($this->arr_reservas);
           $this->obj_dia->set_capacidades($this->arr_capacidades);
           
            
           $this->obj_dia->set_encabezado($this->obj_dia->get_dia(date('w',  $this->obj_dia->dia))." ".date('d/m',  $this->obj_dia->dia)  );            
            
           $this->obj_dia->clase_fondo = "cal_bloque";
           
          
           $this->obj_dia->generar_dia();            
           $this->data["cols_dias"] .= $this->obj_dia->get_html("in");
       }   
    }
    
    public function revisar_reservas($dia_TS){
        $resultado      = array();
        $x              = 0;
        
        //for($i=0;$i<count($this->arr_reservas);$i++){
        foreach ($this->arr_reservas as $valor) {
            $fecha_res_com  = $valor->fecha;
            
            $dia_res = mktime(null,null,null,date("m",$fecha_res_com),date("d",$fecha_res_com),date("Y",$fecha_res_com));
            
            //echo $dia_res." ".$dia_TS." ".date("d/m/Y h:i", $dia_res)." ".date("d/m/Y h:i", $dia_TS)."<br />";
            if ($dia_res == $dia_TS){
                $resultado[$x] = new Reserva_model();
                $resultado[$x] = $valor;
                $x++;
            }   
        }
        //var_dump($resultado);
        return $resultado;
    }
    
    
     public function revisar_bloqueados($dia_TS){
        $resultado      = array();
        $x              = 0;
        
        
        $dia_TS = mktime(date("H",$dia_TS)+7,date("i",$dia_TS),null,date("m",$dia_TS),date("d",$dia_TS),date("Y",$dia_TS));
        foreach ($this->arr_bloqueados as $valor) {
            
            $fecha_res_com  = strtotime($valor->fecha_inicio);
            
            
            
            $dia_res = mktime(null,null,null,date("m",$fecha_res_com),date("d",$fecha_res_com),date("Y",$fecha_res_com));
            
            //echo $dia_res." ".$dia_TS." ".date("d/m/Y H:i", $dia_res)." ".date("d/m/Y H:i", $dia_TS)."<br />";
            if ($dia_res <= $dia_TS){
                $resultado[$x] = new Reserva_model();
                $resultado[$x] = $valor;
                $x++;
                //echo "<br/> entro <br/>";
            }   
        }
        return $resultado;
    }
    
    
    
    
    public function set_cap_planta($fecha_TS){
        $cap = 0;
        $fecha_for = date("Y-m-d H:i:s",$fecha_TS);

            foreach ($this->arr_capacidades as $valor) {
                if($fecha_for >= $valor->fecha_inicio){
                        $cap = $valor->capacidad; 
                        //echo "entro ".$valor->fecha_inicio."<br />";
                }
            }   
            //echo $fecha_for." ".$cap."<br/>";
        return $cap;
    }
    
    
    public function get_reservas(){
        
        $desde      = date("Y-m-d H:i", $this->fecha_actual);        
        $hasta      = date("Y-m-d H:i", $this->fecha_hasta);        
        
        $obj_reserva_c  = new Reserva_class();
        $tabla          = $obj_reserva_c->tabla;
        
        $obj_reserva_c->get_reservas_dh($desde, $hasta, $this->id_planta);
        $arreglo = $obj_reserva_c->arreglo;
        
        $obj_crud = new Crud_model();
        $obj_crud->tabla = $tabla;
        
        $lista_reservas = $obj_crud->get_registros($arreglo);
                
        for($i=0;$i<count($lista_reservas);$i++){
            $lista_reservas[$i]['fecha'] = strtotime($lista_reservas[$i]['fecha']);
            $this->arr_reservas[$i] = new Reserva_model();             
            $this->arr_reservas[$i]->set_registro_arr($lista_reservas[$i]);
        }
        
    }
    
    
    public function get_bloqueados(){
        
        $desde      = date("Y-m-d H:i", $this->fecha_actual);        
        $hasta      = date("Y-m-d H:i", $this->fecha_hasta);        
        
        $obj_bloqueado_c    = new Bloqueado_class();
        $tabla              = $obj_bloqueado_c->tabla;
        
        $obj_bloqueado_c->get_bloqueados_dh($desde, $hasta, $this->id_planta);
          
        
        $arreglo = $obj_bloqueado_c->arreglo;
        
        $obj_crud = new Crud_model();
        $obj_crud->tabla = $tabla;
        
        $registros = $obj_crud->get_registros($arreglo);
        
        $i=0;
        foreach ($registros as $valor) {
            //$lista_reservas[$i]['fecha'] = strtotime($lista_reservas[$i]['fecha']);
            $this->arr_bloqueados[$i] = new Bloqueado_model();             
            $this->arr_bloqueados[$i]->set_registro_arr($valor);
            $i++;
        }
        //var_dump($this->arr_bloqueados);
    }
    
    
    
    public function get_capacidades(){
        //$obj_capacidad  = new Capacidad_model();
        //$tabla          = $obj_capacidad->tabla;
        
        $desde          = date("Y-m-d H:i", $this->fecha_actual);        
        //$hasta          = date("Y-m-d H:i", $this->fecha_hasta);
        
        /*
        $condicion      = array(
                        'fecha_inicio <=' => $desde, 
                        'id_planta =' => $this->id_planta 
                        );
        
        $campos         = "fecha_inicio,capacidad";
        $orden          = "fecha_inicio";
        $direccion      = "desc";
        $limite         = 1;
        
        $arreglo = array(
                        "campos"    => $campos,
                        "condicion" => $condicion,
                        "orden"     => $orden,
                        "direccion" => $direccion,
                        "limite"    => $limite
                        );      
        */
        
        $obj_capacidad_c = new Capacidad_class();
        $tabla = $obj_capacidad_c->tabla;
        
        $obj_capacidad_c->get_capacidades_dh($desde, $this->id_planta);
        $arreglo = $obj_capacidad_c->arreglo;
        
        $obj_crud = new Crud_model();
        $obj_crud->tabla = $tabla;
        $lista_capacidades = $obj_crud->get_registros($arreglo);
       // $campos="*", $condicion = null, $arreglo = null, $operador=null, $orden = null, $direccion = "desc", $limite = null
        
        if(count($lista_capacidades)>0){
            $this->arr_capacidades[0] = new Capacidad_model();             
            $this->arr_capacidades[0]->set_registro_arr($lista_capacidades[0]);
        }
        
        $hasta      = date("Y-m-d H:i", $this->fecha_hasta);        
        $obj_capacidad_c->get_capacidades_dh($desde, $this->id_planta, $hasta);
        $arreglo = $obj_capacidad_c->arreglo;
        /*
        $condicion = array(
                        'fecha_inicio >=' => $desde, 
                        'fecha_inicio <=' => $hasta, 
                        'id_planta =' => $this->id_planta 
                        );
        $campos         = "fecha_inicio,capacidad";
        $orden          = "fecha_inicio";
        $direccion      = "asc";
                
        $arreglo = array(
                        "campos"    => $campos,
                        "condicion" => $condicion,
                        "orden"     => $orden,
                        "direccion" => $direccion
                        ); 
        */
        
        $lista_capacidades = $obj_crud->get_registros($arreglo);
        
        for($i=1;$i<=count($lista_capacidades);$i++){
            $this->arr_capacidades[$i] = new Capacidad_model();             
            $this->arr_capacidades[$i]->set_registro_arr($lista_capacidades[$i-1]);
        }
    }
    
    
    public function get_ovs(){        
        
        $obj_ov_c   = new Ov_class();
        $tabla      = $obj_ov_c->tabla;
        
        $obj_ov_c->get_ovs($this->id_planta);
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
    }
    
     public function get_tipos_blo(){        
        /*
        $obj_tipo_blo = new Tipo_bloque_model(); 
        $tabla  = $obj_tipo_blo->tabla;
        
        $campos         = "*";
        
        $arreglo = array(
                        "campos" => $campos
                        );
        */
         
        $obj_tipo_bloque_c = new Tipo_bloque_class();
        $tabla  = $obj_tipo_bloque_c->tabla;
        $obj_tipo_bloque_c->get_tipos_blo();        
        $arreglo = $obj_tipo_bloque_c->arreglo;
        
        $obj_crud = new Crud_model();
        $obj_crud->tabla = $tabla;
        $lista_ovs = $obj_crud->get_registros($arreglo);
        
        return $lista_ovs;        
    }
    
    public function desbloquear(){
        $res=0;
        $this->data['id_planta'] = $this->id_planta;
        $id_bloqueado = $this->input->post('id_bloc');
        
        $obj_bloqueados_c = new Bloqueado_class();
        $tabla = $obj_bloqueados_c->tabla;
        
        $arr_bloqueados = $this->get_bloqueado($id_bloqueado);
        
        if(count($arr_bloqueados)>0){
            
            $obj_crud = new Crud_model();
            $obj_crud->tabla = $tabla;
            
            $arreglo = array(
                                "condicion" => array("id_bloqueado" => $id_bloqueado)
                            );
            
            $res = $obj_crud->delete_registro($arreglo);
        }
        echo $res;
    }
    
    
    public function bloquear($fecha_TS = null){
        
        $hora = $this->input->post('hora');
        $id_bloqueo = $this->input->post('id_bloc');
        
        //echo "hora=".$hora;
        //echo "fecha_TS=".$fecha_TS;
        
        
        
        //$this->data['fecha'] = $hora;
        $this->data['id_planta'] = $this->id_planta;
        
        if($id_bloqueo!=null){
            $arr_bloqueados = $this->get_bloqueado($id_bloqueo);
            //var_dump($arr_bloqueados);
            $this->data['mensaje']      = $arr_bloqueados[0]->mensaje;
            $this->data['fecha_inicio'] = $arr_bloqueados[0]->fecha_inicio;
            $this->data['fecha_termino']= $arr_bloqueados[0]->fecha_termino;
            $this->load->view('admin_ver',$this->data);  
            return;
        }
        
        if($fecha_TS == null){
            $this->data['fecha_ini'] = date("d/m/Y H:i", $hora);             
            $this->data['fecha_ter'] = date("d/m/Y H:i", mktime(date("H",$hora)+1, date("i",$hora), null, date("m",$hora), date("d",$hora), date("Y",$hora))); 
            
            $this->load->view('admin',$this->data);        
        }else{
            $hora_ini   = $this->input->post('ini');
            $hora_fin   = $this->input->post('fin');
            
            $arr_fecha_ini = $this->parsea_fecha($hora_ini);
            $arr_fecha_fin = $this->parsea_fecha($hora_fin);
            
            $id_planta  = $this->input->post('id_planta');
            $id_usuario = $this->id_usuario;
            $mensaje    = $this->input->post('mensaje');
            
            $hora_ini = date("Y-m-d H:i",mktime($arr_fecha_ini[3], $arr_fecha_ini[4], null, $arr_fecha_ini[1], $arr_fecha_ini[0], $arr_fecha_ini[2]));            
            $hora_fin = date("Y-m-d H:i",mktime($arr_fecha_fin[3], $arr_fecha_fin[4], null, $arr_fecha_fin[1], $arr_fecha_fin[0], $arr_fecha_fin[2]));            
            
            //echo $hora_ini." ".$hora_fin;
            //return;
            
            $arreglo = array(
                            "fecha_inicio"      => $hora_ini,
                            "fecha_termino"     => $hora_fin,
                            "id_planta"         => $id_planta,
                            "id_usuario"        => $id_usuario,
                            "mensaje"           => $mensaje                            
                            );
            $obj_bloqueado = new Bloqueado_model();
            $obj_bloqueado->set_registro_arr($arreglo);
                        
            
            //echo $hora_ini." ".$hora_fin." ".$id_planta." ".$id_usuario." ".$mensaje;
            
            
            $res = $this->bloquear_confirmado($obj_bloqueado);
            echo $res;
        }
    }
    
    public function parsea_fecha($fecha){
        // Ej de formato: 18/10/2013 16:11
        $arr_seccion    = explode(" ", $fecha);        
        $arr_fecha      = explode("/", $arr_seccion[0]);        
        $arr_hora       = explode(":", $arr_seccion[1]);                
        array_push($arr_fecha, $arr_hora[0], $arr_hora[1]);
        return $arr_fecha;
    }
    
    public function bloquear_confirmado($obj_bloqueado){
        $res = false;
        
        $obj_mensaje_c = new Mensaje_class();
        $tabla = $obj_mensaje_c->tabla;
        
        $arreglo = array(
                    "id_usuario"    => $obj_bloqueado->id_usuario,
                    "mensaje"       => $obj_bloqueado->mensaje
                    );
        
        $obj_mensaje = new Mensaje_model();
        $obj_mensaje->set_registro_arr($arreglo);
        
        $data = $obj_mensaje->get_registros_arr();
        
        $obj_crud = new Crud_model();
        $obj_crud->tabla = $tabla;
        $res = $obj_crud->add_registro($data);
        if ($res){
            $id_mensaje = $obj_crud->id_ultimo;
            $obj_bloqueado_c = new Bloqueado_class();
            $tabla = $obj_bloqueado_c->tabla;  
            
            $obj_bloqueado->id_mensaje = $id_mensaje;
            
            $data = $obj_bloqueado->get_registros_arr();
                        
            
            $obj_crud = new Crud_model();
            $obj_crud->tabla = $tabla;
            $res = $obj_crud->add_registro($data);            
        }
        return $res;
    }
    
    public function get_mensaje($id_mensaje=null){
        
    }
    
    public function get_bloqueado($id_bloqueado=null){
        $obj_bloqueado_c = new Bloqueado_class();
        $tabla = $obj_bloqueado_c->tabla;  
        
        $campo = "id_bloqueado";
        $valor = $id_bloqueado;
        $obj_bloqueado_c->get_registro_campo($campo, $valor);
        
        $arreglo = $obj_bloqueado_c->arreglo;
        
        $obj_crud = new Crud_model();
        $obj_crud->tabla = $tabla;
        $registros = $obj_crud->get_registros($arreglo);
        
        $i=0;
        foreach($registros as $valor){
            $arr_obj[$i] = new Bloqueado_model();
            $arr_obj[$i]->set_registro_arr($valor);
            $i++;
        }
        return $arr_obj;
    }
    
    public function crear_leyenda(){
        $this->arr_tipos_blo;
        $html = "<div class='txt_leyenda'><b>Leyenda: </b></div>";
        
        //var_dump($this->arr_tipos_blo);
        
        foreach ($this->arr_tipos_blo as $valor) {
            $html .= "<div class='cuadro_leyenda' style='background-color:".$valor['color_fondo']."'></div>";
            $html .= "<div class='txt_leyenda'>";
            $html .= $valor['nombre']; 
            $html .= "</div>";
        }
        
        return $html;
        
        
    }
    
}
/*
*end modules/login/controllers/index.php
*/  