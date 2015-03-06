$().ready(function() {
$().ready(function() {
    var nowTemp = new Date();
    var now = new Date(nowTemp.getFullYear(), nowTemp.getMonth(), nowTemp.getDate(), 0, 0, 0, 0);
    var dateInter  = parseInt(nowTemp.getMonth())+1;  
    var todayMonth = (dateInter<10) ? "0"+dateInter : dateInter;
    var todayDay   = (nowTemp.getDate()<10) ? "0"+nowTemp.getDate(): nowTemp.getDate();        

    if($("#inputFechaIn").val()==""){
      $("#inputFechaIn").val(nowTemp.getFullYear()+"-"+todayMonth+"-"+todayDay+ ' 00:00');      
    }

    var checkin = $('#inputFechaIn').datetimepicker({
        format: "yyyy-mm-dd hh:ii",
        showMeridian: false,
        autoclose: true,
        todayBtn: true,
        startDate: $("#inputFechaIn").val(),
    });

  $("#FormData").validate({
        rules: {
            inputFechaIn    :      "required",    
            inputTipo       :      "required",    
            inputUnidad     :      "required",    
            inputComment    :      "required"
        },
        messages: {                          
            inputFechaIn    :      "Campo Requerido",        
            inputTipo       :      "Campo Requerido",        
            inputUnidad     :      "Campo Requerido",        
            inputComment    :      "Campo Requerido" 
        },
        submitHandler: function(form) {
            form.submit();
        }
    }); 
});

function optionAll(inputCheck){
    if(inputCheck){
        $('.chkOn').prop('checked', true);         
    }else{
        $('.chkOn').prop('checked', false);
    }
}

function backToMain(){
  var mainPage = $("#hRefLinkMain").val();
  location.href= mainPage;
}
