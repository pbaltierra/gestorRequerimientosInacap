<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Bloque_class
{
    Public $id;
    Public $texto;
    Public $clase_fondo;
    Public $clase_texto;
    Public $html;
    Public $vinculo;
    Public $vin_extras;
    Public $hora;
    Public $reservas;
    Public $cap_planta;
    
    Public $tipo_bloque;
    Public $cap_res;
    Public $bloqueo;
    Public $hora_TS;
    
    
    public function __construct()
    {        
        //parent::__construct();  
        
        $this->id           = 0;
        $this->texto        = "";
        $this->clase_fondo  = "cal_bloque span1 popover-link over";        
        $this->clase_texto  = "";
        $this->html         = "";   
        $this->vinculo      = "";
        $this->vin_extras   = ""; 
        $this->hora         = 0;
        $this->cap_planta   = 0;
        $this->reservas     = array();
        
        $this->tipo_bloque  = new Tipo_bloque_model();
        $this->cap_res      = 0;
        $this->bloqueo = null;
        $this->hora_TS = 0;
    }
    
       
    public function crear_bloque($titulo=null){
            $bloqueo = $this->bloqueo;
            
            
            //echo $this->hora;
            //$fecha_hoy  = date("Y-m-d H:i"); 
            //$fecha      = date("Y-m-d H:i", $this->hora_TS); 
            //echo $fecha." <br/>";
            //if($fecha_hoy<=$fecha){
            //    $this->html.="<a href='".$this->vinculo."' ".$this->vin_extras;
            //}else{
                $this->html.="<a href='".$this->vinculo."' ".$this->vin_extras;
            //}
            
            
            
            if($titulo!=null){
                $this->html .= "class='".$this->clase_fondo."' style='background-color: ".$this->tipo_bloque->color_fondo."; color=\'".$this->tipo_bloque->color_texto."\'    '>".$titulo."\r";
            }else{
                if($this->cap_res>0){
                    $this->html .= "data-content='".$this->genera_txt_pop($bloqueo)."' data-original-title='".$this->hora."' class='".$this->clase_fondo."' style='background-color: ".$this->tipo_bloque->color_fondo."; color:".$this->tipo_bloque->color_texto."; '>".$this->texto."\r";
                }else{
                    $this->html .= "class='".$this->clase_fondo."' style='background-color: ".$this->tipo_bloque->color_fondo."; color:".$this->tipo_bloque->color_texto."; '>".$this->texto."\r";
                }
            }
            
            $this->html.="</a>";
       
    }
    
    public function genera_txt_pop($bloqueo=null){
        
        $txt_cap    = "Cap. Programada: ".$this->cap_res."/".$this->cap_planta;        
        $txt_cli    = "Cliente(s): "; $clientes   = "";
             
        $txt_tip    = "Bloque: ".$this->tipo_bloque->nombre;
        
        
        //$txt_aut    = "Autor(es): "
        $clientes = "";      
        for($i=0;$i<count($this->reservas);$i++){
            $clientes .= $this->reservas[$i]->cliente;
            
            if( $i<(count($this->reservas)-1)   ) $clientes .=", ";                        
        }    
        $txt_cli    .= $clientes;
        
        if($bloqueo == null){
            $txt = $txt_cap."<br/>".$txt_cli."<br/>".$txt_tip; 
        }else{
            $txt = $txt_cap."<br/>".$txt_tip; 
        }   
          
        return $txt;
    }    
    
    public function set_hora($hora_TS){
        $this->hora_TS = $hora_TS;
        $this->hora = date("d/m/Y H:i",$hora_TS);
        //$this->hora = $hora_TS;
    }
    
    public function set_reservas($arr_reservas){
        $this->reservas = $arr_reservas;        
        
        
        $cap_acu = 0;
        for($i=0;$i<count($this->reservas);$i++){           
           $cap_acu += $this->reservas[$i]->capacidad;
        }
        $this->cap_res = $cap_acu;
        
        if($this->cap_res > $this->cap_planta){
            $this->cap_res = $this->cap_planta;
        }       
        if( ($this->cap_planta > 0) && ($this->cap_res > 0)  ){
            $this->texto = $this->cap_planta - $this->cap_res;
            if($this->texto < 0){
                $this->texto = 0;
            }
        }
        //var_dump($arr_reservas);
    }
    
    public function set_tipo_bloque($tipo_bloque=null){
        if($tipo_bloque != null){
            $this->tipo_bloque = $tipo_bloque;
        }else{
            /*
                for($i=0;$i<count($this->reservas);$i++){
                    $this->reservas[$i]->capacidad;
                }
             * 
             */
        }        
    }
    
}
/*
*end modules/login/models/index_model.php
*/