    


    <div id="modal_recordar"  class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="" aria-hidden="true" style="z-index: 5000;">
        <div class="modal-header2">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h6>Recordar clave</h6>
        </div>
        <div class="modal-body2">
            <center>
                <div id="div_alert" class="alert" style="display:none;">
                            <!-- button type="button" class="close" data-dismiss="alert">&times;</button-->
                            <strong>¡Atenci&oacute;n! </strong>
                            <div id="msg_error"></div>
                </div>
                <p>Ingrese el email asociado a su cuenta</p>
                <p><input type="email" nombre="email" id="email"></p>
            </center>
        </div>
        <div class="modal-footer2">
            <button class="btn" data-dismiss="modal" aria-hidden="true">Cerrar</button>
            <button id="env_modal" class="btn btn-primary">Enviar</button>
        </div>
    </div>


<script>
$(function() {
   $("#env_modal").click(function(){ enviar_form(); });
   
   $("#rec_clave").click(function(){    ocultar_msj();     });

});   

function ocultar_msj(){
    $("#div_alert").css("display","none");
}


function enviar_form(){
    var email = $("#email").val();
    var request = $.ajax({
        url: '<?=base_url()?>index.php/recordar_clave/recordar/gen_nva_cla',
        type: "POST",
        dataType: "json",
        data: { email:email }
        });
    request.done(function( msg ) {                
        if(msg.cod==1){
            $( "#div_alert" ).addClass( "alert-success" );
        }else{
            $( "#div_alert" ).removeClass( "alert-success" );
        }
        $("#msg_error").html(msg.res);
        $("#div_alert").css("display","block");
    });
    request.fail(function( jqXHR, textStatus ) {
        alert( "Request failed: " + textStatus );
    });
}

</script>