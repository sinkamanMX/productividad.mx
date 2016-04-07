$().ready(function() {
    $("#btnNewDir").click(function() { openDirection(); return false; });
    $("#bntSearchUnit").click(function() { openSearch(1); return false; });

    $('#iFrameSearch').on('load', function () {        
      $('#loader').hide();
      $('#iFrameSearch').show();
    });       

    var nowTemp = new Date();
    var now = new Date(nowTemp.getFullYear(), nowTemp.getMonth(), nowTemp.getDate(), 0, 0, 0, 0);
    var dateInter  = parseInt(nowTemp.getMonth())+1;  
    if(inDate>31){
        inDate = "01";
        dateInter = dateInter+1;
    }

    var todayMonth = (dateInter<10) ? "0"+dateInter : dateInter;
    var inDate     = nowTemp.getDate() + 1;

    
    var todayDay   = (inDate<10) ? "0" + inDate: inDate;          

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
            inpuClienteEmp  :      "required",

            inputPlacas : "required",
            inputIden   : "required",
            inputColor  : "required",
            inputAnio   : "required",
            inputMarca  : "required",
            inputModelo : "required",
            inputIden2  : "required",
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
            inpuClienteEmp  :      "Campo Requerido",

            inputMarca  : "Campo Requerido",
            inputModelo : "Campo Requerido",
            inputPlacas : "Campo Requerido",
            inputIden   : "Campo Requerido",
            inputColor  : "Campo Requerido",
            inputAnio   : "Campo Requerido",
            inputIden2  : "Campo Requerido"
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

    $("#inputMarca").rules("remove", "required");   
    $("#inputModelo").rules("remove", "required");   
    $("#inputPlacas").rules("remove", "required");   
    $("#inputIden").rules("remove", "required");   
    $("#inputColor").rules("remove", "required");   
    $("#inputAnio").rules("remove", "required");   
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
        $(".inputDir").prop( "disabled", false );
    }else{
        $("#divSaveDir").hide('slow');   
        $("#divDireNew").hide('slow'); 
        $(".inputDir").prop('readonly', true);
        $(".inputDir").prop( "disabled", false );
        var iValue = parseInt(inputValue);
        if(inputValue>0){
            getInfoDir(inputValue);
        }
    }
}

function newCar(inputValue){    
    if(inputValue=="-1"){
        $("#inputMarca").val("");        
        $("#inputModelo").val("");
        $("#inputPlacas").val("");
        $("#inputIden").val("");
        $("#inputColor").val("");
        $("#inputAnio").val("");
        $("#divCarNew").show('slow'); 
        $("#inputMarca").rules("add",  {required:true});  
        $("#inputModelo").rules("add",  {required:true});  
        $("#inputPlacas").rules("add",  {required:true});  
        $("#inputIden").rules("add",  {required:true});  
        $("#inputColor").rules("add",  {required:true});  
        $("#inputAnio").rules("add",  {required:true});  
        $("#inputIden2").rules("add",  {required:true});  
        $(".inputCar").prop('readonly', false);
        $(".inputCar").prop( "disabled", false );
    }else{
        $("#inputMarca").rules("remove", "required");   
        $("#inputModelo").rules("remove", "required");   
        $("#inputPlacas").rules("remove", "required");   
        $("#inputIden").rules("remove", "required");   
        $("#inputColor").rules("remove", "required");   
        $("#inputAnio").rules("remove", "required");           
        $("#inputIden2").rules("remove", "required"); 
        $("#divCarNew").hide('slow'); 
        $("#inputMarca").val("");        
        $("#inputModelo").val("");
        $("#inputPlacas").val("");
        $("#inputIden").val("");
        $("#inputColor").val("");
        $("#inputAnio").val("");   
        $(".inputCar").prop('readonly', true);    
        $(".inputCar").prop( "disabled", true );
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
                    $("#inputEntreCalles").val(values.CP);
                    $("#inputRefs").val(values.CP);
                    $("#inputContacto").val(values.CP);
                    $("#inputTelCont").val(values.CP);                    

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

function openSearch(option){
    $("#loader").show('slow');
    $('#iFrameSearch').hide();
    $('#iFrameSearch').attr('src','/leasing/request/findunits');    
    $("#MyModalSearch").modal("show");
}

function assignValue(idValue){
    $("#inputUnidad").val(idValue);
    $("#MyModalSearch").modal("hide");
}

function searchUnits(idClient){
    $("#divUnidad").html("Cargando Información");

    $.ajax({
        url: "/leasing/request/getunits",
        type: "GET",
        data: { catId : idClient, 
                oprDb : 'searchUnits' },
        success: function(data) { 
            $("#divUnidad").html("");
            var dataCbo = '<select class=" m-wrap " id="inputUnidad" name="inputUnidad" onChange="newCar(this.value);" >';
            if(data!="no-info"){
                dataCbo += '<option value="">Seleccionar una opción</option>'+data+'<option value="-1">Otro</option> </select></select>';
            }else{
                dataCbo += '<option value="">Sin Información</option><option value="-1">Otro</option>';
            }
            dataCbo += '</select>';

            $("#divUnidad").html(dataCbo);            
            $(".chosen-select").chosen({disable_search_threshold: 10});            
            $("#divCarNew").hide('slow'); 
            $("#inputMarca").val("");        
            $("#inputModelo").val("");
            $("#inputPlacas").val("");
            $("#inputIden").val("");
            $("#inputColor").val("");
            $("#inputAnio").val("");
            searchPlaces(idClient);
        }
    }); 
}

function searchPlaces(idClient){
    $("#divLugares").html("Cargando Información");

    $.ajax({
        url: "/leasing/request/getplaces",
        type: "GET",
        data: { catId : idClient, 
                oprDb : 'searchUnits' },
        success: function(data) { 
            $("#divLugares").html("");
            var dataCbo = '<select class=" m-wrap " id="inputPlace" name="inputPlace"  onChange="newdireccion(this.value);" >';
            if(data!="no-info"){
                dataCbo += '<option value="">Seleccionar una opción</option>'+data+'<option value="-1">Otro</option> </select>';
            }else{
                dataCbo += '<option value="">Sin Información</option><option value="-1">Otro</option> ';
            }
            dataCbo += '</select>';

            $("#divLugares").html(dataCbo);            
            $(".chosen-select").chosen({disable_search_threshold: 10});
            $("#divSaveDir").hide('slow');   
            $("#divDireNew").hide('slow'); 
        }
    });     
}

function confirmCancelSol(idtableRow){ 
    $("#inputCancelSol").val(idtableRow);  
    $("#modalConfirmDelete").modal('show');
    $('#modalConfirmDelete').on('hidden.bs.modal', function () {
        location.reload();
    });         
}

function cancelSolicitud(){   
    var sComentario  = $("#inCancelComent").val();
    var idItem       = $("#inputCancelSol").val();
    if(sComentario!=""){
        $.ajax({
            url: "/leasing/request/cancel",
            type: "GET",
            dataType : 'json',
            data: { catId  : idItem, 
                    sComent: sComentario,
                    optReg : 'cancel'},
            success: function(data) {
                var result = data.answer; 

                if(result == 'canceled'){
                    $("#modalConfirmDelete").modal('hide'); 
                }else if(result == 'problem'){
                    alert("Ocurrio un Problema al cancelar la solicitud");          
                }else{
                    alert("Ocurrio un Problema al cancelar la solicitud");
                }
            }
        }); 
    }else{
        alert("Favor de ingresar un comentario.");
    }
}