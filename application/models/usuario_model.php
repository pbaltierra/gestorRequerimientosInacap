<?php
 
/**
 * @author McCubo
 * @copyright 2011
 * @license GPL V2.
 */
class Usuario_model extends CI_Model {
    
    public $id_usuario;
    public $nombre;
    public $ape_paterno;
    public $ape_materno;
    public $id_sap;
    public $valido;
    public $fecha_creacion;
    public $fecha_actualizacion;
    public $email;
    public $clave;
    public $user;
    //public $id_tipo;       // objeto tipo de usuario
    
    
    public $perfil;         // arr de perfiles
    
    
    public $nom_planta;
    private $tabla;
    private $campoValido;
    public $plantas;     
    
    //public $id_planta;  
    
    public function __construct() {
        parent::__construct(); //Llamada al constructor padre.
        $this->load->helper('security');
        $this->tabla = "usuario";
        $this->campoValido = "valido";
        $this->perfil = array();
        $this->plantas = array();
        $this->valido = 1;
        //$this->id_tipo = 0;
    }
    
    public function get_registro($criterio="", $valor="")
        {
            if (($criterio == "") && ($valor==""))
            {
                    $query = $this->db->get($this->tabla);
                    return $query->result_array();
            }

            $query = $this->db->get_where($this->tabla, array($criterio => $valor,  $this->campoValido => 1));
            return $query->row_array();
        }
        
    public function get_registros_arr($interno=null){         
        $arreglo = get_object_vars($this); 
        if($interno == null){
            
            unset(
                    $arreglo['tabla'],
                    $arreglo['perfil'],
                    $arreglo['campoValido'],
                    $arreglo['id_tipo'],
                    $arreglo['nom_planta'],
                    //$arreglo['id_planta'],
                    $arreglo['plantas']
                    );
            
        }         
        return $arreglo;         
    }
    
    public function set_registro_arr($arreglo){        
        reset($arreglo);  
        while (list($key, $value) = each($arreglo)) {  
          $this->$key = $value;  
        }         
    }
    
    public function desencriptar_clave(){
        //$this->load->library('encrypt');
        //$this->clave = $this->encrypt->decode($this->clave);
        $this->clave = "protegida";
    }
    
    public function encriptar_clave($clave){
        $this->load->library('encrypt');
        return do_hash($clave, 'md5'); // MD5 
        //return $this->encrypt->encode($clave);
    }   
}
?>