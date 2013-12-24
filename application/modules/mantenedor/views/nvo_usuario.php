<div id="mantenedor_btns">

<?php if(isset($btn2))  echo $btn2 ;     ?>    </div>
<div id="nvo_usuario" >
    <?php
    $attributes = array('class' => 'form-horizontal');
    echo form_open('administracion/index/insertar/usuario', $attributes)
    ?>
    <?=$msg;?>
    <table class="table table-bordered table-hover table-condensed table-striped">
    <thead>
        <tr>
        <th>Campo</th><th>Valor</th></tr>
    </thead>
    <tbody>
        <tr>
            <td>Usuario</td><td><input type="text" name="id_sap" required /></td></tr>
        <tr>
            <td>Clave</td><td><input type="password" name="clave" required/></td></tr>
        <tr>
            <td>Email</td><td><input type="email" name="email" required/></td></tr>
        <tr>
            
            <td>Nombre</td><td><input type="text" name="nombre" /></td></tr>
        <tr>
            <td>A.Paterno</td><td><input type="text" name="ape_paterno" /></td></tr>
        <tr>
            <td>A.Materno</td><td><input type="text" name="ape_materno" /></td></tr>
        <tr>
            <td>Planta</td><td><?=$combo_plantas?></td></tr>
        <tr>
         <tr>
            <td>Perfil</td><td><?=$combo_tipos_usu?></td></tr>
         <!-- tr>  
            <td>Usuario SAP</td><td><input type="text" name="id_sap" /></td></tr>
        <tr-->
            <td></td><td><input value="Guardar" class="btn btn-primary" type="submit" /></td></tr>
    </tbody>
    </form>
    </table>
</div>

