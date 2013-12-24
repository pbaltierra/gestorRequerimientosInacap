
<form name="frm_reporte" action="<?=base_url();?>index.php/reporte/generar_reporte/crear" method="POST">
    <div>
        <?=$tabla_fil_hor;?>
    </div>
    <div>
        <?=$tabla_fil_ver;?>
    </div>    
    <div id="cont_actualizar">
        
        <a class="btn btn-success btn-small pull-right" onclick="enviar_formulario();" href="javascript:void(0);" id="btn_generar">Generar</a>
        
        <a class="btn btn-success btn-small pull-right" onclick="buscar_info();" href="javascript:void(0);" id="btn_generar" style="margin-right: 5px;">Buscar</a>
        <!-- input type="submit"-->
    </div>
</form>
<div class="well well-small">Resultados</div>
<div id="div_resultados">
    
</div>    


<script>

$(function(){
   

  var clientes = [
      <?=$js_clientes;?>
    /*  
    { value: 'Afghan afghani', data: 'AFN' },
    { value: 'Albanian lek', data: 'ALL' },
    { value: 'Algerian dinar', data: 'DZD' },
    { value: 'European euro', data: 'EUR' },
    { value: 'Angolan kwanza', data: 'AOA' }
    */
  ];
  
  
  var ovs = [
      <?=$js_ovs;?>
  ];
  
  var ovs_sap = [
      <?=$js_ovs_sap;?>
  ];
  
  
  $('#cliente').autocomplete({
    lookup: clientes,
    onSelect: function (suggestion) {
      /*
      var thehtml = '<strong>Currency Name:</strong> ' + suggestion.value + ' <br> <strong>Symbol:</strong> ' + suggestion.data;
      $('#outputcontent').html(thehtml);
      */
    }
  });
  
  $('#id_ov').autocomplete({
    lookup: ovs,
    onSelect: function (suggestion) {
     
    }
  });
  
  $('#ov').autocomplete({
    lookup: ovs_sap,
    onSelect: function (suggestion) {     
    }
  });
  
  
    $( ".time_picker" ).datetimepicker({ 
            language: 'es',
            format: 'dd/MM/yyyy hh:mm',
            //pickTime: false
        }            
    );
  
  
  
});
  

function buscar_info(){
    var id_planta       = $('#id_planta').val();
    var id_tipo         = $('#id_tipo').val();
    var cliente         = $('#cliente').val();
    var ov              = $('#ov').val();
    var id_ov           = $('#id_ov').val();
    var des_prog_input  = $('#des_prog_input').val();
    var has_prog_input  = $('#has_prog_input').val();
    var des_crea_input  = $('#des_crea_input').val();
    var has_crea_input  = $('#has_crea_input').val();
    var des_entr_input  = $('#des_entr_input').val();
    var has_entr_input  = $('#has_entr_input').val();
    
    var request = $.ajax({
        url: "<?=base_url();?>index.php/reporte/generar_reporte",
        type: "POST",
        data: { 
                id_planta : id_planta,
                id_tipo : id_tipo,        
                cliente : cliente,
                ov : ov,
                id_ov : id_ov,
                des_prog_input : des_prog_input,
                has_prog_input : has_prog_input,
                des_crea_input : des_crea_input,
                has_crea_input : has_crea_input,
                des_entr_input : des_entr_input,
                has_entr_input : has_entr_input
                },
        dataType: "html"
    });
    request.done(function( msg ) {
        
        
        $( "#div_resultados" ).html( msg );
        //on_ready();
        
        //on_ready();
    });
    request.fail(function( jqXHR, textStatus ) {
        alert( "Request failed: " + textStatus );
    });
}


function enviar_formulario(){
    document.frm_reporte.submit(); 
}


</script>
