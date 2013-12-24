<div >
    <a class="btn btn-success btn-small btns_datepicker" href="javascript:void(0);" id="cal_prev_semana" onclick="cal_mover('semana','atras','<?=$fecha;?>')"><<</a> 
    <a class="btn btn-success btn-small btns_datepicker" href="javascript:void(0);" id="cal_prev_dia" onclick="cal_mover('dia','atras','<?=$fecha;?>')"     ><  </a>    
    
    <div id="datetimepicker3" class="time_picker_cal input-append date">
        <input type="text" name="datepicker" id="datepicker" value="<?=$fecha_format;?>"></input>
        <span class="add-on">
            <i data-time-icon="icon-time" data-date-icon="icon-calendar"></i>
        </span>
    </div> 
    
    <a class="btn btn-success btn-small btns_datepicker" href="javascript:void(0);" id="cal_sgte_dia" onclick="cal_mover('dia','sgte','<?=$fecha;?>')"      >>  </a>
    <a class="btn btn-success btn-small btns_datepicker" href="javascript:void(0);" id="cal_sgte_semana" onclick="cal_mover('semana','sgte','<?=$fecha;?>')"   >>> </a> 
    
</div>



<div id="calendario_contenedor">
    <?=$col_horas?>
    <?=$cols_dias?>
</div>
<div id="leyenda"><?=$leyenda;?></div> 

<!-- Calendario ************************************************* -->

<script>
    
    var hora_TS2    = 0;
    var men_1       = "Por favor ingresa los datos solicitados";
    var men_2       = "Hubo un error, por favor inténtelo más tarde";
    var men_3       = "Solicitud satisfactoria";
    var men_4       = "Capacidad errónea";
    var men_5       = "Bloqueo satisfactorio";
    var men_6       = "Desbloqueo satisfactorio";
    var men_7       = "Registro eliminado";
    var men_8       = "¿Desea eliminar este registro?";
    var men_9       = "Fecha incorrecta";
    var men_10      = "No posee los permisos necesarios";
    var men_11      = "Cliente desconocido";
    
    function bloquear_guardar(){
        $('#btn_guardar').css("display","none");
        $('#btn_guardar').removeAttr("onclick");
    }
    
 function cal_act(){
    var fecha_ori = $( "#datepicker" ).val();    
    var n = fecha_ori.split("/"); 
    //console.log(n);
    
    
    if(n.length == 3){
        var request = $.ajax({
            url: "<?=base_url();?>index.php/calendario/index_dma/"+n[0]+"/"+n[1]+"/"+n[2],
            type: "POST",
            //data: { id : tiempo_num },
            dataType: "html"
        });
        request.done(function( msg ) {
            $( "#cal_ajax" ).html( msg );
            on_ready();
        });
        request.fail(function( jqXHR, textStatus ) {
            alert( "Request failed: " + textStatus );
        });
        request = null;
    }
    
}

function cal_mover(tiempo,direccion,fecha){
    tiempo_num = 0;
    switch(tiempo){
        case "dia"      :   tiempo_num = 1; desplazamiento = "-=61";    break;
        case "semana"   :   tiempo_num = 7; desplazamiento = "-=427";     break;
        case "mes"      :   tiempo_num = 30;    break;
        default         :   tiempo_num = 1;     break;       
    }
    
    if(direccion=="atras"){
        tiempo_num *= -1;
        desplazamiento = "+=61";
        if(tiempo =="semana"){
            desplazamiento = "+=427";
        }
    }
    
    var request = $.ajax({
        url: "<?=base_url();?>index.php/calendario/index/"+tiempo_num+"/"+fecha,
        type: "POST",
        //data: { id : tiempo_num },
        dataType: "html"
    });
    request.done(function( msg ) {
        

        $( "#cal_ajax" ).html( msg );
        on_ready();
        
        //on_ready();
    });
    request.fail(function( jqXHR, textStatus ) {
        alert( "Request failed: " + textStatus );
    });
   request = null;
}


function on_ready(){
    /*
    $( "#datepicker" ).datepicker({ 
        dateFormat: "dd/mm/yy"        
    });
    */
       
    $( ".time_picker_cal" ).datetimepicker({ 
            language: 'es',
            format: 'dd/MM/yyyy',
            pickTime: false
        }            
    );
    $('.time_picker_cal').datetimepicker().on('changeDate', function(){
            cal_act();
            //$('.bootstrap-datetimepicker-widget').css("display","none")
            $('.bootstrap-datetimepicker-widget').hide();
            /*
            if(  $('.bootstrap-datetimepicker-widget').css("display")!= "none"   ){
                
            }
            */
    });
    $(".popover-link").popover({});    
}

$(function() {
    on_ready();
    //on_ready_2();
});

function handleEnter(inField, e) {
    var charCode;
        
    if(e && e.which){
        charCode = e.which;
    }else if(window.event){
        e = window.event;
        charCode = e.keyCode;
    }

    if(charCode == 13) {
        cal_act();
        //alert("Enter was pressed on " + inField.id);
    }
}
    
    function activar_modal_blo(hora_for, hora_TS, id_bloc){
         $('#btn_eliminar').css("display","none"); 
         
         $('#btn_guardar').removeAttr("onclick");          
         $('#btn_guardar').css("display","none")
        
         if((id_bloc=="") || (id_bloc == "undefined")    ){
            id_bloc = null;
         }
         
         if(id_bloc == null){
            //$('#btn_guardar').attr("onclick","bloquear_horas("+hora_TS+");");
         }else{
            // $('#btn_guardar').attr("onclick","desbloquear_horas("+id_bloc+");");
            
         }
        var titulo = "Horario bloqueado";
        
        $('#myModalLabel').html( titulo + "," + hora_for + " "   );
        hora_TS2 = hora_TS;
        //$('#myModal').modal('show');
        
        var request = $.ajax({
            url: "<?=base_url(); ?>index.php/calendario/calendario/bloquear/",
            type: "POST",
            data: { hora:hora_TS, id_bloc:id_bloc },
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
    
// Admin    
        
     function activar_modal_2(hora_for, hora_TS, id_bloc){
          $('#btn_eliminar').css("display","none"); 
          $('#btn_guardar').css("display","inline-block");
          
         if((id_bloc=="") || (id_bloc == "undefined")    ){
            id_bloc = null;
         }
         
         if(id_bloc == null){
            $('#btn_guardar').html("Bloquear"); 
            $('#btn_guardar').attr("onclick","bloquear_horas("+hora_TS+");");
         }else{
            $('#btn_guardar').html("Desbloquear");  
            $('#btn_guardar').attr("onclick","desbloquear_horas("+id_bloc+");");           
         }
        var titulo = "Editar hora";
        
        $('#myModalLabel').html( titulo + "," + hora_for + " "   );
        hora_TS2 = hora_TS;
        //$('#myModal').modal('show');
              
        
        var request = $.ajax({
            url: "<?=base_url(); ?>index.php/calendario/calendario/bloquear/",
            type: "POST",
            data: { hora:hora_TS, id_bloc:id_bloc },
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
    
    function bloquear_horas(hora_TS){
        
        ini         = $('#ini').val();
        fin         = $('#fin').val();
        id_planta   = $('#id_planta').val();
        mensaje     = $('#mensaje').val();
        
        //console.log(ini+" "+fin);
        var request = $.ajax({
            url: "<?=base_url(); ?>index.php/calendario/calendario/bloquear/"+hora_TS,
            type: "POST",
            data: { ini:ini, fin:fin,id_planta:id_planta, mensaje:mensaje },
            dataType: "html"
        });
        request.done(function( msg ) {            
            if(msg == 1){
                //alert(men_5);
            }else{
                alert( men_2 );
            }
            //$( ".modal-body" ).html( msg );            
            $('#myModal').modal('hide');
            cal_act();
        });
        request.fail(function( jqXHR, textStatus ) {
            alert( men_2 );
        });
    }
    
    function desbloquear_horas(id_bloc){
               
        //console.log(ini+" "+fin);
        var request = $.ajax({
            url: "<?=base_url(); ?>index.php/calendario/calendario/desbloquear/",
            type: "POST",
            data: { id_bloc: id_bloc },
            dataType: "html"
        });
        request.done(function( msg ) {            
            if(msg == 1){
                alert(men_6);
            }else{
                alert( men_2 );
            }
            //$( ".modal-body" ).html( msg );            
            $('#myModal').modal('hide');
            cal_act();
        });
        request.fail(function( jqXHR, textStatus ) {
            alert( men_2 );
        });
    }
    
    function activar_modal(hora_for, hora_TS, cap_pla){
        $('#btn_eliminar').css("display","none"); 
        $('#btn_guardar').html("Guardar");
        $('#btn_guardar').css("display","inline-block");
        $('#btn_guardar').attr("onclick","insertar_reserva();");
        var titulo = "Reservas de capacidad";
        
        $('#myModalLabel').html( titulo + "," + hora_for + " "   );
        hora_TS2 = hora_TS;
        var request = $.ajax({
            url: "<?=base_url(); ?>index.php/calendario/reserva/index/"+hora_TS+"/"+cap_pla,
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
</script>