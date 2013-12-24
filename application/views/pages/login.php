<div class="container">
    
    
    
    
    <?php
    $attributes = array('class' => 'form-horizontal');
    echo form_open('login/validar', $attributes)
    ?>

    <div class="row" id="home_logIn">
        <div class="span2" >&nbsp;</div>
        <div class="span8" >&nbsp;
            <div class="row-fluid" >  
                <div class="span6" >
                    <div><legend>Ingreso</legend></div>
                    <?=$msgError;?>
                    <div class="row-fluid" style="margin-bottom: 5px">
                        <div class="span6"><img src="<?= base_url(); ?>/assets/img/user.png"><small>Usuario</small></div>
                        <div class="span6"><input class="span12" name="nickname" type="text" placeholder=""> </div>
                    </div>

                    <div class="row-fluid">
                        <div class="span6"><img src="<?= base_url(); ?>/assets/img/key.png"><small>Contrase&ntilde;a</small></div>
                        <div class="span6"><input class="span12" name="clave" type="password" placeholder=""> </div>
                    </div>
                    
                    <div><small></small></div>
                    <legend style="margin-bottom: 10px; margin-top: 10px"></legend>
                    <div class="row-fluid">
                        <div class="span6"><a id="rec_clave" href="#modal_recordar" data-toggle="modal"><small>¿Ha olvidado su contraseña?</small></a></div>
                        <div class="span6" style="text-align:right"><button type="submit" class="btn btn-success right" >Ingresar</button> </div>
                    </div>
                </div>
                <div class="span6" id="home_titulo" ><h3>Planificaci&oacute;n Inteligente</h3></div>
            </div>   

        </div>
        <div class="span2" >&nbsp;</div>
    </div>    

</form>

</div>  

<?=$mod_recordar;?>

