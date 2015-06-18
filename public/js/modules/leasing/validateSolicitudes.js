$().ready(function() {
    $("#btnNewDir").click(function() { openDirection(); return false; });

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
            inputPlace      :      "required",
            inputTequipo    :      "required",
            inputCalle      :      "required",
            inputColonia    :      "required",
            inputMunicipio  :      "required",
            inputEstado     :      "required",
            inputCP         :      "required",
            inputDescripcion:      "required",

        },
        messages: {                          
            inputFechaIn    :      "Campo Requerido",        
            inputTipo       :      "Campo Requerido",        
            inputUnidad     :      "Campo Requerido",        
            inputComment    :      "Campo Requerido",
            inputHorario    :      "Campo Requerido",
            inputPlace      :      "Selecciona una unidad",
            inputTequipo    :      "Campo Requerido",
            inputCalle      :      "Campo Requerido",
            inputColonia    :      "Campo Requerido",
            inputMunicipio  :      "Campo Requerido",
            inputEstado     :      "Campo Requerido",
            inputCP         :      "Campo Requerido",
            inputDescripcion:      "Campo Requerido",
        },
        submitHandler: function(form) {
            form.submit();
        }
    }); 

    $("#inputDescripcion").rules("remove", "required");  
    
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

function modifyFields(){
    $("#btnSaveOk").hide('slow');
    $("#btnModify").hide('slow');
    $("#btnSave").show('slow');
    $("#btnSaveCancel").show('slow');
    $("#inputFechaIn").prop( "disabled", false );
    $("#inputHorario").prop( "disabled", false );
    $("#inputComment").prop( "disabled", false );
    $("#inputPlace").prop( "disabled", false );
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

    $("#FormData").submit();    
}

function newdireccion(inputValue){
    if(inputValue=="-1"){
        $("#inputCalle").val("");
        $("#inputColonia").val("");
        $("#inputMunicipio").val("");
        $("#inputEstado").val("");
        $("#inputCP").val("");

        $("#divDireNew").show('slow');   
        $("#divSaveDir").show('slow');   
        $(".inputDir").prop('readonly', false);
    }else{
        $("#divSaveDir").hide('slow');   
        $("#divDireNew").hide('slow'); 
        $(".inputDir").prop('readonly', true);
        var iValue = parseInt(inputValue);
        if(inputValue>0){
            getInfoDir(inputValue);
        }
    }
}

function getInfoDir(inputIdValue){
    if(inputIdValue>0){
        $("#infoUnit").html('<img src="/images/assets/loading.gif" alt="loading gif"/>'); 
        $.ajax({
            url: "/leasing/request/getinfodir",
            type: "GET",
            dataType : 'json',
            data: { catId: inputIdValue },
            success: function(data) {
                var result = data.answer; 
                var values = data.aData;

                if(result=='ok'){
                    $("#inputCalle").val(values.CALLE);
                    $("#inputColonia").val(values.COLONIA);
                    $("#inputMunicipio").val(values.MUNICIPIO);
                    $("#inputEstado").val(values.ESTADO);
                    $("#inputCP").val(values.CP);

                    $("#divDireNew").show('slow');
                    $("#infoUnit").html("");
                }else{
                  alert("No hay direccion");
                }
            }
        });
    }
}

function addNameDir(){
    var option = $("#chkSaveDir").is(':checked');
    if(option){
        $("#inputDescripcion").rules("add",  {required:true});
        $("#divNameDes").show('slow');
    }else{
        $("#divNameDes").hide('slow');
        $("#inputDescripcion").val('');
        $("#inputDescripcion").rules("remove", "required");  
    }
}

/*
function openDirection(){
    $('#iFrameSearch').attr('src','/main/tequipos/newdireccion');
    $("#MyModalSearch").modal("show");
}*/