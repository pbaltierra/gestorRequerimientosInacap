
        
<h6 class="txtcentrado" id="sub_modal"></h6>
<div id="msje"></div>
<center>
<table id="admin_cal">
            <tr>
                <td>Desde</td>
                <td>
                        <input type="text" disabled class="span3" value="<?=$fecha_inicio?>" />                 
                </td>
            </tr>
            <tr>
                <!-- td>&nbsp;&nbsp;</td -->
                <td>Hasta</td>
                <td>    <input type="text" disabled class="span3" value="<?=$fecha_termino?>" /> 
                </td>
            </tr> 
 </table>   
            <center>
            <textarea class="span4" name="mensaje" id="mensaje" disabled><?=$mensaje?></textarea>  
            <input type="hidden" id="id_planta" value="<?=$id_planta?>">
            </center>
 
</center>


<script>
    /*
$(function() {
        $( ".time_picker" ).timepicker({ 
            dateFormat: "dd/mm/yy",
            timeText: "Tiempo",
            hourText:"Hora",
            secondText: "Segundos",
            closeText: "Aceptar",
            currentText: "Actual",
            timeFormat: "HH"
        });
});
   */ 
</script>    