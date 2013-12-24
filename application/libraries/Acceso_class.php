<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Acceso_class {
    //put your code here
    
    Public $arr_permisos;
    Public $id_seccion;
    Public $criterio;
    Public $operador;
     
    public function __construct()
    { 
        $this->arr_permisos     = array();
        $this->id_seccion       = 0;
        $this->criterio         = array();
        $this->operador         = "or";
    }
    
    public function verificar_permisos($criterio=null){  
        $res = false;
        //var_dump($this->arr_permisos);
        if(($criterio == null) && ($this->criterio!=null)){
            $criterio = $this->criterio;
        }
            
        $criterio       = strtolower($criterio);
        $arr_criterio   = explode(",", $criterio); 
        
        //var_dump($this->arr_permisos);
        //return;
        
        if(count($this->arr_permisos)>0){
            foreach($this->arr_permisos as $valor){
                if($valor['id_seccion'] == $this->id_seccion){
                    //echo $valor[$arr_criterio[0]];

                    for($i=0;$i<count($arr_criterio);$i++){
                        //echo $valor[$arr_criterio[$i]]." ".$arr_criterio[$i];
                        if($valor[$arr_criterio[$i]] == 1){
                            //echo "valido <br/>";
                            return true;
                        }
                    }

                }
            }
        }
        return $res;
    }
    
    
}
?>
