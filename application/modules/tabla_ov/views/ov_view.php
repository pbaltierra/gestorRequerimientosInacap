<div id="ov_plantilla">    
    <table id="ov_provisoria" align="center">
            <tr>
                <td>Cliente</td>
                <td style="width: 20px;">&nbsp;</td>
                <td>
                    <!-- input id="cam_cliente" class="span3" <?=$deshabilitar;?> type="text" value="<?=$cliente;?>" /-->
                    <?=$txt_cliente;?>
                </td>
                
            </tr> 
            
            <tr><td>Vigas</td>
                <td style="width: 20px;">&nbsp;</td>
                <td><input id="cam_capacidad" class="span2" <?=$deshabilitar;?> type="text" value="<?=$capacidad;?>" /></td>
            </tr> 
            
            <tr>
                <td>Kgs</td>
                <td style="width: 20px;">&nbsp;</td>
                <td><input id="cam_kilogramos" class="span2" <?=$deshabilitar;?> type="text" value="<?=$kilogramos;?>" /></td>
            </tr> 
            
            <tr>
                <td>Piezas</td>
                <td style="width: 20px;">&nbsp;</td>
                <td><input id="cam_piezas" class="span2" <?=$deshabilitar;?> type="text" value="<?=$piezas;?>" /></td>
            </tr> 
            
            <tr>
                <td>Fecha de entrega</td>
                <td style="width: 20px;">&nbsp;</td>
                <td>
                    <div id="datetimepicker4" class="time_picker input-append date">
                        <input id="datepicker_ov" type="text" <?=$deshabilitar;?> value="<?=$fecha_format;?>" name="fecha_entrega">
                        <span class="add-on">
                            <i class="icon-calendar" data-date-icon="icon-calendar" data-time-icon="icon-time"></i>
                        </span>
                    </div>
                </td>
            </tr>  
            <tr>
                <td>Comentario</td>
                <td style="width: 20px;">&nbsp;</td>
                <td><textarea id="cam_comentario" maxlength="150" <?=$deshabilitar;?> class="span3"><?=$comentario;?></textarea></td>
            </tr> 
            <tr><?=$combo_sap;?>
                <!--
                <td>Enlazar con SAP</td>
                <td style="width: 20px;">&nbsp;</td>
                <td><?//=$ov_sap?></td>
                -->
            </tr> 
    </table>
    
    
</div>

<script>
    
        
  var clientes = [
      <?=$js_cliente;?>
  ];
    
    
    $( ".time_picker" ).datetimepicker({ 
            language: 'es',
            format: 'dd/MM/yyyy',
            pickTime: false
        }            
    );
    
  $('#cam_cliente').autocomplete({lookup: clientes,onSelect: function (suggestion) {}});
    
 </script>   