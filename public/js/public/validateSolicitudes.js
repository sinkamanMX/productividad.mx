$().ready(function() {
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
            infoUnit       :       "required"
        },
        messages: {                          
            inputFechaIn    :      "Campo Requerido",        
            inputTipo       :      "Campo Requerido",        
            inputUnidad     :      "Campo Requerido",        
            inputComment    :      "Campo Requerido",
            inputHorario    :      "Campo Requerido",
            infoUnit        :      "Selecciona una unidad"

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