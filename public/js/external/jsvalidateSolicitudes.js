var map = null;
var infoWindow;
var markers = [];
var bounds;

var geocoder      = new google.maps.Geocoder();
var markerTable   = null;
var directionsDisplay;
var directionsService = new google.maps.DirectionsService();


$().ready(function(){
    if($("#inputLugar").val()==""){
        if (navigator.geolocation){
            var content = document.getElementById("geolocation-test");
            if (navigator.geolocation){
                navigator.geolocation.getCurrentPosition(function(objPosition){
                    var lon = objPosition.coords.longitude;
                    var lat = objPosition.coords.latitude;

                    codeLatLng(lat,lon,0);
                    $("#inputLatitud").val(lat);
                    $("#inputLontigud").val(lon);
                    printPoints();
                }, function(objPositionError){
                    bError = true;
                    switch (objPositionError.code){
                        case objPositionError.PERMISSION_DENIED:
                            bError = false;
                        break;
                        case objPositionError.POSITION_UNAVAILABLE:
                            bError = false;
                        break;
                        case objPositionError.TIMEOUT:
                            bError = false;
                        break;
                        default:
                            bError = false;
                            //content.innerHTML = "Error desconocido.";
                    }

                    if(bError){

                    }
                },{
                    maximumAge: 75000,
                    timeout: 15000
                });
            }
        }        
    }

    var nowTemp = new Date();
    var now = new Date(nowTemp.getFullYear(), nowTemp.getMonth(), nowTemp.getDate(), 0, 0, 0, 0);
    var dateInter  = parseInt(nowTemp.getMonth())+1;  
    var todayMonth = (dateInter<10) ? "0"+dateInter : dateInter;
    var inDate     = nowTemp.getDate() + 1;
    var todayDay   = (inDate<10) ? "0"+inDate: inDate;           

    if($("#inputFechaIn").val()==""){
      $("#inputFechaIn").val(nowTemp.getFullYear()+"-"+todayMonth+"-"+todayDay);      
    }

    var checkin = $('#inputFechaIn').datetimepicker({
        format: "yyyy-mm-dd",
        showMeridian: false,
        autoclose: true,
        todayBtn: true,
        minView: 2,
        startDate: $("#inputFechaIn").val(),
    });

  $("#FormData").validate({
        rules: {
            inputFechaIn    :      "required",    
            inputTipo       :      "required",    
            inputUnidad     :      "required",    
            inputComment    :      "required",
            inputHorario    :      "required",
            infoUnit        :      "required",
            inputLugar      :      "required"
        },
        messages: {                          
            inputFechaIn    :      "Campo Requerido",        
            inputTipo       :      "Campo Requerido",        
            inputUnidad     :      "Campo Requerido",        
            inputComment    :      "Campo Requerido",
            inputHorario    :      "Campo Requerido",
            infoUnit        :      "Selecciona una unidad",
            inputLugar      :      "Campo Requerido"
        },
        submitHandler: function(form) {
            form.submit();
        }
    }); 

    $('.dataTable').dataTable( {
        "sDom": "<'row'<'span6'l><'span6'f>r>t<'row'<'span6'i><'span6'p>>",
        "sPaginationType": "bootstrap",
        "bDestroy": true,
        "bLengthChange": false,
        "bPaginate": true,
        "bFilter": true,
        "bSort": true,
        "bJQueryUI": true,
        "iDisplayLength": 10,      
        "bProcessing": true,
        "bAutoWidth": true,
        "bSortClasses": false,
          "oLanguage": {
              "sInfo": "Mostrando _TOTAL_ registros (_START_ a _END_)",
              "sEmptyTable": "Sin registros.",
              "sInfoEmpty" : "Sin registros.",
              "sInfoFiltered": " - Filtrado de un total de  _MAX_ registros",
              "sLoadingRecords": "Leyendo información",
              "sProcessing": "Procesando",
              "sSearch": "Buscar:",
              "sZeroRecords": "Sin registros",
              "oPaginate": {
                "sPrevious": "Anterior",
                "sNext": "Siguiente"
              }          
          }
    });  

    $('[data-toggle="tooltip"]').tooltip();  
    initMapToDraw()
    $('.nopaste').bind("cut copy paste",function(e) {
      e.preventDefault();
      alert("La dirección se tiene que ingresar de manera manual.");
    });        
});

function backToMain(){
  var mainPage = $("#hRefLinkMain").val();
  location.href= mainPage;
}

function getInfoUnit(inputIdValue){
    if(inputIdValue>0){
        $("#infoUnit").html('<img src="/images/assets/loading.gif" alt="loading gif"/>'); 
        $.ajax({
            url: "/external/request/getinfodata",
            type: "GET",
            dataType : 'json',
            data: { catId: inputIdValue },
            success: function(data) {
                var result = data.answer; 

                if(result=='ok'){
                    var sTableInfo = '<b>Ult.reporte:</b> '+ data.uReporte  + '<br/>'+
                                    '<b>Placas:</b> '      + data.Placas    + '<br/>'+
                                    '<b>Eco:</b> '         + data.Eco       + '<br/>'+
                                    '<b>Ip:</b> '          + data.Ip        + '<br/>'+
                                    '<b>Tipo Equipo:</b> ' + data.TipoE     + '<br/>'+
                                    '<b>Tipo Unidad:</b> ' + data.Tunidad   + '<br/>';
                    $("#infoUnit").html(sTableInfo);
                    $("#inputInfo").html(sTableInfo);
                }else{
                  alert("La unidad no tiene pocisión válida");
                }
            }
        });
    }
}

function modifyFields(){
    $("#btnSaveOk").hide('slow');
    $("#btnModify").hide('slow');
    $("#btnSave").show('slow');
    $("#btnSaveCancel").show('slow');
    $("#inputFechaIn").prop( "disabled", false );
    $("#inputHorario").prop( "disabled", false );
    $("#inputHorario2").prop( "disabled", false );
    $("#inputComment").prop( "disabled", false );
    $("#inputComment").html("");
    $("#bOperation").val('modify');
}

function cancelModify(){
    location.reload();   
}

function updateUnits(){
    $("#inputFechaIn").rules("remove", "required");   
    $("#inputTipo").rules("remove", "required");   
    $("#inputUnidad").rules("remove", "required");   
    $("#inputComment").rules("remove", "required");  
    $("#inputHorario").rules("remove", "required");   

    $("#divContent").hide('slow');
    $("#divLoading").show('slow');
    $("#optReg").val("updateUnits");
    $("#bOperation").val("");     
    /*   
    $("#infoUnit").rules("remove", "required");   */

    $("#FormData").submit();    
}


function initMapToDraw(){
    directionsDisplay = new google.maps.DirectionsRenderer();
    geocoder          = new google.maps.Geocoder();    
    infoWindow        = new google.maps.InfoWindow;

    var mapOptions = {
      zoom: 5,
      center: new google.maps.LatLng(24.52713, -104.41406),
      mapTypeId: google.maps.MapTypeId.ROADMAP
    };
    map = new google.maps.Map(document.getElementById('Map'),mapOptions);
    bounds = new google.maps.LatLngBounds();    

    var valInputOrigen      = (document.getElementById('inputLugar'));
    var autCompleteOrigen   = new google.maps.places.Autocomplete(valInputOrigen);

    google.maps.event.addListener(autCompleteOrigen, 'place_changed', function() {
        var place = autCompleteOrigen.getPlace();
        if (!place.geometry) {
          return;
        }

        $("#inputLatitud").val(place.geometry.location.lat());
        $("#inputLontigud").val(place.geometry.location.lng());
        if( $("#inputLatitud").val()!=""  && $("#inputLatitud").val()!="0" &&
            $("#inputLontigud").val()!="" && $("#inputLontigud").val()!="0"
            ){
            printPoints();
        }
    });

    if( $("#inputLatitud").val()!=""  && $("#inputLatitud").val()!="0" &&
        $("#inputLontigud").val()!="" && $("#inputLontigud").val()!="0"
        ){
        printPoints();
    }
}

function printPoints(){
    if(markerTable!=null){
        markerTable.setMap(null);    
    }
    
    var Latitud  = parseFloat($("#inputLatitud").val())
    var Longitud = parseFloat($("#inputLontigud").val());

    markerTable = new google.maps.Marker({
      map: map,
      draggable:true,
      animation: google.maps.Animation.DROP,      
      position: new google.maps.LatLng(Latitud,Longitud),
      title:  'ubicacion',
      icon:   '/images/marker.png'
    });

    google.maps.event.addListener(markerTable, 'click', toggleBounce);    

    google.maps.event.addListener(markerTable, "dragend", function(event) {
        $("#inputLatitud").val(event.latLng.lat());
        $("#inputLontigud").val(event.latLng.lng());

        if( $("#inputLatitud").val()!="" && $("#inputLatitud").val()!="0" &&
            $("#inputLontigud").val()!="" && $("#inputLontigud").val()!="0"
            ){
            codeLatLng(event.latLng.lat(),event.latLng.lng(),0);
            printPoints()
        }
    });     

    map.setZoom(19);
    map.panTo(markerTable.getPosition());  
}

function toggleBounce() {
  if (markerTable.getAnimation() != null) {
    markerTable.setAnimation(null);
  } else {
    markerTable.setAnimation(google.maps.Animation.BOUNCE);
  }
}


function codeLatLng(inputLat,inputLon) {
  var lat = parseFloat(inputLat);
  var lng = parseFloat(inputLon);
  var latlng = new google.maps.LatLng(lat, lng);

  geocoder.geocode({'latLng': latlng}, function(results, status) {
    if (status == google.maps.GeocoderStatus.OK) {
      if (results[1]) {
            
        $("#inputLugar").val(results[0].formatted_address);
      } else {
        alert('No results found');
      }
    } else {
      alert('Geocoder failed due to: ' + status);
    }
  });
}