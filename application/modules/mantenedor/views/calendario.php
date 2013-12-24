<div id="mantenedor_calendario">
    <div id="cal_ajax" >
    <?=$calendario;?>
    </div>
    
    <div><?=$tabla_tipos;?></div>
    <div id="cont_actualizar"><a class="btn btn-success btn-small pull-right" onclick="actualizar_bloques();" href="javascript:void(0);" id="btn_actualizar">Guardar</a></div>
</div>

<!-- Modal -->
<div id="myModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        
        <h6 id="myModalLabel"></h6>
    </div>
    <div class="modal-body"></div>
    <div class="modal-footer">
        <button class="btn" data-dismiss="modal" aria-hidden="true">Cerrar</button>
        <a id="btn_guardar" class="btn btn-primary" onclick="insertar_reserva();" >Guardar</a>
    </div>
</div>
<script>
    $(function() {
        $('.color').colorpicker();
        get_colores("color_texto");
        get_colores("color_fondo");
    });        
    
    function get_colores(tipo){
        limite = 7;
         
        for(i=0;i<=limite;i++){
            color_txt = $('#color_'+i+"_"+tipo).val();
            //console.log(color_txt);
            $('#muestra_'+i+"_"+tipo).css("background-color", color_txt);
        }
    }
    
    function actualizar_bloques(){
        
            color_fondo_1 = $('#color_1_color_fondo').val();
            color_fondo_2 = $('#color_2_color_fondo').val();
            color_fondo_3 = $('#color_3_color_fondo').val();
            color_fondo_4 = $('#color_4_color_fondo').val();
            color_fondo_5 = $('#color_5_color_fondo').val();
            color_fondo_6 = $('#color_6_color_fondo').val();
            color_fondo_7 = $('#color_7_color_fondo').val();
            
            color_texto_1 = $('#color_1_color_texto').val();
            color_texto_2 = $('#color_2_color_texto').val();
            color_texto_3 = $('#color_3_color_texto').val();
            color_texto_4 = $('#color_4_color_texto').val();
            color_texto_5 = $('#color_2_color_texto').val();
            color_texto_6 = $('#color_3_color_texto').val();
            color_texto_7 = $('#color_4_color_texto').val();
            
    
            var request = $.ajax({
                            url: '<?=base_url()?>index.php/mantenedor/mantenedor/actualizar_bloques',
                            type: "POST",
                            data: { cf_1: color_fondo_1, cf_2: color_fondo_2,
                                    cf_3: color_fondo_3, cf_4: color_fondo_4,
                                    cf_5: color_fondo_5, cf_6: color_fondo_6,
                                    cf_7: color_fondo_7, ct_1: color_texto_1,
                                    ct_2: color_texto_2, ct_3: color_texto_3,
                                    ct_4: color_texto_4, ct_5: color_texto_5,
                                    ct_6: color_texto_6, ct_7: color_texto_7                                    
                                  }
                            //dataType: "html"
                            });
            request.done(function( msg ) {
                if(msg == 1){
                    alert("Actualizacion satisfactoria");
                    location.reload();
                }
                
                //console.log(msg);
            });
            request.fail(function( jqXHR, textStatus ) {
                alert( "Request failed: " + textStatus );
            });
    }
</script>    