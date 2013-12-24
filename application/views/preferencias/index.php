<div class="container">
   <div class="well well-small" style="margin-bottom: 10px">Preferencias</div>
   
   <div>
       <?=$tabla;?>
   </div>
</div>

<!-- Modal -->
<!-- div id="myModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        
        <h6 id="myModalLabel">Reservas de capacidad </h6>
    </div>
    <div class="modal-body"></div>
    <div class="modal-footer">
        <button class="btn" data-dismiss="modal" aria-hidden="true" >Cerrar</button>
        <a id="btn_eliminar" class="btn btn-danger hide" onclick="" >Eliminar</a>

        <a id="btn_guardar" class="btn btn-primary" onclick="insertar_reserva();" >Guardar</a>
    </div>
</div-->


<script>
$(function() {
    
        /*
        $('.editable').editable(
            {
            type: 'text',
            url: '<?=base_url()?>index.php/mantenedor/mantenedor/editar',
            params: {entidad:'<?=$entidad;?>'},
            validate: function(value) {
                        if($.trim(value) == '') return 'Valor requerido';
                      },            
            success: function(data) {},
            error: function(data) {                    
                    // actions on validation error (or ajax error) 
                    var msg = '';
                    if(data.errors) { //validation error
                        $.each(data.errors, function(k, v) { msg += k+": "+v+"<br>"; });
                    } else if(data.responseText) { //ajax error
                        msg = data.responseText;
                    }
                }
            }
        );
        */
       
       
       $('.editable').editable(
            {
            type: 'text',
            url: '<?=base_url()?>index.php/mantenedor/mantenedor/editar',
            emptytext: 'VACIO',
            mode:'inline',
            showbuttons:false,
            //  display:false, 
            
                //function(value, response) {
                        //alert(response);
                  //  },
                  
            params: {entidad:'<?=$entidad;?>'},
            validate: function(value) {
                        if($.trim(value) == '') return 'Valor requerido';
                      },            
            success: function(data) {
                    //alert(data);
                    
            },
            error: function(data) {                    
                    // actions on validation error (or ajax error) 
                    var msg = '';
                    if(data.errors) { //validation error
                        $.each(data.errors, function(k, v) { msg += k+": "+v+"<br>"; });
                    } else if(data.responseText) { //ajax error
                        msg = data.responseText;
                    }
                }
            }
        );
       
       
       
});

</script>


<?=$cambiar_pass;?>