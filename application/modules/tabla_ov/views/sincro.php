<div id="sincro">    
    <center>
    <table>
        <tbody>
            <tr>
                <td>Desde</td>
                <td style="width: 20px;">&nbsp;</td>
                <td>
                    <input type="text" name="ini" disabled id="ini" value="<?=$fecha_ini;?>"></input>                        
                </td>    
                              
            </tr> 
            
            <tr>
                <td>Hasta</td>
                <td style="width: 20px;">&nbsp;</td>
                <td>
                    <div id="datetimepicker1" class="time_picker input-append date">
                        <input type="text" name="fin" id="fin" value="<?=$fecha_ter;?>"></input>
                        <span class="add-on">
                          <i data-time-icon="icon-time" data-date-icon="icon-calendar"></i>
                        </span>
                        
                    </div>  
                    
                </td>
            </tr> 
            <tr>
                <td>
                    * Valores incluyentes
                </td>
            </tr>
       </tbody>     
    </table>
    </center>    
</div>


<script>

$(function() {
    
        $( ".time_picker" ).datetimepicker({ 
            language: 'es',
            format: 'dd/MM/yyyy',
            pickTime: false         // disables seconds in the time picker
            
        });
        
});
 
</script>    