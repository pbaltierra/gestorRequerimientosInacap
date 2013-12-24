<div id="mantenedor_btns">
<?php if(isset($btn))  echo $btn ;     ?>   
<?php if(isset($btn2))  echo $btn2 ;     ?>   
</div>
<div id="nvo_planta" >
    <?php
    $attributes = array('class' => 'form-horizontal');
    echo form_open('administracion/index/insertar/planta/', $attributes)
    ?>
    <?=$msg;?>
    <table class="table table-bordered table-hover table-condensed table-striped">
    <thead>
        <tr>
        <th>Campo</th><th>Valor</th></tr>
    </thead>
    <tbody>
        <tr>
            <td>Nombre</td><td><input required type="text" name="nombre" /></td></tr>
        
        <tr>       
        
        <tr>
            <td>C&oacute;digo</td><td><input required type="text" name="codigo" /></td></tr>
        
        <tr>
        <tr>
            <td>Canal</td><td><input type="text" name="canal" /></td></tr>
        
        <tr>
        
            <td>Direcci&oacute;n</td><td><input type="text" name="direccion" /></td></tr>
        <tr>
            <td>Direcci&oacute;n secundaria</td><td><input type="text" name="direccion2" /></td></tr>
        <tr>            
            <td></td><td><input type="hidden" name="id_planta" value="<?=$id_planta?>"><input value="Guardar" class="btn btn-primary" type="submit" /></td></tr>
    </tbody>
    </form>
    </table>
</div>

