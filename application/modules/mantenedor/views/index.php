<div id="mantenedor_btns">
<?php if(isset($btn2)) echo $btn2 ;   ?>      
<?php if(isset($btn)) echo $btn ;   ?>
  
<!-- a class="btn btn-success btn-small" href="javascript:void(0);" id="btn_crear">Crear</a--></div>
<form style="display:none" id="form_id_planta" method="post" action="<?=base_url();?>index.php/administracion/index/crear/<?=$entidad;?>" ><input type="hidden" name="id_planta" value="<?=$id_planta?>" /></form>
<div id="mantenedor_edit" >
    <?=$tabla_html;?>
</div>

<script>
    
var valor_edi;    
$(function() {
        /*
        
        */
        $('.editable').editable(
            {
            type: 'text',
            url: '<?=base_url()?>index.php/mantenedor/mantenedor/editar',
            emptytext: 'VACIO',
            mode:'inline',
            showbuttons:false,
            autotext:'never',
            display:false,
            
                    ////function(value, response) {
                    //    alert(value);
                    //},
                  
            params: {entidad:'<?=$entidad;?>'},
                
            validate: function(value) {
                        if($.trim(value) == '') return 'Valor requerido';
                        valor_edi = value;                        
                      },            
            success: function(data) {
                    if(data!="1"){
                        alert(data);
                        //$(this).text('value' + '$');
                        //userModel.set('id_sap', newValue); //update backbone model
                    }else{
                        if($(this).attr("data-name")!="clave"){
                           $(this).text(valor_edi);                            
                        }else{
                            
                        }                        
                    }        
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
        
        $('.editable').on( "click", function() {
             if($(this).attr("data-name")=="clave"){
                $('.input-sm').attr('type','password');
                //alert('click');
             }
        });
        
        $( ".plantas" ).on( "click", function() {
            act_plantas();            
        });
        
        $( ".tipo_usu" ).on( "click", function() {
            act_permisos();            
        });
        
        $( "#btn_crear" ).on( "click", function() {
            var frm_element = document.getElementById ('form_id_planta');   
            frm_element.submit();
        });
        
});


function act_permisos(){
    //var searchIDs = $("input:checkbox:checked").map(function(){
    var searchIDs = $(".tipo_usu:checked").map(function(){    
      return $(this).val();
    }).get(); // <----
    
    
    var pk = $('.editable').attr("data-pk")
    //console.log(pk);
    
    tipos = searchIDs ;
    
            var request = $.ajax({
                            url: '<?=base_url()?>index.php/mantenedor/mantenedor/act_tipos_usu',
                            type: "POST",
                            data: { tipos:tipos, pk:pk }
                            //dataType: "html"
                            });
            request.done(function( msg ) {                
                //console.log(msg);
            });
            request.fail(function( jqXHR, textStatus ) {
                alert( "Request failed: " + textStatus );
            });
}


function act_plantas(){
    //var searchIDs = $("input:checkbox:checked").map(function(){
    var searchIDs = $(".plantas:checked").map(function(){
      return $(this).val();
    }).get(); // <----
    
    
    var pk = $('.editable').attr("data-pk")
    //console.log(pk);
    
    plantas = searchIDs ;
    
            var request = $.ajax({
                            url: '<?=base_url()?>index.php/mantenedor/mantenedor/act_plantas',
                            type: "POST",
                            data: { plantas:plantas, pk:pk }
                            //dataType: "html"
                            });
            request.done(function( msg ) {
                
                //console.log(msg);
            });
            request.fail(function( jqXHR, textStatus ) {
                alert( "Request failed: " + textStatus );
            });
}












var msje_eliminar = new Array();
msje_eliminar[0] = "Hubo un error, intentalo más tarde";
msje_eliminar[1] = "Registro eliminado satisfactoriamente";
msje_eliminar[2] = "¿Deseas eliminar el registro";

function eliminar(entidad, id){
    confirmar=confirm(msje_eliminar[2]); 
    if (confirmar){ 
        if( (entidad!="")   && (id!="") ){
        
            var request = $.ajax({
                            url: '<?=base_url()?>index.php/mantenedor/mantenedor/eliminar',
                            type: "POST",
                            data: { id : id, entidad:entidad },
                            //dataType: "html"
                            });
            request.done(function( msg ) {

                switch(msg){
                    case "1": msje = msje_eliminar[1];break;
                    case "0": msje = msje_eliminar[0];break;
                    default : msje = msje_eliminar[0];break;    
                }

                alert(msje);
                if(msg=="1"){
                    location.reload();
                }
                //console.log("msg");
            });
            request.fail(function( jqXHR, textStatus ) {
                alert( "Request failed: " + textStatus );
            });
        }
    }else{ 
    
    }  
    
}


    
</script>    