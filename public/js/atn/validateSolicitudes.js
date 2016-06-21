var map = null;
var infoWindow;
var markers = [];
var bounds;

$().ready(function() {
    var nowTemp = new Date();
    var now = new Date(nowTemp.getFullYear(), nowTemp.getMonth(), nowTemp.getDate(), 0, 0, 0, 0);
    var dateInter  = parseInt(nowTemp.getMonth())+1;  
    var todayMonth = (dateInter<10) ? "0"+dateInter : dateInter;
    var todayDay   = (nowTemp.getDate()<10) ? "0"+nowTemp.getDate(): nowTemp.getDate();        

    if($("#inputFechaIn").val()==""){
      $("#inputFechaIn").val(nowTemp.getFullYear()+"-"+todayMonth+"-"+todayDay);      
    }

    if($("#inputTimeBegin").val()==""){
      $("#inputTimeBegin").val( "00:00");      
    }

    if($("#inputTimeEnd").val()==""){
      $("#inputTimeEnd").val("23:59");
    }    

    var checkin = $('#inputFechaIn').datetimepicker({
        format: "yyyy-mm-dd",
        showMeridian: false,
        autoclose: true,
        todayBtn: true,
        minView : 2,
        maxView : 2,
        startView: 2,
        startDate: $("#inputFechaIn").val(),
    });

    var hourIn = $('#inputTimeBegin').datetimepicker({
        format: "hh:ii",
        showMeridian: false,
        autoclose: true,
        todayBtn: false,
        minView : 0,
        maxView : 1,
        startView: 1,
    }).on('changeDate', function(ev) {
        if($('#inputTimeBegin').val() > $('#inputTimeEnd').val()){
            $('#inputTimeEnd').val($('#inputTimeBegin').val());
        }
    });

    var hourEnd = $('#inputTimeEnd').datetimepicker({
        format: "hh:ii",
        showMeridian: false,
        autoclose: true,
        todayBtn: false,
        minView : 0,
        maxView : 1,
        startView: 1,
    }).on('changeDate', function(ev) {
        if($('#inputTimeEnd').val() < $('#inputTimeBegin').val()){
            $('#inputTimeBegin').val($('#inputTimeEnd').val());
        }        
    });

    $("#FormData").validate({
        rules: {
            inputFechaIn    :      "required",    
            inputTipo       :      "required",     
            inputComment    :      "required",
            inputHorario    :      "required",
            infoUnit        :      "required",
            inputRevision   :      "required",
            inputFolio      :      "required",
        },
        messages: {                          
            inputFechaIn    :      "Campo Requerido",        
            inputTipo       :      "Campo Requerido",            
            inputComment    :      "Campo Requerido",
            inputHorario    :      "Campo Requerido",
            infoUnit        :      "Selecciona una unidad",
            inputRevision   :      "Campo Requerido",
            inputFolio      :      "Campo Requerido"

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
    } );      
});

function backToMain(){
  var mainPage = $("#hRefLinkMain").val();
  location.href= mainPage;
}

function modifyFields(){
    $("#btnSaveOk").hide('slow');
    $("#btnModify").hide('slow');
    $("#btnSave").show('slow');
    $("#btnSaveCancel").show('slow');
    $("#inputFechaIn").prop( "disabled", false );
    $("#inputHorario").prop( "disabled", false );
    $("#inputTipo").prop( "disabled", false );
    $("#inputHorario2").prop( "disabled", false );
    $("#bOperation").val('modify');
    $("#inputRevision").prop( "disabled", false );
    $("#inputRevision").html("");
}

function cancelModify(){
    location.reload();   
}

function closeSol(){
    $("#optReg").val('close');
    $("#closetxt").show('slow');
    $(".closeBtun").hide('slow');
    $("#btnCloseok").show('slow');
}

function reDrawMap(){
    if(map ==null){
        initMapToDraw();    
    }else{
        setTimeout('resize()', 500);
    }
}

function resize(){
    google.maps.event.trigger(map,'resize');
    map.setCenter(map.getCenter()); 
    map.setZoom( map.getZoom() );
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
    printPoints();
}

function printPoints(){
    var Latitud  = parseFloat($("#inputLatitud").val())
    var Longitud = parseFloat($("#inputLontigud").val());

    if(Latitud!="" && Longitud!=""){
        markerTable = new google.maps.Marker({
          map: map,
          position: new google.maps.LatLng(Latitud,Longitud),
          title:  'ubicacion',
          icon:   '/images/marker.png'
        });

        map.setZoom(13);
        map.panTo(markerTable.getPosition());  
    }
}