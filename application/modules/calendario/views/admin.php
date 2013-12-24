
        
<h6 class="txtcentrado" id="sub_modal">Bloquear horario</h6>
<div id="msje"></div>
<center>
<table id="admin_cal">
             <tr>
                <td>Desde</td>
                <td>
                    <div id="datetimepicker1" class="time_picker input-append date">
                        <input type="text" name="ini" id="ini" value="<?=$fecha_ini;?>"></input>
                        <span class="add-on">
                          <i data-time-icon="icon-time" data-date-icon="icon-calendar"></i>
                        </span>
                    </div>              
                </td>
             </tr>   
                <!-- td>&nbsp;&nbsp;</td -->
             <tr>   
                <td>Hasta</td>
                <td>
                    <div id="datetimepicker2" class="time_picker input-append date">
                        <input type="text" name="fin" id="fin" value="<?=$fecha_ter;?>"></input>
                        <span class="add-on">
                          <i data-time-icon="icon-time" data-date-icon="icon-calendar"></i>
                        </span>
                    </div> 
                </td>
            </tr>
 </table>   
            <center>
                <h6>Comentario</h6>
            <textarea class="span4" name="mensaje" id="mensaje" ></textarea>  
            <input type="hidden" id="id_planta" value="<?=$id_planta?>">
            </center>
 
</center>


<script>

$(function() {
    
        $( ".time_picker" ).datetimepicker({ 
            language: 'es',
            format: 'dd/MM/yyyy hh:mm',
            pickSeconds: false         // disables seconds in the time picker
            
        });
        
});
 
</script>    