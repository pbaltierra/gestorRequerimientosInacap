/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */







/*
//Muestra latitud y longitud
var lat = 0;
var lon = 0;
var x=document.getElementById("xy");
var tecDistancia = new Array();
var tecTiempo = new Array();
var tecTiempoMinutos = new Array();
var tpoDestino = new Array();

var iconA = "https://cdn1.iconfinder.com/data/icons/Map-Markers-Icons-Demo-PNG/32/Map-Marker-Push-Pin--Right-Chartreuse.png";
var iconB = "https://cdn1.iconfinder.com/data/icons/Map-Markers-Icons-Demo-PNG/32/Map-Marker-Push-Pin--Right-Azure.png";
var iconC = "https://cdn1.iconfinder.com/data/icons/Map-Markers-Icons-Demo-PNG/32/Map-Marker-Push-Pin--Right-Pink.png";

var geocoder, map, marker;

var destinationIcon = 'https://chart.googleapis.com/chart?chst=d_map_pin_letter&chld=D|FF0000|000000';
var originIcon = 'https://chart.googleapis.com/chart?chst=d_map_pin_letter&chld=O|FFFF00|000000';





function showLatLon(){
    //x.innerHTML="Latitud: "+ lat + ", Longitud: " + lon;
    calculateDistances();
}

function callback(response, status) {
  if (status != google.maps.DistanceMatrixStatus.OK) {
    alert('Error was: ' + status);
  } else {
    var origins = response.originAddresses;
    var destinations = response.destinationAddresses;
    var outputDiv = document.getElementById('outputDiv');
    outputDiv.innerHTML = '';
    deleteOverlays();

    for (var i = 0; i < origins.length; i++) {
      var results = response.rows[i].elements;
      //addMarker(origins[i], false);
      for (var j = 0; j < results.length; j++) {
        //addMarker(destinations[j], true);
            //outputDiv.innerHTML += origins[i] + ' to ' + destinations[j]
            //+ ': ' + results[j].distance.text + ' in '
            //+ results[j].duration.text + '<br>';
            
            tecDistancia[i]     = results[j].distance.text;
            tecTiempo[i]        = results[j].duration.text;
            
                
            tecTiempoMinutos[i]= results[j].duration.value/60;
            
            //console.log(tecTiempoMinutos[i]);
            
            //outputDiv = document.getElementById('rutaDistancia'+i);
            //outputDiv.innerHTML = tecDistancia[i];
            
            //outputDiv = document.getElementById('rutaTiempo'+i);
            //outputDiv.innerHTML = tecTiempo[i];
            
            //outputDiv = document.getElementById('tiempoDestino'+i);

            
            //outputDiv.innerHTML = redondeo(tecTiempoMinutos[i]+tpoTermino[i]) + " min   ";
            calculaTiempoDestino();
            tecTiempoMinutos = reordena(tecTiempoMinutos);
            tecDistancia    = reordena(tecDistancia);
            tecTiempo       = reordena(tecTiempo);
            
            tpoTermino      = reordena(tpoTermino);
            id_interno      = reordena(id_interno);
            nombre          = reordena(nombre);
            estado          = reordena(estado);
            tipo            = reordena(tipo);
            
            //tpoDestino = reordena(tpoDestino);
            //tpoDestino = reordena(tpoDestino);
            tpoDestino = reordena(tpoDestino);
            calculaTiempoDestino();
            llenaTabla();
            
            
      }
    }
  }
  
  //imprimeArreglo(tpoDestino);
}

function calculaTiempoDestino(){
    for(i=0;i<tecDistancia.length;i++){
        tpoDestino[i]  = redondeo(tecTiempoMinutos[i]+tpoTermino[i]);
    }
}

function llenaTabla(){
    for(i=0;i<tpoDestino.length;i++){
        outputDiv = document.getElementById('id_interno'+i);        
        outputDiv.innerHTML = id_interno[i];
        
        outputDiv = document.getElementById('nombre'+i);
        outputDiv.innerHTML = nombre[i];
        
        outputDiv = document.getElementById('estado'+i);
        outputDiv.innerHTML = estado[i];
        
        outputDiv = document.getElementById('tipo'+i);
        outputDiv.innerHTML = tipo[i];
        
        outputDiv = document.getElementById('tpo_termino'+i);
        outputDiv.innerHTML = tpoTermino[i] + " min   ";
        
        outputDiv = document.getElementById('rutaDistancia'+i);
        outputDiv.innerHTML = tecDistancia[i];
        
        outputDiv = document.getElementById('rutaTiempo'+i);
        outputDiv.innerHTML = tecTiempo[i];
            
        outputDiv = document.getElementById('tiempoDestino'+i);
        outputDiv.innerHTML = tpoDestino[i] + " min   ";        
    }
}

function reordena(arregloOrdenado){
    temporal = "";
    for(i=0;i<tpoDestino.length;i++){
        for(j=i+1;j<tpoDestino.length;j++){
            if(tpoDestino[i]>tpoDestino[j]){
                temporal = arregloOrdenado[i];
                arregloOrdenado[i] = arregloOrdenado[j];
                arregloOrdenado[j] = temporal;
            }
        }
    }
    return arregloOrdenado;    
}

function imprimeArreglo(arreglo){
    for(i=0;i<arreglo.length;i++){
        console.log(arreglo[i]);
    }
}


function addMarker(location, isDestination) {
  var icon;
  if (isDestination) {
    icon = destinationIcon;
  } else {
    icon = originIcon;
  }
  geocoder.geocode({'address': location}, function(results, status) {
    if (status == google.maps.GeocoderStatus.OK) {
      bounds.extend(results[0].geometry.location);
      map.fitBounds(bounds);
      var marker = new google.maps.Marker({
        map: map,
        position: results[0].geometry.location,
        icon: icon
      });
      markersArray.push(marker);
    } else {
      //alert('Geocode was not successful for the following reason: '+ status);
    }
  });
}

function deleteOverlays() {
  for (var i = 0; i < markersArray.length; i++) {
    markersArray[i].setMap(null);
  }
  markersArray = [];
}

function redondeo(numero)
{
    var original=parseFloat(numero);
    var result=Math.round(original*1)/1;
    return result;
}

*/