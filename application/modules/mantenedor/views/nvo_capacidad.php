<div id="mantenedor_btns">
<?php if(isset($btn2))  echo $btn2 ;     ?>  
</div>
<div id="nvo_capacidad" >
    <?php
    $attributes = array('class' => 'form-horizontal');
    echo form_open('administracion/index/insertar/capacidad/'.$id_planta, $attributes)
    ?>
    <?=$msg;?>
    <table class="table table-bordered table-hover table-condensed table-striped">
    <thead>
        <tr>
        <th>Campo</th><th>Valor</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>Planta</td><td><?=$nom_planta?></td></tr>
        
        <tr>
            <td>Fecha de inicio</td><td><input id="datepicker" required type="text" name="fecha" /></td></tr>
        <tr>
            <td>Capacidad</td><td><input required type="text" name="capacidad" /></td></tr>
        <tr>            
            <td></td><td><input type="hidden" name="id_planta" value="<?=$id_planta?>"><input value="Guardar" class="btn btn-primary" type="submit" /></td></tr>
    </tbody>
    </form>
    </table>
</div>




<script>
$(function() {
    $( "#datepicker" ).datepicker({ 
        dateFormat: "dd/mm/yy"        
    });     
});

</script>