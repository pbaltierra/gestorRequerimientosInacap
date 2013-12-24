<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Ov_class {
    //put your code here
    
    Public $arreglo;
    Public $tabla;
    Public $fecha;
    Public $fecha_mayor;
    Public $campos;
    Public $id_planta;
    Public $id_tipo;
    Public $grupo;
    Public $desde;
    Public $hasta;
    Public $estado;
    Public $procesada;
    Public $orden;
    Public $cliente;
    
    public function __construct()
    { 
        $this->arreglo      = array();
        $this->tabla        = "ov";
        //$this->fecha        = date("Y-m-d H:i"); 
        $this->fecha        = null;
        $this->fecha_mayor  = null;
        $this->campos       = "";
        $this->id_planta    = 0;
        $this->id_tipo      = 0;
        $this->grupo        = "";
        $this->desde        = "";
        $this->hasta        = "";
        $this->estado       = "";
        $this->procesada    = null;
        $this->orden        = null;
        $this->cliente      = null;
    }
    
    public function get_ovs_programables($id_planta){        
       
    }
    
    
    public function get_ovs($id_planta, $id_ov = null, $cliente = null, $todas=null, $id_interno = null){   
        
        //$procesada  = 1; // ov procesada
        $limite     = null;
        
        if($this->orden==null){
            $orden      = array(array("campo" => "ov.fecha_entrega", "direccion" => "asc"));
        }else{
            $orden      = $this->orden;
        }
        $campos     = "ov.*, tipo_ov.nombre tipo_nombre, cliente.id_interno";
        
        /*
        if($this->fecha == null){
            $this->fecha        = date("Y-m-d H:i"); 
        }
        */
        $condicion  = array(
                            "ov.id_planta"          =>  $id_planta,
                            //'ov.estado <>'          =>  $procesada//,
                            //'ov.fecha_entrega >='   =>  $this->fecha
                            );
        if($id_ov != null)              { $condicion += array("ov.id_ov" => $id_ov)         ;}
        if($id_interno != null)         { $condicion += array("cliente.id_interno" => $id_interno);}
        
        if($this->procesada != null)    { $condicion += array("ov.estado" => 1)         ;}
        if($this->fecha_mayor != null)  { $condicion += array("ov.fecha_entrega >=" => $this->fecha_mayor)         ;}
        if($this->id_tipo != null)      { $condicion += array("ov.id_tipo" => $this->id_tipo)         ;}
        
        $join       = array(
                            array("tabla" =>"tipo_ov", "condicion" => "ov.id_tipo = tipo_ov.id_tipo")
                            ,array("tabla" =>"cliente", "condicion" => "ov.id_cliente = cliente.id_cliente")
                            );
        
        $like = array();
        if($cliente != null)    {            array_push($like,array("campo" => "ov.cliente", "valor" => $cliente))     ;}
        
        if($this->grupo == null)      { $grupo = null; }else{   $grupo  = $this->grupo;   }
        
        $this->arreglo = array(
                        "campos"    => $campos,
                        "condicion" => $condicion,
                        "join"      => $join,
                        "limite"    => $limite,
                        "orden"     => $orden,
                        "grupo"     => $grupo,
                        "like"      => $like
                        );
        
    }
    
    public function get_cliente($id_planta, $id_ov){
        
        $campos         = "ov.cliente";
        $condicion      = array(
                            "ov.id_planta" =>  $id_planta,
                            "ov.id_ov"     =>  $id_ov
                            );
        $limite         = 1;
        
        $this->arreglo  = array(
                        "campos"    => $campos,
                        "condicion" => $condicion,
                        "limite"    => $limite
                        );
    }
    public function get_clientes($id_planta){
        
        $campos         = "ov.id_ov,ov.cliente";
        $condicion      = array(
                            "ov.id_planta"          =>  $id_planta
                            //'ov.fecha_entrega >='   =>  $this->fecha
                            );
        if($this->fecha != null)      { $condicion += array("ov.fecha_entrega >=" => $this->fecha);}
        
        if($this->grupo == null)      { $grupo = "ov.cliente"; }else{   $grupo  = $this->grupo;   }
        
        $this->arreglo  = array(
                        "campos"    => $campos,
                        "condicion" => $condicion,
                        "grupo"     => $grupo
                        );
    }
    
    public function get_registro_campo($campo=null,$valor=null){  
        
        if($this->campos == null){
            $campos     = $this->tabla.".*";
        }else{
            $campos =   $this->campos;
        }
        $condicion  = array();
        
        if(     ($campo!=null) && ($valor!=null)    ){
        $condicion  = array(                           
                            $campo  =>  $valor
                            );
        } 
        
        if($this->fecha != null)      { $condicion += array("fecha_creacion" => $this->fecha)         ;}
        
        if($this->id_planta != null)  { $condicion += array("id_planta" => $this->id_planta)         ;}
        
        if($this->grupo == null)      { $grupo = null; }else{   $grupo  = $this->grupo;   }
        
        if($this->cliente != null)      { $condicion += array("cliente" => $this->cliente)         ;}
        
        
        
        
        $this->arreglo = array(
                        "campos"        => $campos,
                        "condicion"     => $condicion,
                        "grupo"     => $grupo
                        );     
    }
    
    
    
    
    
    
    
    public function reporte($id_ov=null,$cliente=null,$id_tipo=null,$id_ovsap=null,$fechas = null){
        
        $limite     = null;
        $orden      = array(array("campo" => "ov.fecha_entrega", "direccion" => "asc"));
        
        if($this->campos == null){
            $campos     = "ov.*,
                       cliente.id_interno cli_id,
                       cliente.nombre cli_nombre,
                       tipo_ov.nombre tipo_nombre, 
                       planta.nombre nom_planta, 
                       reserva.id_reserva, 
                       reserva.fecha res_fecha, 
                       reserva.kilogramos res_kilos, 
                       reserva.capacidad res_vigas, 
                       reserva.piezas res_piezas";
        }else{
            $campos = $this->campos;
        }
        $condicion  = array(
                                "tipo_ov.valido !="     => 0, 
                                "planta.valido !="      => 0, 
                                "reserva.valido !="     => 0, 
                                "cliente.valido !="     => 0,
                            );
        
        
        if($this->grupo!=null){
            $grupo = $this->grupo;
        }else{
            $grupo      = "reserva.id_reserva";
        }    
        
        if($id_ov != null)              { $condicion += array("ov.id_ov" => $id_ov)         ;}
        if($id_tipo != null)            { $condicion += array("ov.id_tipo" => $id_tipo)     ;}
        if($this->id_planta != null)    { $condicion += array("ov.id_planta" => $this->id_planta);}  
        //if($this->fecha != null)        { $condicion += array("reserva.fecha >=" => $this->fecha);}  
        
        if($fechas != null){
           if($fechas['prog']['desde'] != null){
                $condicion += array("reserva.fecha >=" => $fechas['prog']['desde'])         ;
           } 
           if($fechas['prog']['hasta'] != null){
                $condicion += array("reserva.fecha <=" => $fechas['prog']['hasta'])         ;
           }
           
           
           if($fechas['crea']['desde'] != null){
                $condicion += array("ov.fecha_creacion >=" => $fechas['crea']['desde'])         ;
           } 
           if($fechas['crea']['hasta'] != null){
                $condicion += array("ov.fecha_creacion <=" => $fechas['crea']['hasta'])         ;
           }
           
           if($fechas['entr']['desde'] != null){
                $condicion += array("ov.fecha_entrega >=" => $fechas['entr']['desde'])         ;
           } 
           if($fechas['entr']['hasta'] != null){
                $condicion += array("ov.fecha_entrega <=" => $fechas['entr']['hasta'])         ;
           }
        }
        
                
        $join       = array(
                            array("tabla" =>"tipo_ov", "condicion" => "ov.id_tipo = tipo_ov.id_tipo"),
                            array("tabla" =>"planta", "condicion" => "ov.id_planta = planta.id_planta"),
                            array("tabla" =>"reserva", "condicion" => "ov.id_ov = reserva.id_ov"),
                            array("tabla" =>"cliente", "condicion" => "ov.id_cliente = cliente.id_cliente"),
                            );
        
        $like       = array ();
        if($cliente != null)    {array_push($like, array("campo" => "cliente", "valor" => $cliente));}
        if($id_ovsap != null)   {array_push($like, array("campo" => "id_ovsap", "valor" => $id_ovsap));}
        if($this->fecha != null){array_push($like, array("campo" => "reserva.fecha", "valor" => $this->fecha));}
        
        $this->arreglo = array(
                        "campos"    => $campos,
                        "condicion" => $condicion,
                        "join"      => $join,
                        "limite"    => $limite,
                        "grupo"     => $grupo,
                        "like"      => $like,
                        "orden"     => $orden
                        );
        
        
    }
    
    
    
    public function grafico($campo=null,$valor=null,$id_vendedor=null,$id_cliente=null){  
        
        if($this->campos == null){
            $campos     = $this->tabla.".*";
        }else{
            $campos =   $this->campos;
        }
        $condicion  = array(
                                //"tipo_ov.valido !="     => 0, 
                                "usuario.valido !="     => 0, 
                                //"reserva.valido !="     => 0, 
                                //"cliente.valido !="     => 0,
                            );
        
        if(     ($campo!=null)     ){
        $condicion  = array(                           
                            $campo  =>  $valor
                            );
        }         
        if($this->id_planta != null)    { $condicion += array("id_planta" => $this->id_planta) ;}   
        if($this->id_tipo != null)      { $condicion += array("ov.id_tipo" => $this->id_tipo) ;}   
        if($this->desde != null)        { $condicion += array("ov.fecha_creacion >=" => $this->desde) ;}   
        if($this->hasta != null)        { $condicion += array("ov.fecha_creacion <=" => $this->hasta) ;}   
        
        $like       = array ();
        
        if($id_vendedor != null)        {
            $nombre = explode(" ", $id_vendedor);
            if(isset($nombre[0])){
                array_push($like, array("campo" => "usuario.nombre", "valor" => $nombre[0]));
            }    
            if(isset($nombre[1])){
                array_push($like, array("campo" => "usuario.ape_paterno", "valor" => $nombre[1]));
            }
            //$like += array("nombre" => $nombre[0]);
            //$like += array("ape_paterno" => $nombre[1]);
        }
        
        if($this->fecha != null){array_push($like, array("campo" => "ov.fecha_creacion", "valor" => $this->fecha));}
        
        if($id_cliente != null)         { array_push($like, array("campo" => "ov.cliente", "valor" => $id_cliente));}
        
        if($this->limite == null)       { $limite = null; }else{   $limite  = $this->limite;   }               
             
        if($this->grupo == null)        { $grupo = null; }else{   $grupo  = $this->grupo;   }
        
        $join       = array(
                            array("tabla" =>"usuario", "condicion" => "ov.id_usuario = usuario.id_usuario"),
                            //array("tabla" =>"planta", "condicion" => "ov.id_planta = planta.id_planta"),
                           // array("tabla" =>"reserva", "condicion" => "ov.id_ov = reserva.id_ov"),
                           // array("tabla" =>"cliente", "condicion" => "ov.id_cliente = cliente.id_cliente"),
                            );
        
        
        $this->arreglo = array(
                        "campos"        => $campos,
                        "condicion"     => $condicion,
                        "like"          => $like,
                        "join"          => $join,
                        "grupo"         => $grupo,
                        "limite"        => $limite
                        );     
    }
    
    
}
?>
