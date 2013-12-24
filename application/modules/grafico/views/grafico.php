<div class="well well-small">



    <div>
        <?=$tabla_fil_hor1;?>
    </div>
    <div id="graph_container1" style="min-width: 310px; height: 400px; margin: 0 auto"></div>
    <div id="datos_graph1">
        <?=$tabla_fil_ver1;?>
    </div>

    
</div>


<div class="well well-small">
    <div>
        <?=$tabla_fil_hor2;?>
    </div>
    <div id="graph_container2" style="min-width: 310px; height: 400px; margin: 0 auto"></div>
    <div id="datos_graph2">
        <?=$tabla_fil_ver2;?>
    </div>
</div>  


<div class="well well-small">
    <div>
        <?=$tabla_fil_hor3;?>
    </div>
    <div id="graph_container3" style="min-width: 310px; height: 400px; margin: 0 auto"></div>
    <div id="graph_container4" style="min-width: 310px; height: 400px; margin: 0 auto"></div>
    <div id="datos_graph3">
        <?=$tabla_fil_ver3;?>
    </div>
</div>  


<div class="well well-small">
    <div>
        <?=$tabla_fil_hor5;?>
    </div>
    <div id="graph_container5" style="min-width: 310px; height: 400px; margin: 0 auto"></div>
    <!--div id="graph_container4" style="min-width: 310px; height: 400px; margin: 0 auto"></div-->
    <div id="datos_graph5">
        <?=$tabla_fil_ver5;?>
    </div>
</div>  


<script src="<?=base_url();?>assets/highcharts/highcharts.js"></script>
<script src="<?=base_url();?>assets/highcharts/exporting.js"></script>
<script>
var nom_vendedor, nom_cliente;

var color_con ='#2f7ed8';
var color_pro ='#8bbc21';
var color_dis ='yellow';
var color_anu ='#AA4643';


$(function(){
   
  var chart;
  
   
  var clientes = [
      <?=$js_clientes;?>
  ];
  
  
  var vendedores = [
      <?=$js_vendedores;?>
  ];
  
    
  $('#id_cliente').autocomplete({lookup: clientes,onSelect: function (suggestion) {}});
  $('#id_vendedor').autocomplete({lookup: vendedores,onSelect: function (suggestion) {}});
  $('#id_vendedor5').autocomplete({lookup: vendedores,onSelect: function (suggestion) {}});
  
  
    $( ".time_picker" ).datetimepicker({ 
            language: 'es',
            format: 'dd/MM/yyyy hh:mm',
            //pickTime: false
        }            
    );
  
    crear_grafico(1);
    crear_grafico(2);
    crear_grafico3();
    crear_grafico5();
  
});


function crear_grafico(tipo){
     var cod = tipo;   
     var por_confirmadas    = parseFloat($('#v_1_'+cod).attr('data-valor'));  
     var por_noconfirmadas  = parseFloat($('#v_2_'+cod).attr('data-valor'));  
     var por_anuladas       = parseFloat($('#v_3_'+cod).attr('data-valor')); 
     
     var titulo = "Confirmación de órdenes provisorias";
     
     //if(nom_vendedor != "" && nom_vendedor != undefined){ titulo += " - " + nom_vendedor;  }
     //if(nom_cliente != "" && nom_cliente != undefined){ titulo += " - " + nom_cliente;  }
     
     
    //console.log(por_confirmadas+" , "+por_noconfirmadas+" , "+por_anuladas);
    // Build the chart
        $('#graph_container'+cod).highcharts({
            
            colors: [
                color_con,
                color_pro,
                color_anu
            ],
            
            chart: {
                plotBackgroundColor: null,
                plotBorderWidth: null,
                plotShadow: false
            },
            title: {
                text: titulo
            },
            tooltip: {
        	    pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
            },
            plotOptions: {
                pie: {
                    allowPointSelect: true,
                    cursor: 'pointer',
                    dataLabels: {
                        enabled: false
                    },
                    showInLegend: true
                }
            },
            series: [{
                type: 'pie',
                //name: 'Browser share',
                data: [
                    ['Confirmadas',         por_confirmadas],
                    ['No confirmadas',      por_noconfirmadas],
                    ['Anuladas',            por_anuladas]
                    /*
                    {
                        name: 'Chrome',
                        y: 12.8,
                        sliced: true,
                        selected: true
                    },
                        ['Safari',    8.5],
                        ['Opera',     6.2],
                    */
                ]
            }]
        });
}



function crear_grafico3(){
    
    
    
    
     var arr_col = Array();
     arr_col    = $('#arr_col').attr('data-col'); 
     arr_col    = arr_col.split("|");
     
     var arr_ley = Array();
     arr_ley    = $('#arr_ley').attr('data-col'); 
     arr_ley    = arr_ley.split("|");
     
     var arr_cap_dia    = Array();
     arr_cap_dia    = $('#arr_cap_dia').attr('data-col'); 
     arr_cap_dia    = arr_cap_dia.split("|");
          
     var arr_vig_con    = Array();
     arr_vig_con    = $('#arr_vig_con').attr('data-col'); 
     arr_vig_con    = arr_vig_con.split("|");
     
     var arr_vig_pro    = Array();
     arr_vig_pro    = $('#arr_vig_pro').attr('data-col'); 
     arr_vig_pro    = arr_vig_pro.split("|");
     
     
     //console.log(arr_vig_pro);
     
     
     var arr_cap_dis    = Array();
     arr_cap_dis    = $('#arr_cap_dis').attr('data-col'); 
     arr_cap_dis    = arr_cap_dis.split("|");     
     
     for(i=1;i<arr_vig_con.length;i++){
            arr_cap_dia[i] = parseFloat(arr_cap_dia[i]);
            arr_vig_con[i] = parseFloat(arr_vig_con[i]);
            arr_cap_dis[i] = parseFloat(arr_cap_dis[i]);
            arr_vig_pro[i] = parseFloat(arr_vig_pro[i]);
     }
     
     
     
     //console.log(arr_cap_dia);
     
     
     
     /*
     var por_confirmadas    = parseFloat($('#v_1_'+cod).attr('data-valor'));  
     var por_noconfirmadas  = parseFloat($('#v_2_'+cod).attr('data-valor'));  
     var por_anuladas       = parseFloat($('#v_3_'+cod).attr('data-valor')); 
     */
     var titulo     = "Distribución de capacidad por tipo de pedido";
     var titulo2    = "Cantidad de vigas programadas por tipo de pedido";
     
    
    // Build the chart
        $('#graph_container3').highcharts({
            
             colors: [
                color_con,
                
                color_anu,
                color_pro
            ],
            chart: {
                type: 'area'
            },
            title: {
                text: titulo
            },
            subtitle: {
                //text: 'Source: Wikipedia.org'
            },
            xAxis: {
                //categories: ['1750', '1800', '1850', '1900', '1950', '1999', '2050'],
                categories: arr_col,
                tickmarkPlacement: 'on',
                title: {
                    enabled: false
                }
            },
            yAxis: {
                title: {
                    text: 'Vigas'
                },
                labels: {
                    formatter: function() {
                        return this.value;
                    }
                }
            },
            tooltip: {
                shared: true,
                headerFormat: '<span style="font-size:10px">{point.key}</span><table>',        
                pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
                    '<td style="padding:0"><b>{point.y:.1f} vigas</b></td></tr>',
                footerFormat: '</table>',        
                valueSuffix: ' vigas',
                useHTML: true
            },
            plotOptions: {
                area: {
                    stacking: 'normal',//'percent',//normal
                    lineColor: '#666666',
                    lineWidth: 1,
                    marker: {
                        lineWidth: 1,
                        lineColor: '#666666'
                    }
                }
            },
            series: [
            
            {
                name: arr_ley[1],
                data: arr_vig_con
                //color: color_con
            }, {
                name: arr_ley[2],
                data: arr_vig_pro
                //color: color_pro
            }, {
                name: arr_ley[3],
                data: arr_cap_dis
                //color: color_dis
            }]
        });
        
        
        
        $('#graph_container4').highcharts({
             colors: [
                color_con,
                color_anu,
                color_pro
                
            ],
            chart: {
                type: 'column'
            },
            title: {
                text: titulo2
            },
            subtitle: {
                //text: 'Source: WorldClimate.com'
            },
            xAxis: {
                categories: arr_col
            },
            yAxis: {
                min: 0,
                title: {
                    text: 'Vigas'
                }
            },
            tooltip: {
                
                headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
                pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
                    '<td style="padding:0"><b>{point.y:.1f} vigas</b></td></tr>',
                footerFormat: '</table>',
                shared: true,
                useHTML: true
            },
            plotOptions: {
                column: {
                    pointPadding: 0.2,
                    borderWidth: 0
                }
            },
            series: [
            /*
            {
                name: 'Tokyo',
                data: [49.9, 71.5, 106.4, 129.2, 144.0, 176.0, 135.6, 148.5, 216.4, 194.1, 95.6, 54.4]
    
            },*/ 
            {
                name: arr_ley[1],
                data: arr_vig_con                
                
            }, {
                name: arr_ley[2],
                
                data: arr_vig_pro
    
            }, {
                name: arr_ley[3],
                data: arr_cap_dis
                
    
            }
            ]
        });
        //console.log(arr_vig_con);
        
        
}

function crear_grafico5(){
     var arr_col = Array();
     arr_col    = $('#arr_col5').attr('data-col'); 
     arr_col    = arr_col.split("|");
     
     var arr_ley = Array();
     arr_ley    = $('#arr_ley5').attr('data-col'); 
     arr_ley    = arr_ley.split("|");
     
     var arr_cap_dia    = Array();
     arr_cap_dia    = $('#arr_ovs_pro').attr('data-col'); 
     arr_cap_dia    = arr_cap_dia.split("|");
          
     var arr_vig_con    = Array();
     arr_vig_con    = $('#arr_ovs_con').attr('data-col'); 
     arr_vig_con    = arr_vig_con.split("|");
     
     var arr_vig_pro    = Array();
     arr_vig_pro    = $('#arr_ovs_nco').attr('data-col'); 
     arr_vig_pro    = arr_vig_pro.split("|");
         
     var arr_cap_dis    = Array();
     arr_cap_dis    = $('#arr_ovs_anu').attr('data-col'); 
     arr_cap_dis    = arr_cap_dis.split("|");     
     
     for(i=1;i<arr_vig_con.length;i++){
            arr_cap_dia[i] = parseFloat(arr_cap_dia[i]);
            arr_vig_con[i] = parseFloat(arr_vig_con[i]);
            arr_cap_dis[i] = parseFloat(arr_cap_dis[i]);
            arr_vig_pro[i] = parseFloat(arr_vig_pro[i]);
     }
     
     var titulo = "Distribución de órdenes provisorias";
    
      
        $('#graph_container5').highcharts({
             colors: [
                color_con,
                color_anu,
                color_pro
                
            ],
            chart: {
                type: 'column'
            },
            title: {
                text: titulo
            },
            subtitle: {
                //text: 'Source: WorldClimate.com'
            },
            xAxis: {
                categories: arr_col
            },
            yAxis: {
                min: 0,
                title: {
                    text: 'Ordenes'
                }
            },
            tooltip: {
                
                headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
                pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
                    '<td style="padding:0"><b>{point.y:.1f} &oacute;rdenes</b></td></tr>',
                footerFormat: '</table>',
                shared: true,
                useHTML: true
            },
            plotOptions: {
                column: {
                    pointPadding: 0.2,
                    borderWidth: 0
                }
            },
            series: [
            /*
            {
                name: 'Tokyo',
                data: [49.9, 71.5, 106.4, 129.2, 144.0, 176.0, 135.6, 148.5, 216.4, 194.1, 95.6, 54.4]
    
            },*/ 
            {
                name: arr_ley[1],
                data: arr_vig_con                
                
            }, {
                name: arr_ley[2],
                data: arr_vig_pro
    
            }, {
                name: arr_ley[3],
                data: arr_cap_dis
                
    
            }
            ]
        });
        //console.log(arr_vig_con);
}
  

function act_info(tipo){
    
     var id_planta       = $('#id_planta'+tipo).val();
    
    //console.log(id_planta);
    
    var id_vendedor     = $('#id_vendedor').val();
    var id_cliente      = $('#id_cliente').val();
    
    if(tipo==2){
        var id_vendedor     = null;
    }else if (tipo==1){
        var id_cliente      = null;
    }
    var des_prog_input  = $('#des_prog_input'+tipo).val();
    var has_prog_input  = $('#has_prog_input'+tipo).val();
    
    var request = $.ajax({
        url: "<?=base_url();?>index.php/grafico/act_datos/"+tipo,
        type: "POST",
        data: { 
                id_planta : id_planta,
                id_vendedor : id_vendedor,        
                id_cliente : id_cliente, 
                des_prog_input : des_prog_input,
                has_prog_input : has_prog_input
                
                },
        dataType: "html"
    });
    request.done(function( msg ) {
        if(id_vendedor != null) {   nom_vendedor    = id_vendedor;  };
        if(id_cliente != null)  {   nom_cliente     = id_cliente;  };
        
        $( "#datos_graph"+tipo ).html(msg);
        crear_grafico(tipo);
        
    });
    request.fail(function( jqXHR, textStatus ) {
        alert( "Request failed: " + textStatus );
    });
}


function act_info3(){
     var tipo="3";
     var id_planta       = $('#id_planta'+tipo).val();
    
    var des_prog_input  = $('#des_prog_input'+tipo).val();
    var has_prog_input  = $('#has_prog_input'+tipo).val();
    
    var request = $.ajax({
        url: "<?=base_url();?>index.php/grafico/act_datos/"+tipo,
        type: "POST",
        data: { 
                id_planta : id_planta,
                des_prog_input : des_prog_input,
                has_prog_input : has_prog_input
                
                },
        dataType: "html"
    });
    request.done(function( msg ) {        
        $( "#datos_graph"+tipo ).html(msg);
        crear_grafico3();
        
    });
    request.fail(function( jqXHR, textStatus ) {
        alert( "Request failed: " + textStatus );
    });
}


function act_info5(){
    var tipo="5";
    var id_planta       = $('#id_planta'+tipo).val();
    var id_vendedor     = $('#id_vendedor'+tipo).val();
    var des_prog_input  = $('#des_prog_input'+tipo).val();
    var has_prog_input  = $('#has_prog_input'+tipo).val();
    
    var request = $.ajax({
        url: "<?=base_url();?>index.php/grafico/act_datos/"+tipo,
        type: "POST",
        data: { 
                id_planta : id_planta,
                id_vendedor: id_vendedor,        
                des_prog_input : des_prog_input,
                has_prog_input : has_prog_input
                
                },
        dataType: "html"
    });
    request.done(function( msg ) {        
        $( "#datos_graph"+tipo ).html(msg);
        crear_grafico5();        
    });
    request.fail(function( jqXHR, textStatus ) {
        alert( "Request failed: " + textStatus );
    });
}



function enviar_formulario(){
    document.frm_reporte.submit(); 
}


</script>
