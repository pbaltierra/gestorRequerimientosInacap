
<div class = "tabla_ov well">
        
        <!--div-->
            <h6>&Oacute;rdenes de venta</h6>
            <?php if ($crear_ov){?>
            <a style="float:right; margin-left: 10px; margin-bottom: 10px;" href="javascript:void(0);" onclick="activar_modal2();" class="btn btn-success btn-small">Crear</a>
            <?php } ?>
            <?php if ($sincro == "sap"){?>
            <a style="float:right; " href="javascript:void(0);" class="btn btn-success btn-small" href="javascript:void(0);" onclick="act_modal('sincronizar',{0:'bla'});">Sincronizar</a>
            <?php } ?>
        <!--/div-->
        <div id="div_datos_tabla_ov">
       <?=$tabla;?>
        </div>    
    </div>

<script>
    
    function act_modal(tipo){
        //params[0] = "bla";
    
        switch(tipo){
            case "sincronizar":
                var titulo = "Sincronizar con SAP";
                $('#myModalLabel').html(titulo);
                $('#btn_eliminar').css("display","none");
                $('#btn_guardar').css("display","none");
                $('#btn_guardar').html("Sincronizar");
                $('#btn_guardar').attr("onclick","sincro_sap();");
                
                $( ".modal-body" ).html = "";
                
                url = "<?=base_url(); ?>index.php/tabla_ov/tabla_ov/sincronizar_sap";
                var request = $.ajax({
                    url: url,
                    type: "POST",
                    //data: { params:params },
                    dataType: "html"
                });
                
                request.done(function( msg ) {
                    $('#btn_guardar').css("display","inline-block");
                    $( ".modal-body" ).html( msg );
                    $('#myModal').modal('show');
                });
                
                request.fail(function( jqXHR, textStatus ) {
                    $( ".modal-body" ).html( "<center>"+men_2+"</center>" ); 
                    $('#myModal').modal('show');
                });
                
                break;
                
            default:
                break;
               
        }
        
    }
    
    function sincro_sap(){
        fin = $( "#fin" ).val();
            url = "<?=base_url(); ?>index.php/tabla_ov/tabla_ov/sincronizar_sap";
            
             var request = $.ajax({
                    url: url,
                    type: "POST",
                    data: { fin:fin },
                    dataType: "html"
                });
                
                request.done(function( msg ) {
                               
                if(msg==1){
                    $( ".modal-body" ).html("<center>Sincronizaci&oacute;n exitosa</center>");
                    $('#btn_guardar').css("display","none");
                     tabla_act();
                }else{
                    $( ".modal-body" ).html( "<center>"+men_2+"</center>" ); 
                }
               
               });
                request.fail(function( jqXHR, textStatus ) {
                    $( ".modal-body" ).html( "<center>"+men_2+"</center>" ); 
                    //$('#myModal').modal('show');
                });   
    }
      
    
    // Crear OV
     function activar_modal2(){
         
         $('#btn_eliminar').css("display","none");
         $('#btn_guardar').css("display","inline-block");
         $('#btn_guardar').html("Crear");
         
        var titulo = "Orden de venta provisoria";
        $('#myModalLabel').html( titulo );
        //hora_TS2 = hora_TS;
        $('#btn_guardar').attr("onclick","insertar_ov();");
        var request = $.ajax({
            url: "<?=base_url(); ?>index.php/tabla_ov/tabla_ov/ov",
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
    
    
    
    // Ver OV
    function activar_modal3(id_ov,id_tipo){
        
        
        //'<a id="btn_eliminar" class="btn btn-danger hide" onclick="" >Eliminar</a>';
        
        var titulo = "Orden de venta"; 
        $('#btn_guardar').css("display","none"); 
        if(id_tipo == 1){ 
            $('#btn_eliminar').css("display","none");
            titulo += " confirmada";
            <?php if ($editar_ov_c){?>           
                $('#btn_guardar').html("Editar");
                $('#btn_guardar').css("display","inline-block");
                $('#btn_guardar').attr("onclick","editar_ov("+id_ov+","+id_tipo+")");
                    <?php if ($eliminar_ov){?>
                    $('#btn_eliminar').css("display","inline-block");
                    $('#btn_eliminar').attr("onclick","eliminar_ov("+id_ov+")");
                    <?php } ?>               
             <?php } ?>
        }else if(id_tipo == 2){
            titulo += " provisoria";
             <?php if ($editar_ov){?>
            $('#btn_guardar').html("Editar");
            $('#btn_guardar').css("display","inline-block");
            $('#btn_guardar').attr("onclick","editar_ov("+id_ov+","+id_tipo+")");
                <?php if ($eliminar_ov){?>
                $('#btn_eliminar').css("display","inline-block");
                $('#btn_eliminar').attr("onclick","eliminar_ov("+id_ov+")");
                <?php } ?>
            
            <?php } ?>
        }
        
        
        
        $('#myModalLabel').html( titulo );
        //hora_TS2 = hora_TS;
        
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

<?php if ($editar_ov){?>
    function editar_ov(id_ov,id_tipo){        
    
        cam_cliente         = $('#cam_cliente').val();
        cam_capacidad       = $('#cam_capacidad').val();
        cam_kilogramos      = $('#cam_kilogramos').val();
        cam_piezas          = $('#cam_piezas').val();
        cam_fecha_entrega   = $('#datepicker_ov').val();
        cam_comentario      = $('#cam_comentario').val();
        
        if(id_tipo == 1){
            cam_id_ovsap        = $('#id_sap').val();
        }else{
            cam_id_ovsap        = $('#id_ovsap option:selected').val();
        }    
        //    
        //console.log(id_ov + " " + prioridad + " " + capacidad + " " + piezas + " " + kilogramos + " " + fecha);
        
        if  ( 
             (cam_cliente!="") && (cam_capacidad!="")  &&  (cam_kilogramos!="")   &&  (cam_piezas!="") &&
             (cam_fecha_entrega!="")
            )     
           {
            var request = $.ajax({
                
                url: "<?=base_url(); ?>index.php/tabla_ov/tabla_ov/editar_ov/",
                type: "POST",
                data: { 
                        cliente: cam_cliente, 
                        capacidad: cam_capacidad,
                        kilogramos: cam_kilogramos,
                        piezas: cam_piezas,
                        fecha_entrega: cam_fecha_entrega,
                        comentario: cam_comentario,
                        id_ov:id_ov,                        
                        ov_tipo:id_tipo,                        
                        id_ovsap: cam_id_ovsap
                        },                                
                dataType: "html"
            });
            
            request.done(function( msg ) {            
                if(msg==-1){
                    alert(men_1); // Faltan datos
                    $( ".modal-body" ).html( "<center>"+men_1+"</center>" ); 
                }else if(msg==-3){
                    alert(men_4); // Cap erronea                   
                }else if(msg==-4){
                    alert(men_9); // Fecha incorrecta        
                }else if(msg==-5){
                    alert(men_10); // No es el autor 
                }else if(msg==-11){
                    alert(men_11); // Cliente incorrecto                            
                }else if(msg==1){
                    $('#myModal').modal('hide'); 
                    cal_act();
                    tabla_act();
                    historial_act();
                }
                
            });
            request.fail(function( jqXHR, textStatus ) {
                alert( men_2 );
            });
        }else{
            alert(men_1);
        }        
    
    }
<?php } ?>

 <?php if ($eliminar_ov){?>
    function eliminar_ov(id_ov){
        if(id_ov!=""){
            
            var conf = confirm(men_8);
            
            if(conf){
                var request = $.ajax({

                    url: "<?=base_url(); ?>index.php/tabla_ov/tabla_ov/eliminar_ov/",
                    type: "POST",
                    data: { id_ov: id_ov },
                    dataType: "html"
                });
                request.done(function( msg ) {            
                    if(msg==1){
                        //alert(men_7); // Exito
                        $('#myModal').modal('hide');  
                        tabla_act();
                        historial_act();
                        cal_act();
                    }else if(msg==-5){
                        alert(men_10); // No es el autor  
                    }
                });
                request.fail(function( jqXHR, textStatus ) {
                    alert( men_2 );
                });
            }    
        }    
    }
 <?php } ?>


<?php if ($crear_ov){?>
    function insertar_ov(){        
    
        cam_cliente         = $('#cam_cliente').val();
        cam_capacidad       = $('#cam_capacidad').val();
        cam_kilogramos      = $('#cam_kilogramos').val();
        cam_piezas          = $('#cam_piezas').val();
        cam_fecha_entrega   = $('#datepicker_ov').val();
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
                }else if(msg==-4){
                    alert(men_9); // Fecha incorrecta
                }else if(msg==-11){
                    alert(men_11); // Cliente incorrecto    
                }else if(msg==1){
                    //alert(men_3); // Exito
                    $('#myModal').modal('hide');  
                    tabla_act();
                    historial_act();
                }
                
            });
            request.fail(function( jqXHR, textStatus ) {
                alert( men_2 );
            });
        }else{
            alert(men_1);
        }        
    
    }
<?php } ?> 
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
 
 function historial_act(){
     var request = $.ajax({                
                url: "<?=base_url(); ?>index.php/historial/historial/index/",
                type: "POST",               
                dataType: "html"
            });
       request.done(function( msg ) {
           $("#tabla_historial_ajax").html(msg);
           //on_ready_2();
       });
       
       request.fail(function( jqXHR, textStatus ) {
           alert( men_2 );
       });
 }
 
 
 
 on_ready_2();   
  
 </script>   