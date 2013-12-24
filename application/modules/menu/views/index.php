              <ul class="nav">
                <li><a href="<?=base_url();?>">Dashboard</a></li>
                
                <?php if($ver_mant){  // Validacion simple?>
                <li class="dropdown">
                  <a href="#" class="dropdown-toggle" data-toggle="dropdown">Administraci&oacute;n<b class="caret"></b></a>
                  <ul class="dropdown-menu">
                        <li><a href="<?=base_url();?>index.php/administracion/index/usuario">Mantenedor de usuarios</a></li>
                        <?php if($ver_mant_plan) {?><li><a href="<?=base_url();?>index.php/administracion/index/planta">Mantenedor de plantas</a></li><?php } ?>
                        <?php if($ver_mant_cap) {?><li><a href="<?=base_url();?>index.php/administracion/index/planta/<?=$id_planta;?>">Mantenedor de capacidad</a></li><?php } ?>
                        <li><a href="<?=base_url();?>index.php/administracion/index/calendario">Mantenedor de calendario</a></li>
                        <li><a href="<?=base_url();?>index.php/preferencias/index/">Preferencias de sistema</a></li>
                  </ul>
                </li>
                <?php } ?>
                
                <?php if($ver_esta){  // Validacion simple?>
                <li class="dropdown">
                  <a href="#" class="dropdown-toggle" data-toggle="dropdown">Estad&iacute;sticas<b class="caret"></b></a>
                  <ul class="dropdown-menu">
                        <li><a href="<?=base_url();?>index.php/estadisticas/graficos">Gr&aacute;ficos</a></li>
                        <li><a href="<?=base_url();?>index.php/estadisticas/reportes">Reportes</a></li>
                  </ul>
                </li>
                <?php } ?>
              </ul>

                <div class="pull-right barraInfo">
                  <a id="btnSalir" href="<?=base_url();?>index.php/login/logout" class="btn btn-success btn-small">Salir</a>
                </div>
                <ul class="nav pull-right">
                    <li class="dropdown">
                      <a href="#" class="dropdown-toggle" data-toggle="dropdown"><?=$nombre;?><b class="caret"></b></a>
                     
                      <ul class="dropdown-menu">
                          <li><a id="btn_cambiar_clave" href="#modal_cambiar" data-toggle="modal">Cambiar clave</a></li>
                           <?=$menu_tipos;?>
                      </ul>
                    </li>
                    
                    <li class="dropdown">
                      <a href="#" class="dropdown-toggle" data-toggle="dropdown">Plantas (<?=$nom_planta?>)<b class="caret"></b></a>
                      <?=$menu_plantas;?>
                    </li>
                    <li>
                        
                    </li>    
                </ul>
                  
