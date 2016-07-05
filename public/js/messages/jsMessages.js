
$().ready(function(){
    $("#formDbman").validate({
        rules: {
            txtFecha     : "required",
            cboSendto    : "required",
            cboReSend    : "required",
            cboEach      : "required",
            cboTime      : "required",
            txtFechaFin  : "required",
            txaMensaje   : {
                required : true,
                minlength: 1
            }, 
        },
        messages: {
            txtFecha     : "Campo Requerido",
            cboSendto    : "Campo Requerido",
            cboReSend    : "Campo Requerido",
            cboEach      : "Campo Requerido",
            cboTime      : "Campo Requerido",
            txtFechaFin  : "Campo Requerido",
            txaMensaje   : {
                required  : "Campo Requerido",
                maxlength : "El número máximo de caracteres son 140."
            },   
        },
        submitHandler: function(form) {
            if(validateListCheksCustom()){
                form.submit();
            }
        }
    });     

    var nowTemp = new Date();
    var now = new Date(nowTemp.getFullYear(), nowTemp.getMonth(), nowTemp.getDate(), 0, 0, 0, 0);
    var dateInter  = parseInt(nowTemp.getMonth())+1;  
    var todayMonth = (dateInter<10) ? "0"+dateInter : dateInter;
    var todayDay   = (nowTemp.getDate()<10) ? "0"+nowTemp.getDate(): nowTemp.getDate();        

    if($("#txtFechaFin").val()==""){
      $("#txtFechaFin").val(nowTemp.getFullYear()+"-"+todayMonth+"-"+todayDay+ ' 23:59');    
    }

    var dateInit;
    if($("#txtFecha").val()==""){
        $("#txtFecha").val(nowTemp.getFullYear()+"-"+todayMonth+"-"+todayDay+" "+nowTemp.getHours()+":"+nowTemp.getMinutes());      
        dateInit = nowTemp.getFullYear()+"-"+todayMonth+"-"+todayDay+" "+nowTemp.getHours()+":"+nowTemp.getMinutes();
    }else{
        dateInit = nowTemp;
    }
 
    var checkin = $('#txtFecha').datetimepicker({
        format: "yyyy-mm-dd hh:ii",
        showMeridian: false,
        autoclose: true,
        todayBtn: true,  
        startDate: dateInit,
    }).on('changeDate', function(ev) {
      if(ev.date.valueOf() > $('#txtFechaFin').datetimepicker('getDate').valueOf()){
        $('#txtFechaFin').datetimepicker('setDate', ev.date);   
      }

      $('#txtFechaFin').datetimepicker('setStartDate', ev.date);      
    });

    var checkout = $('#txtFechaFin').datetimepicker({
        format: "yyyy-mm-dd hh:ii",
        showMeridian: false,
        autoclose: true,
        todayBtn: true
    }).on('changeDate', function(ev) {
      $('#txtFecha').datetimepicker('setEndDate', ev.date);
    });     

    $('#txaMensaje').on('keyup', function(){
        textareaLengthCheck();
    });    

    if($("#cboSendto").val()=='1'){
        changeDevices()
    }
});

function changerep(inputvalue){
    if(inputvalue==1){
        $(".optResend").show("slow");        
        $("#cboEach").rules("add",  {required:true});        
        $("#cboTime").rules("add",  {required:true});        
        $("#txtFechaFin").rules("add",  {required:true});  
    }else{
        $(".optResend").hide("slow");
        $("#cboEach").rules("remove", "required");
        $("#cboTime").rules("remove", "required");
        $("#txtFechaFin").rules("remove", "required");          
    }
    $('#formDbman').bootstrapValidator('updateStatus', 'cboEach', 'NOT_VALIDATED');
    $('#formDbman').bootstrapValidator('updateStatus', 'cboTime', 'NOT_VALIDATED');
    $('#formDbman').bootstrapValidator('updateStatus', 'txtFechaFin', 'NOT_VALIDATED');    
}

function changeDevices(){
    var inputvalue = $("#cboSendto").val();
    var itable     = 0;

    if(inputvalue==0){
        $("#tableSelect").hide("slow");
    }else{
        $("#tableSelect").show("slow");  
    }

    setTimeout(function(){ printTable(itable) }, 1000);      
}

function textareaLengthCheck() {
    var length = $("#txaMensaje").val().length;
    var charactersLeft = 140 - length;
    $("#lblLenght").html(charactersLeft);
}

function validateListCheksCustom(){
    var bResult  = false;
    var selected = '';        
    if($("#cboSendto").val()==1){
        $('#formDbman input[type=checkbox]').each(function(){
            if (this.checked) {
                selected += $(this).val()+', ';
            }
        }); 

        if (selected != ''){    
            bResult = true;
        }else{
            //$.jGrowl('Debe de seleccionar al menos una opción', { sticky: false, theme: 'growl-error', header: '¡Atención!' ,life: 3000 });        
            alert('Debe de seleccionar al menos una opción');
        }           
    }else{
        bResult  = true;
    }

    return bResult;    
}

function uncheckAll(){
    $('#formDbman input[type=checkbox]').each(function(){
        if (this.checked) {
            $(this).removeAttr('checked');
        }
    });     
}

function printTable(option){
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
}