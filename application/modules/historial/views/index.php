 <div class = "tabla_historial well">
        
        
            <h6>Historial de actividades</h6>
            <a style="float:right; margin-left: 10px; margin-bottom: 10px;" href="javascript:void(0);" onclick="activar_modal_his();" class="btn btn-success btn-small">Ver m&aacute;s</a>
            <!--a style="float:right; " href="javascript:void(0);" class="btn btn-success btn-small">Sincronizar</a-->
        
        <div id="div_datos_tabla_historial">
            
            <?=$tabla;?>
        </div>    
    </div>



<script>
    
   
    function activar_modal_his(){
        $('#btn_eliminar').css("display","none"); 
        //$('#btn_guardar').html("Guardar");
        $('#btn_guardar').css("display","none");
        //$('#btn_guardar').attr("onclick","insertar_reserva();");
        $( ".modal-body" ).html('');  
        var titulo = "Historial de actividades";
        
        $('#myModalLabel').html( titulo );
        //hora_TS2 = hora_TS;
        var request = $.ajax({
            url:"<?=base_url(); ?>index.php/historial/historial/index/1",
            type: "POST",
            dataType: "html"
        });
        request.done(function( msg ) {            
            $( ".modal-body" ).html( msg );            
            $('#myModal').modal('show');
            
        });
        request.fail(function( jqXHR, textStatus ) {
            alert( men_2 );
        });        
    }
    /*
    function activar_modal3(id_ov,id_tipo){
        var titulo = "Orden de venta"; 
        if(id_tipo == 1){        
            titulo += " confirmada";
        }else if(id_tipo == 2){
            titulo += " provisoria";
        }
        
        $('#myModalLabel').html( titulo );
        //hora_TS2 = hora_TS;
        $('#btn_guardar').attr("onclick","alert('bla')");
        var request = $.ajax({
            url: "<?=base_url(); ?>index.php/tabla_ov/tabla_ov/ov_view/"+id_ov,
            type: "POST",
            dataType: "html"
        });
        request.done(function( msg ) {            
            $( ".modal-body" ).html( msg );            
            $('#myModal').modal('show');
            
        });
        request.fail(function( jqXHR, textStatus ) {
            alert( men_2 );
        });        
    } 
    
    
    function insertar_ov(){        
    
        cam_cliente         = $('#cam_cliente').val();
        cam_capacidad       = $('#cam_capacidad').val();
        cam_kilogramos      = $('#cam_kilogramos').val();
        cam_piezas          = $('#cam_piezas').val();
        cam_fecha_entrega   = $('#cam_fecha_entrega').val();
        cam_comentario      = $('#cam_comentario').val();
        
        
        //console.log(id_ov + " " + prioridad + " " + capacidad + " " + piezas + " " + kilogramos + " " + fecha);
        
        if  ( 
             (cam_cliente!="") && (cam_capacidad!="")  &&  (cam_kilogramos!="")   &&  (cam_piezas!="") &&
             (cam_fecha_entrega!="")
            )     
           {
            var request = $.ajax({
                
                url: "<?=base_url(); ?>index.php/tabla_ov/tabla_ov/insertar_ov/",
                type: "POST",
                data: { 
                        cliente: cam_cliente, 
                        capacidad: cam_capacidad,
                        kilogramos: cam_kilogramos,
                        piezas: cam_piezas,
                        fecha_entrega: cam_fecha_entrega,
                        comentario: cam_comentario
                        },
                dataType: "html"
            });
            request.done(function( msg ) {            
                if(msg==-1){
                    alert(men_1); // Faltan datos
                }else if(msg==-3){
                    alert(men_4); // Cap erronea
                }else if(msg==1){
                    alert(men_3); // Exito
                    $('#myModal').modal('hide');  
                    tabla_act();
                    //cal_act();
                }
                
            });
            request.fail(function( jqXHR, textStatus ) {
                alert( men_2 );
            });
        }else{
            alert(men_1);
        }        
    
    }
  
  function tabla_act(){
        var request = $.ajax({                
                url: "<?=base_url(); ?>index.php/tabla_ov/tabla_ov/index/",
                type: "POST",               
                dataType: "html"
            });
       request.done(function( msg ) {
           $("#tabla_ov_ajax").html(msg);
           on_ready_2();
       });
       
       request.fail(function( jqXHR, textStatus ) {
           alert( men_2 );
       });
  }
  
  function on_ready_2(){
       $( ".det_ov" ).click(function() {
            var $input      = $( this );
            var id_ov       = $input.attr( "data-id-ov" );
            var tipo_ov     = $input.attr( "data-id-tipo" );
            //alert(id);
            activar_modal3(id_ov,tipo_ov);            
            
        });
  }
 
 on_ready_2();   
  */
 </script>   