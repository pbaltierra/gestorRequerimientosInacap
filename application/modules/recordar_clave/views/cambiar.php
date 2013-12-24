    


    <div id="modal_cambiar" class="modal hide fade" tabindex="-2" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-header2">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h6>Cambiar clave</h6>
        </div>
        <div class="modal-body2">
            <center>
                <div id="div_alert" class="alert" style="display:none;">
                            <!-- button type="button" class="close" data-dismiss="alert">&times;</button-->
                            <strong>¡Atenci&oacute;n! </strong>
                            <div id="msg_error"></div>
                </div>
                <p>Clave actual</p>
                <p><input type="password" nombre="pass0" id="pass0"></p>
                <p>Clave nueva</p>
                <p><input type="password" nombre="pass1" id="pass1"></p>
                <p>Reingrese clave nueva</p>
                <p><input type="password" nombre="pass2" id="pass2"></p>
            </center>
        </div>
        <div class="modal-footer2">
            <button class="btn" data-dismiss="modal" aria-hidden="true">Cerrar</button>
            <button id="env_modal" class="btn btn-primary">Enviar</button>
        </div>
    </div>


<script>
$(function() {
    
   $("#env_modal").click(function(){        cambiar_clave();    });
   
   $("#btn_cambiar_clave").click(function(){    ocultar_msj();     });
   

}); 

function ocultar_msj(){
   $("#div_alert").css("display","none"); 
}

function cambiar_clave(){
    
    var pass0 = $("#pass0").val();
    var pass1 = $("#pass1").val();
    var pass2 = $("#pass2").val();
    
    var request = $.ajax({
        url: '<?=base_url()?>index.php/recordar_clave/cambiar/cam_cla',
        type: "POST",
        dataType: "json",
        data: { pass1:pass1, pass2:pass2, pass0:pass0 }
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