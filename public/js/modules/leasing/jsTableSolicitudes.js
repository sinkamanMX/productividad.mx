var sSucursal=-1;

$( document ).ready(function() {
    $(".chzn-select").chosen();

    var nowTemp = new Date();
    var now = new Date(nowTemp.getFullYear(), nowTemp.getMonth(), nowTemp.getDate(), 0, 0, 0, 0);
    var dateInter  = parseInt(nowTemp.getMonth())+1;  
    var todayMonth = (dateInter<10) ? "0"+dateInter : dateInter;
    var todayDay   = (nowTemp.getDate()<10) ? "0"+nowTemp.getDate(): nowTemp.getDate();        

    if($("#inputFechaIn").val()==""){
      $("#inputFechaIn").val(nowTemp.getFullYear()+"-"+todayMonth+"-"+todayDay);      
    }

    if($("#inputFechaFin").val()==""){
      $("#inputFechaFin").val(nowTemp.getFullYear()+"-"+todayMonth+"-"+todayDay);
    }
    
    var checkin = $('#inputFechaIn').datetimepicker({
        format: "yyyy-mm-dd",
        showMeridian: false,
        autoclose: true,
        todayBtn: true,
        minView: 2,
    }).on('changeDate', function(ev) {
      if(ev.date.valueOf() > $('#inputFechaFin').datetimepicker('getDate').valueOf()){
        $('#inputFechaFin').datetimepicker('setDate', ev.date);   
      }

      $('#inputFechaFin').datetimepicker('setStartDate', ev.date);      
      $('#inputFechaFin').prop('disabled', false);
      $('#inputFechaFin')[0].focus();      
    });

    var checkout = $('#inputFechaFin').datetimepicker({
        format: "yyyy-mm-dd",
        showMeridian: false,
        autoclose: true,
        todayBtn: true,
        minView: 2,
    }).on('changeDate', function(ev) {
      /*if(ev.date.valueOf() < $('#inputFechaIn').datetimepicker('getDate').valueOf()){
        $('#inputFechaIn').datetimepicker('setDate', ev.date);   
      }*/
      $('#inputFechaIn').datetimepicker('setEndDate', ev.date);
    }); 

    $('#dataTable').dataTable( {
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
              "sInfo": "",
              "sEmptyTable": "",
              "sInfoEmpty" : "",
              "sInfoFiltered": "",
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

    $('[data-toggle="tooltip"]').tooltip();      
    $('#iFrameDetCita').on('load', function () {        
        $('#loader').hide();
        $('#iFrameDetCita').show();
    });        
});

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

function showDetail(idDate){
    $('#loader').show();  

    $("#myModalinfoVis").modal("show");        
    $('#iFrameDetCita').attr('src','/leasing/request/getinfosol?strInput='+idDate);    
}

function goToEdit(idItem){
  $("#myModalinfoVis").modal("hide");
  location.href='/leasing/request/getinfo?catId='+idItem;
}

function acceptSol(idItem){
    $.ajax({
        url: "/leasing/request/acceptsol",
        type: "GET",
        dataType : 'json',
        data: { catId  : idItem, 
                oprDb : 'accept'},
        success: function(data) {
            var result = data.answer; 

            if(result == 'accept'){
                $("#myModalinfoVis").modal('hide'); 
                location.href='/leasing/request/index'; 
            }else if(result == 'problem'){
                alert("Ocurrio un Problema al cancelar la solicitud");          
            }else{
                alert("Ocurrio un Problema al cancelar la solicitud");
            }
        }
    }); 
}