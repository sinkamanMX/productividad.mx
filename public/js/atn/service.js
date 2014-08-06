var map = null;
var geocoder;
var infoWindow;
var infoLocation;
var markers = [];
var bounds;
var arrayTravels="";
var mon_timer=60;
var startingOp=false;
var aSelected=Array();

$( document ).ready(function() {
	$('#tabs').tab();
	initMapToDraw();
	$('#slideTimeUp').slider({
		formater: function(value) {
			mon_timer = value;
			timerUpdate()
			return 'Actualizar cada ' + value + ' segs.';			
		},
		value: 60
	}).on('slide', function(ev){
		var valorSegs = ev.value;
		mon_timer = valorSegs;
		timerUpdate()
    	$("#labelMinutes").html(valorSegs);
  	});


	$('#divSliderC').hide('fast'); 
	$("#countdown").hide('fast'); 	
});

function timerUpdate(){
	$("#countdown").show('slow');
	$('#divSliderC').show('slow');
	if(mon_timer>0){
		$("#countdown").countdown360({
		    radius: 30,
		    seconds: 20,
		    label: ['seg', 'segs'],
		    fontColor: '#FFFFFF',
		    autostart: false,
		    onComplete: function () {
		      mapLoadData()
		    }		
		}).addSeconds(mon_timer);
	}else{
		$("#countdown").countdown360({
		    radius: 30,
		    seconds: 20,
		    label: ['seg', 'segs'],
		    fontColor: '#FFFFFF',
		    autostart: false,
		    onComplete: function () {
		      mapLoadData()
		    }		
		}).stop();
	}
}

function initMapToDraw(){
	infoWindow = new google.maps.InfoWindow;
    var mapOptions = {
      zoom: 5,
      center: new google.maps.LatLng(24.52713, -104.41406),
      mapTypeId: google.maps.MapTypeId.ROADMAP
    };
	map = new google.maps.Map(document.getElementById('Map'),mapOptions);
	
	bounds = new google.maps.LatLngBounds();
}

function mapClearMap(){
	if(markers || markers.length>-1){
		for (var i = 0; i < markers.length; i++) {
	          markers[i].setMap(null);
		}	
		markers = [];
	}
	arrayTravels=null;
}

function drawSelectPersonal(idValue){
	stopTimer();
	$('#inputTecnico').find('option').remove().end().append('<option value="-1">Todos</option>');
	$("#divTecnicosSelect").html("");	
	var datapersonal = $("#divDataPersonal").html().split("?");
	for(var i=0;i<datapersonal.length;i++){
		var datainfo = datapersonal[i].split("|");
		
		if(datainfo[2] == idValue){
			$('#inputTecnico').find('option').end().append('<option value="'+datainfo[0]+'">'+datainfo[1]+'</option>');
			$('#divTecnicosSelect').append('<div class=""><input type="checkbox" class="chkMap" name="inputChk'+datainfo[0]+'" id="inputChk'+datainfo[0]+'" value="'+datainfo[0]+'" onChange="searchSelected(this.value)"/>'+datainfo[1]+'</div>');
		}
	}
}

function stopTimer(){
	$("#countdown").hide('slow');
	$('#divSliderC').hide('slow');
	arrayTravels= [];
	mapClearMap();
}

function searchSelected(strSearch){
	if(aSelected.length>0){
		var existe = jQuery.inArray(strSearch, aSelected);
		if(existe<0){
			aSelected.push(strSearch);
		}else{
			aSelected.splice(existe,1);	
		}
	}else{
		aSelected.push(strSearch);
	}
	

	mapLoadData();
}

/*
function optionAll(){

}

function unselectedAll(){
	aSelected = [];
	$( '.chkMap' ).attr( 'checked', $( this ).is( ':checked' ) ? 'checked' : '' );
}*/

function mapLoadData(){
	mapClearMap();
	if(aSelected.length>0){
		var idObject = $("#inputId").val();		
		$.ajax({
			type: "POST",
	        url: "/atn/services/getlastp",
			data: { strInput: aSelected},
	        success: function(datos){	        	
				var result = datos;
				if(result!= ""){
					arrayTravels = result.split('!');
					printTravelsMap();
				}
	        }
		});
	}else{
		stopTimer()
	}
}
function printTravelsMap(){	
	for(var i=0;i<arrayTravels.length;i++){
		var travelInfo = arrayTravels[i].split('|');
	    var content     = '';
	    var markerTable = null;
	    if(travelInfo[3]!="null" && travelInfo[4]!="null"){
	    	var latitude  = travelInfo[3]; 
	    	var longitude = travelInfo[4]; 

	    	content='<table width="350" class="table-striped" ><tr><td align="right"><b>Evento</b></td><td width="200" align="left">'+travelInfo[2]+'</td><tr>'+
	    			'<tr><td align="right"><b>Fecha</b></td><td align="left">'+travelInfo[1]+' </td><tr>'+	    			
	    			'<tr><td align="right"><b>Velocidad</b></td><td align="left">'+travelInfo[5]+' kms/h.</td><tr>'+
	    			'<tr><td align="right"><b>Bateria</b></td><td align="left">'+travelInfo[6]+' %</td><tr>'+
	    			'<tr><td align="right"><b>Tipo GPS</b></td><td align="left">'+travelInfo[7]+' kms/h.</td><tr>'+
	    			'<tr><td align="right"><b>Ubicación</b></td><td align="left">'+travelInfo[9]+'</td><tr>'+
	    			'</table>';	    	
			markerTable = new google.maps.Marker({
				map: map,
				position: new google.maps.LatLng(latitude,longitude),
				title: 	travelInfo[1],
				icon: 	'/images/carMarker.png'
			});
			markers.push(markerTable);
			infoMarkerTable(markerTable,content);	    	
	    }
	}
	
	fitBoundsToVisibleMarkers();
	timerUpdate();	
}

function fitBoundsToVisibleMarkers() {
	if(markers.length>0){
	    for (var i=0; i<markers.length; i++) {
			bounds.extend( markers[i].getPosition() );
	    }
	    if(markers.length==1){
			map.setZoom(13);
		  	map.panTo(markers[0].getPosition());
	    }else{
			map.fitBounds(bounds);
	    }
	}
}

function infoMarkerTable(marker,content){	
    google.maps.event.addListener(marker, 'click',function() {
      if(infoWindow){infoWindow.close();infoWindow.setMap(null);}
      var marker = this;
      var latLng = marker.getPosition();
      infoWindow.setContent(content);
      infoWindow.open(map, marker);
      map.setZoom(13);
	  map.setCenter(latLng); 
	  map.panTo(latLng);     
	});
}
