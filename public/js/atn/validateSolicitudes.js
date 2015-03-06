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
            inputTimeBegin  :      "required",    
            inputTimeEnd    :      "required",    
            inputEstatus    :      "required",    
            inputRevision   :      "required"
        },
        messages: {      
            inputFechaIn    :      "Campo Requerido",
            inputTimeBegin  :      "Campo Requerido",   
            inputTimeEnd    :      "Campo Requerido",
            inputEstatus    :      "Campo Requerido",
            inputRevision   :      "Campo Requerido" 
        },
        submitHandler: function(form) {
            form.submit();
        }
    }); 
});

function backToMain(){
  var mainPage = $("#hRefLinkMain").val();
  location.href= mainPage;
}
