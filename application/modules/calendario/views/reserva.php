<div id="reserva_plantilla">
    <?=$reservas;?> 
</div>
<p style="
   display: inline-block;
   font-size: 11px;
   width: 200px;">Capacidad disponible: <?=$cap_lib;?></p>

<div id="popover_falso" class="popover" style="
    display: none;
    float: right;
    position: relative;
    padding-left: 10px;
    padding-right: 10px;
    "
    >
    
    <input id="in_editar" type="text" class="span1" style="margin-top:10px">
    <button id="btn_enviar" class="btn btn-primary" type="button">
        <i class="icon-ok icon-white"></i>
    </button>
    
    <!-- a href="javascript:void(0);" onclick="enviar_pop_falso()" class="btn btn-primary" style="margin-bottom:10px; margin-left:5px;">Ok</a-->
    <button class="btn editable-cancel" type="button">
        <i class="icon-ban-circle"></i>
    </button>
    <span class="help-block" style="color:red; font-size:11px;" id="msg_popover"></span>
</div>

<div class="modal_linea">&nbsp;</div>
<?php if($crear_reserva){?>
<div style="display:<?=$display_sol;?>; width:100%">        
    <h6 class="txtcentrado">Solicitud de reserva </h6>
    <div id="msje"></div>
    <table id="solicitud_reserva" style="margin-left:130px;">
                <tr>
                    <td class="span1" >Cliente</td>
                     <td>
                        <?=$combo_cli;?>    
                       
                    </td>
                    
                    <td class="span1" >
                       
                    </td>
                    <td class="span1" > <a style="margin-top: -12px;" id="bus_ov" onclick="cambiar_ov();" href="javascript:void(0);" class="btn btn-success btn-small">Buscar</a></td>
                    <td class="span1" ></td>
                    


                </tr>
                
                
                <tr>
                    <td>OV</td>
                    <td id="combo_ov" >
                         <select id="id_ov" class="span3">
                            <option value="">Seleccionar</option>                                               
                         </select> 
                    </td>
                    
                    <td class="span1" ></td>
                    <td>Vigas</td>
                    <td><input id="capacidad" class="span1" type="text" /></td>
                </tr>    
                

                <tr>
                   <td>Tipo OV</td>
                    <td id="tipo_ov">No seleccionada</td>
                    <td></td>
                    <td>Kgs</td>
                    <td><input id="kilogramos" class="span1" type="text" /></td>
                </tr> 

                <tr>
                    <td>Prioridad</td>
                    <td><select id="prioridad" class="span2">
                            <option value="1">Alta</option> 
                            <option value="2" selected>Media</option> 
                            <option value="3">Baja</option> 
                         </select> 
                    </td>


                    <td></td>
                    <td>Piezas</td>
                    <td><input id="piezas" class="span1" type="text" /></td>
                </tr> 

                <tr>
                    
                    <td></td>
                    
                </tr> 
     </table>
</div>




<?php } else {?>
<script>
    bloquear_guardar();
</script>
<?php } ?>

<script>
    
    var clientes = [
      <?=$js_cliente;?>
  ];
  
    $('#cam_cliente').autocomplete({lookup: clientes,onSelect: function (suggestion) {}});
    
    
<?php if($editar_reserva){?>
    
    
    $(function() {
        
       
        $('.editable').click(function() {
        
            
            
            $('#in_editar').val($(this).html());
            $('#in_editar').attr("pk",$(this).attr("data-pk"));
            $('#in_editar').attr("name",$(this).attr("data-name"));
            
            $('#popover_falso').show();
            
        });   
        
        $('.editable-cancel').click(function() {
            $('#popover_falso').hide();
        });
        
        $('#btn_enviar').click(function() {
            enviar_pop_falso();
        });
    
    });
    var name,pk,valor;
    function enviar_pop_falso(){
        
        var entidad     = "reserva";
        name            = $('#in_editar').attr("name");
        pk              = $('#in_editar').attr("pk");
        valor           = $('#in_editar').val();
        
        if((valor != null) && (valor != undefined)){
            var request = $.ajax({
                url: '<?=base_url()?>index.php/calendario/reserva/editar',
                data: { 
                        name:name,
                        pk: pk,
                        value: valor,        
                        entidad: entidad
                        },
                type: "POST",
                dataType: "html"
            });
            request.done(function( msg ) {            
                if(msg==-1){
                    alert(men_1); // Faltan datos
                }else if(msg==-5){
                     alert(men_10); // No es el autor  
                }else if(msg==1){
                     $('a[data-pk='+pk+'][data-name='+name+']').html(valor);
                     $('#popover_falso').hide();
                     cal_act();                
                }else{
                    //alert(men_4); // Cap erronea
                    $('#msg_popover').html(msg);                    
                }
                
            });
            request.fail(function( jqXHR, textStatus ) {
                alert( men_2 );
            });
       }
    }
    
    
<?php } ?>


<?php if($crear_reserva){?>
function insertar_reserva(){
      
        id_ov       = $('#id_ov').val();
        prioridad   = $('#prioridad').val();
        capacidad   = $('#capacidad').val();
        piezas      = $('#piezas').val();
        kilogramos  = $('#kilogramos').val();
        fecha       = hora_TS2;
        
        
            if( 
            ((id_ov!="" && id_ov!="undefined"))             && ((prioridad!="" && prioridad!="undefined"))  &&  
            ((capacidad!="")&&(capacidad!="undefined"))     && ((piezas!="")&&(piezas!="undefined"))  &&  
            ((kilogramos!="")&&(kilogramos!="undefined"))   && ((fecha!="")&&(fecha!="undefined"))  
            )     
           {
            var request = $.ajax({
                //url: "<?=base_url(); ?>index.php/calendario/reserva/insertar/"+id_ov+"/"+prioridad+"/"+capacidad+"/"+piezas+"/"+kilogramos+"/"+fecha,
                url: "<?=base_url(); ?>index.php/calendario/reserva/insertar/",
                data: { 
                        id_ov: id_ov, 
                        prioridad: prioridad,
                        capacidad: capacidad,
                        piezas: piezas,
                        kilogramos: kilogramos,
                        fecha: fecha
                        },
                type: "POST",
                dataType: "html"
            });
            request.done(function( msg ) {            
                if(msg==-1){
                    alert(men_1); // Faltan datos
                }else if(msg==-3){
                    alert(men_4); // Cap erronea
                }else if(msg==-4){
                    alert(men_9); //Fecha erronea    
                }else if(msg==1){
                    //alert(men_3); // Exito
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


   
    function cambiar_ov(){
        opcion = $('#cam_cliente').val();
        var request = $.ajax({
            url: "<?=base_url(); ?>index.php/calendario/reserva/crear_combo_ov/",
            type: "POST",
            data: { id_cliente:opcion },
            dataType: "html"
        });
        request.done(function( msg ) {            
            $( "#combo_ov" ).html( msg ); 
        });
        request.fail(function( jqXHR, textStatus ) {
            alert( men_2 );
        });
    }
    
    function cambiar_datos_ov(){
        id_ov       = $('#id_ov option:selected').val();
        var request = $.ajax({
            url: "<?=base_url(); ?>index.php/calendario/reserva/generar_datos_ov/"+id_ov,
            type: "POST",
            dataType: "html"
        });
        request.done(function( msg ) {             
            var dataJson = eval(msg);
            for(var i in dataJson){
                $( "#tipo_ov" ).html( dataJson[i].tipo_nombre ); 
                $( "#kilogramos" ).val( dataJson[i].kilogramos ); 
                $( "#capacidad" ).val( dataJson[i].capacidad );
                $( "#piezas" ).val( dataJson[i].piezas ); 
		}
        });
        request.fail(function( jqXHR, textStatus ) {
            alert( men_2 );
        });
    }
    
 <?php if($eliminar_reserva){?>   
    function eliminar_reserva(id_reserva){
        if(id_reserva!=null){
            
            var conf = confirm(men_8);
            
            if(conf){
                var request = $.ajax({
                    url: "<?=base_url(); ?>index.php/calendario/reserva/eliminar/",
                    type: "POST",
                    data: { id_reserva: id_reserva},
                    dataType: "html"
                });
                request.done(function( msg ) {             
                    
                    if(msg=="1"){
                        //alert( men_7 );
                        cal_act();
                        tabla_act();
                        historial_act();
                        $('#myModal').modal('hide');   
                        //$("#res_"+id_reserva).css("display","none");
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

</script>