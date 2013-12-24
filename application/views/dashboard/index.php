<div class="container">
    <div class="well well-small" style="margin-bottom: 10px">Dashboard</div>
    <!-- h6 class='titulo'>Dashboard </h6 -->
    <h6 class='fecha'><?=$fecha_hoy?></h6>
    <div id="cal_ajax">
        <?=$calendario;?>
    </div>
    <div id="tabla_ov_ajax">
    <?=$tabla_ov;?>
    </div>
    <!-- div class = "tabla_historial well"-->
     <div id="tabla_historial_ajax">   
       <?=$historial;?> 
     </div>   
        <!-- h6>Historial de actividades</h6>
        <table id="historial" class="table table-bordered table-hover" >
            <tr> 
                <th> Fecha </td>
                <th> Autor </td>
                <th> Actividad </td>
                <th> Reportar </td>                
            </tr>
            <tr> 
                <td> 15/09/2013 </td>
                <td> Juan P&eacute;rez </td>
                <td> Cre&oacute; la orden de venta #b92851</td>
                <td style="text-align: center">  <img src="<?= base_url(); ?>/assets/img/aut.png"></img></td>
                
            </tr>
            <tr> 
                <td> 14/09/2013 </td>
                <td> Juan P&eacute;rez </td>
                <td> Cre&oacute; la reserva de capacidad #12851 </td>
                <td style="text-align: center">  <img src="<?= base_url(); ?>/assets/img/aut.png"></img></td>
                
            </tr>
                
        </table-->
    <!-- /div-->
</div>

<!-- Modal -->
<div id="myModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
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
</div>

<?=$cambiar_pass;?>

