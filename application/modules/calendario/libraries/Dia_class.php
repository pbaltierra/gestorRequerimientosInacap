<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Dia_class extends MX_Controller
{
    Public $id;
    Public $encabezado;
    Public $clase_fondo;
    Public $clase_texto;
    Public $clase_enca;
    Public $num_bloques;
    Public $dia_num;
    Public $hra_num_inicio;
    Public $dia;
    Public $hra_step;
    
    Public $arr_bloques;
    Public $arr_dias;
    Public $arr_ovs;
    
    Public $html;
    Public $clase_marco;
    
    Public $arr_bloqueados;
    Public $arr_reservas;
    Public $arr_tipos_blo;
    Public $arr_capacidades;
    
    Public $cap_planta;
    Public $modo;
    Public $gmt;
        
    
    public function __construct()
    {        
        //parent::__construct();  
        parent::__construct();
        $this->load->library('session');
        
        
        $this->id           = 0;
        $this->encabezado   = new Bloque_class();
        $this->clase_fondo  = "cal_bloque span1";
        $this->clase_texto  = "";
        $this->clase_enca   = "cal_bloque span1 cal_encabezado";
        
        
        $this->hra_step         = 1;
        $this->hra_num_inicio   = $this->session->userdata('horas_ini'); 
        
        $this->hra_num_inicio   = $this->session->userdata('horas_ini'); 
        
        $this->dia_num          = 0;
        $this->horas            = $this->session->userdata('horas_tot'); 

        $this->arr_dias         = array("Dom","Lun","Mar","Mi&eacute;","Jue","Vie","S&aacute;b");
        $this->dia              = "";
        
        $this->html             = "";
        
        $this->lim_horas        = $this->horas ;
        $this->hra_num          = $this->hra_num_inicio;
        
        
        $this->clase_marco      = "row span1 cal_filas";
        $this->clase_supramarco = "row span1 cal_filas2";
        $this->arr_reservas     = array();
        $this->arr_ovs          = array();
        $this->arr_tipos_blo    = array();
        $this->arr_bloques      = array();
        $this->arr_capacidades  = array();
        $this->arr_bloqueados  = array();
        
        $this->cap_planta       = 7;
        
        $this->modo             = "";
        
        $this->gmt              = -3;
        
        //$this->modo_cal         = $this->session->userdata('modo_cal'); 
        
        //$this->mantenedor = $this->modo_cal;
        
        //echo $this->mantenedor;
    }
    
    
    public function set_encabezado($titulo=""){  
        $this->encabezado->texto        = $titulo;
        $this->encabezado->clase_fondo = "";
    }
    
    public function get_html($tipo=null){
        if($this->encabezado->clase_fondo == ""){
            $this->encabezado->clase_fondo = $this->clase_enca;
        }
        $this->encabezado->crear_bloque();
        
        $this->html .= $this->encabezado->html;  
        for($i=0;$i<$this->horas;$i++){
            if($this->arr_bloques[$i]->clase_fondo == ""){
                $this->arr_bloques[$i]->clase_fondo = $this->clase_fondo;
            }
            $this->arr_bloques[$i]->crear_bloque();
            $this->html .= $this->arr_bloques[$i]->html;  
        }
        if($tipo==null){
            return $this->html;
        }else if($tipo == "in"){
            $marco_ini  = '<div class="'.$this->clase_supramarco.'"><div class="'.$this->clase_marco.'">';
            $marco_ter  = '</div></div>';
            return  $marco_ini.$this->html.$marco_ter;
        }
    }
    
    public function generar_horas(){
        $texto = "";
        for($i=0;$i<$this->horas;$i++){   
                if($this->hra_num < ($this->lim_horas-1)){    
                    $this->hra_num += $this->hra_step; 
                    
                }else{
                    $this->hra_num = "0";
                }
                
                if($this->hra_num < 10){
                    $this->hra_num = "0".$this->hra_num;
                }
                $texto = $this->hra_num.":00";
       
        $this->arr_bloques[$i] = new Bloque_class();
        $this->arr_bloques[$i]->clase_fondo = $this->clase_enca;
        $this->arr_bloques[$i]->texto = $texto;
        //$this->arr_bloques[$i]->crear_bloque(); 
        }
    }
    
    public function generar_dia(){
        $texto      = "&nbsp;";
        $vinculo    = "javascript:void(0);";
        
        //var_dump($this->arr_reservas);
        //echo $this->modo;
        

        $acum = 0;
        for($i=0;$i<$this->horas;$i++){                          
            $this->arr_bloques[$i] = new Bloque_class();
            $this->arr_bloques[$i]->texto = $texto;
            $this->arr_bloques[$i]->vinculo = $vinculo; 
                        
            $acum = $acum + $this->hra_step;
            $hora_TS = mktime($this->hra_num_inicio + $acum,null,null,date("m",$this->dia),date("d",$this->dia),date("Y",$this->dia)) ;
            
            $arr_reservas = $this->revisar_reservas($hora_TS);
            
            
            $this->arr_bloques[$i]->cap_planta = $this->revisar_cap_planta($hora_TS); 
            //$this->arr_bloques[$i]->texto = $this->arr_bloques[$i]->cap_planta;
            //$this->arr_bloques[$i]->cap_planta = $this->cap_planta;  
                        
            $arr_bloqueados = $this->revisar_bloqueados($hora_TS);
            
            $this->arr_bloques[$i]->set_hora($hora_TS); 
            
            
            
            if($arr_bloqueados){
                
                $tipo_bloque = $this->set_tipo_bloqueado($arr_bloqueados[0]->id_tipo);
                $this->arr_bloques[$i]->set_tipo_bloque($tipo_bloque);            
                $this->arr_bloques[$i]->bloqueo = 1;
                if($this->modo == "user"){
                    $vin_extras = "onclick='activar_modal_blo(\" ".$this->arr_bloques[$i]->hora."\", \"".$hora_TS."\", \"".$arr_bloqueados[0]->id_bloqueado."\" );'";
                }else if ($this->modo == "admin"){
                    $vin_extras = "onclick='activar_modal_2(\" ".$this->arr_bloques[$i]->hora."\", \"".$hora_TS."\", \"".$arr_bloqueados[0]->id_bloqueado."\" );'";
                }
                
            }else{
            
                $this->arr_bloques[$i]->set_reservas($arr_reservas);
                $this->arr_bloques[$i]->set_tipo_bloque(    $this->set_tipo_blo($arr_reservas)  );
                
                $hoy = time(); 
                $gmt = $this->gmt;
                $hoy_gmt = mktime(date("H",$hoy)+$gmt, date("i",$hoy), date("s",$hoy), date("m",$hoy), date("d",$hoy), date("Y",$hoy));
                
                
                $vin_extras = "";
                
                
               if($this->modo == "user"){
                    if($hora_TS >= $hoy_gmt){
                       $vin_extras = "onclick='activar_modal(\" ".$this->arr_bloques[$i]->hora."\", \"".$hora_TS."\", \"".$this->arr_bloques[$i]->cap_planta."\");'"; 
                    }else if ($this->modo == "admin"){
                        if($this->arr_bloques[$i]->cap_res > 0){
                            $vin_extras = "onclick='activar_modal(\" ".$this->arr_bloques[$i]->hora."\", \"".$hora_TS."\", \"".$this->arr_bloques[$i]->cap_planta."\");bloquear_guardar()'";
                        }
                    }   
               }else{
                    $vin_extras = "onclick='activar_modal_2(\" ".$this->arr_bloques[$i]->hora."\", \"".$hora_TS."\");'";
               }
               
            }
            
           $this->arr_bloques[$i] = $this->set_clases($this->arr_bloques[$i]);
            
           $this->arr_bloques[$i]->vin_extras = $vin_extras; 
        }
    }
   
    
    public function set_clases($bloque){
        if( ($bloque->tipo_bloque->id_tipo == 1) ) // 1: disponible y 2: no disponible
            {
                $bloque->clase_fondo="cal_bloque span1 over";                
            }
        return $bloque;
    }
    
    public function set_tipo_bloqueado($tipo_blo){
        $resultado = null;
        for($i=0;$i<count($this->arr_tipos_blo);$i++){            
            
            if($this->arr_tipos_blo[$i]['id_tipo'] == $tipo_blo ){
                $obj_tipo_bloque = new Tipo_bloque_model();
                $obj_tipo_bloque->set_registro_arr($this->arr_tipos_blo[$i]);
                
                $resultado = $obj_tipo_bloque;
            }
            
        }  
        return $resultado;
    }
    public function set_tipo_blo($arr_reservas){
        $cap_acum = 0;
        $tipo_blo = 1; // 1: disponible
        $resultado = null;
        
        for($i=0;$i<count($arr_reservas);$i++){                      
            $obj_reserva = new Reserva_model();
            $obj_reserva = $arr_reservas[$i];
            $cap_acum += $obj_reserva->capacidad;            
        }
        
            $cant_conf = 0; 
            $cant_prov = 0;
            $obj_reserva = new Reserva_model();
            
            for($i=0;$i<count($arr_reservas);$i++){
                $obj_reserva = $arr_reservas[$i];
                //var_dump($obj_reserva);
                $tipo_res = $obj_reserva->id_tipo;
                
                if($tipo_res == 2){ // 2: solicitud no confirmada
                    if($cant_conf == 0){
                        $tipo_blo = 4; // 4: Provisorio
                    }else{
                        $tipo_blo = 5; // 5: Mixta
                    }
                    $cant_prov += 1;
                }
                
                if($tipo_res == 1){ // 1: solicitud confirmada
                    if($cant_prov == 0){
                        $tipo_blo = 3; // 3: Confirmado
                    }else{
                        $tipo_blo = 5; // 5: Mixta
                    }
                    $cant_conf += 1;
                }                
            }
            
               
        if(     ($cap_acum > 0) && ($cap_acum>=$this->cap_planta) && ($cant_prov == 0)    ){
            $tipo_blo = 2;  // 2: no disponible  
        }
       
        if(     ($cap_acum > 0) && ($cap_acum>=$this->cap_planta) && ($cant_conf == 0)    ){
            $tipo_blo = 7;  // 2: no disponible  
        }
        
        for($i=0;$i<count($this->arr_tipos_blo);$i++){            
            
            if($this->arr_tipos_blo[$i]['id_tipo'] == $tipo_blo ){
                $obj_tipo_bloque = new Tipo_bloque_model();
                $obj_tipo_bloque->set_registro_arr($this->arr_tipos_blo[$i]);
                
                $resultado = $obj_tipo_bloque;
            }
            
        }
        //var_dump($this->arr_tipos_blo[0]['id_tipo']);
        //return $tipo_blo;
        return $resultado;
    }
    
    public function revisar_cap_planta($hora_TS){
        
        $cap = 0;
        foreach($this->arr_capacidades as $valor){
            $fech_obj_TS = strtotime($valor->fecha_inicio);
            if($hora_TS>=$fech_obj_TS){
                $cap = $valor->capacidad;                     
            }
        }
        //echo $cap." ".date("Y-m-d H:i",$hora_TS)."<br />";   
        return $cap;
    }
    
    
    public function revisar_reservas($hora_TS){
        $resultado = array();
        $x=0;        
        //echo $hora_TS.'<br/>';
        //if($this->arr_reservas != null){
            for($i=0;$i<count($this->arr_reservas);$i++){              
                
                //$this->arr_reservas[$i]->fecha = strtotime($this->arr_reservas[$i]->fecha);
                //echo "dia fecha: ".date("Y-m-d H:i",$this->arr_reservas[$i]->fecha)."<br />";
                //echo "dia fecha: ". $this->arr_reservas[$i]->fecha."<br />";
                
                $sh_hora_TS = date("Y-m-d H:i",$hora_TS);
                $sh_arr_res = date("Y-m-d H:i",$this->arr_reservas[$i]->fecha);
                
                //echo $sh_hora_TS." ".$sh_arr_res."<br />";

                if($sh_hora_TS == $sh_arr_res ){
                    //echo "iguales";
                    $resultado[$x] = $this->arr_reservas[$i];
                    $x++;
                }else{
                    //$compara = "";
                }  
                
                //echo "<br>";
            }
        //}
        //var_dump($resultado);
        return $resultado;
    }
    
    public function revisar_bloqueados($hora_TS){
        $resultado = false;
        $x=0;        
        //echo $hora_TS.'<br/>';
        //if($this->arr_reservas != null){
            for($i=0;$i<count($this->arr_bloqueados);$i++){              
                
                //$this->arr_reservas[$i]->fecha = strtotime($this->arr_reservas[$i]->fecha);
                //echo "dia fecha: ".date("Y-m-d H:i",$this->arr_reservas[$i]->fecha)."<br />";
                //echo "dia fecha: ". $this->arr_reservas[$i]->fecha."<br />";
                
                $sh_hora_TS     = date("Y-m-d H:i",$hora_TS);
                $sh_arr_res     = date("Y-m-d H:i",   strtotime(  $this->arr_bloqueados[$i]->fecha_inicio)        );
                $sh_arr_res2    = date("Y-m-d H:i",   strtotime(  $this->arr_bloqueados[$i]->fecha_termino)       );
                
                //echo $sh_hora_TS." ".$sh_arr_res."<br />";

                if(($sh_hora_TS >= $sh_arr_res) && ($sh_hora_TS < $sh_arr_res2)){
                    //echo "iguales";
                    $resultado[$x] = $this->arr_bloqueados[$i];
                    $x++;
                }else{
                    //$compara = "";
                }  
                
                //echo "<br>";
            }
        //}
        //var_dump($resultado);
        return $resultado;
    }
    
    
    
    
    public function set_reservas($arr_reservas){        
        $this->arr_reservas = $arr_reservas;
        for($i=0;$i<count($arr_reservas);$i++){
            $this->arr_reservas[$i]->set_fecha($this->arr_reservas[$i]->fecha);
        }
        //var_dump($this->arr_reservas);
    }
    
    public function set_capacidades($arr_capacidades){        
        $this->arr_capacidades = $arr_capacidades;        
    }


    public function get_dia($dia_num)
    {   
        if( $dia_num<count($this->arr_dias) ){
            return $this->arr_dias[$dia_num];  
        }else{
            return 0;
        }
    }
    
}
/*
*end modules/login/models/index_model.php
*/