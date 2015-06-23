var fechaFin = '';

$().ready(function() {
    var nowTemp = new Date();
    var now = new Date(nowTemp.getFullYear(), nowTemp.getMonth(), nowTemp.getDate(), 0, 0, 0, 0);
    var dateInter  = parseInt(nowTemp.getMonth())+1;  
    var todayMonth = (dateInter<10) ? "0"+dateInter : dateInter;
    var inDate     = nowTemp.getDate() + 1;
    var todayDay   = (inDate<10) ? "0"+inDate: inDate;   

        fechaFin   = nowTemp.getFullYear()+"-"+todayMonth+"-"+todayDay;

  $("#FormData").validate({
        rules: {
            inputName         :      "required",    
            inputRfc          :      "required",    
            inputFlota        :      "required",    
            inputFolio        :      "required",    
            inputContrato     :      "required",    
            inputAsesor       :      "required",      
            inputObservaciones:      "required"
        },
        messages:{
            inputName         :      "Campo Requerido",       
            inputRfc          :      "Campo Requerido",    
            inputFlota        :      "Campo Requerido",    
            inputFolio        :      "Campo Requerido",    
            inputContrato     :      "Campo Requerido",    
            inputAsesor       :      "Campo Requerido",     
            inputObservaciones:      "Campo Requerido"
        },
        submitHandler: function(form) {
            var countElement = $("#inputCountElements").val(); 
            if(countElement>0){
                form.submit();    
            }else{
                alert("Debe de ingresar al menos una persona al protocolo.");
            }
        }
    }); 

    $('.cinputFecha').datetimepicker({
        format: "yyyy-mm-dd",
        showMeridian: false,
        autoclose: true,
        todayBtn: true,
        minView: 2,
        startDate: '1940-01-01',
        endDate  : fechaFin
    });
    validatePersons(1)
});


function deleteFieldForm(objectTable,idInput){   
    $("#inputOp"+idInput).val('del');
    var td = $(objectTable).parent();  
    var tr = td.parent();
        tr.fadeOut(400, function(){
            tr.hide('slow');
            $("#trOptions"  +idInput).hide('slow');
            $("#trOptionsval"  +idInput).hide('slow');
        });
}

function addFieldForm(){
    var countElement = $("#inputCountElements").val();        
    var cboOptions   = $("#divSelectStatus").html();

    $('#tableElements tr:last').before('<tr>'+
                                            '<td>'+
                                                '<input name="aElements['+countElement+'][id]" type="hidden" value="-1"/>'+
                                                '<input id="inputOp'+countElement+'" name="aElements['+countElement+'][op]" type="hidden" value="new"/>'+
                                                '<input style="width:30px;" id="inputOrden'+countElement+'" name="aElements['+countElement+'][orden]" type="text" class="span12"  value=""  autocomplete="off">'+                                                
                                            '</td>'+                                            
                                            '<td>'+  
                                                '<input class="span12"  id="inputempreas'+countElement+'" name="aElements['+countElement+'][empresa]"  type="text" name="" value=""></td>'+                                           
                                            '</td>'+
                                            '<td>'+
                                                '<input class="span12"  id="inputnombre'+countElement+'" name="aElements['+countElement+'][nombre]"   type="text" name="" value=""></td>'+
                                            '</td>'+
                                            '<td>'+
                                                '<input class="span12"  id="inputpuesto'+countElement+'" name="aElements['+countElement+'][puesto]"   type="text" name="" value=""></td>'+
                                            '</td>'+
                                            '<td>'+
                                                    '<input class="span12"  id="inputemail'+countElement+'" name="aElements['+countElement+'][email]"   type="text" name="" value=""></td>'+
                                            '</td>'+
                                            '<td>'+
                                                '<select class="span12"  id="inputpriori'+countElement+'" name="aElements['+countElement+'][ispriori]">'+
                                                    cboOptions+
                                                '</select>'+
                                            '</td>'+
                                            '<td>'+
                                                '<select class="span12"  id="inputisposc'+countElement+'" name="aElements['+countElement+'][isposc]">'+
                                                    cboOptions+
                                                '</select>'+                                                   
                                            '</td>'+
                                            '<td>'+
                                                '<button onClick="deleteFieldForm(this,'+countElement+');return false;" class="btn"><i class="icon-remove-sign"></i></button>'+
                                            '</td> '+
                                        '</tr>'+
                                        
                                        '<tr id="trOptions'+countElement+'" style="background-color:#f5f5f5;<?php echo $bVisible;?>">'+                                               
                                            '<th></th>'+
                                            '<th class="span1">Clave de Identificación</th>'+
                                            '<th class="span1">Fecha Nac.</th> '+         
                                            '<th class="span1">Tel. Oficina</th>'+
                                            '<th class="span1">Nextel ID</th>'+                                                                                       
                                            '<th class="span1">Tel. Movil</th>'+
                                            '<th class="span1">Tel. 24 Hrs.</th>'+
                                        '</tr>'+
                                        '<tr>'+
                                            '<td></td>'+
                                            '<td>'+
                                                '<input class="span12" id="inputclave'+countElement+'" name="aElements['+countElement+'][clave]"    type="text" name="" value="">'+
                                            '</td>'+
                                            '</td>'+
                                            '<td>'+
                                                '<input class="span12 cinputFecha" readonly id="inputfecnac'+countElement+'" name="aElements['+countElement+'][fecnac]"   type="text" name="" value=""></td>'+
                                            '</td>'+                                               
                                            '<td>'+
                                                '<input class="span12"  id="inputofna'+countElement+'" name="aElements['+countElement+'][ofna]"   type="text" name="" value=""></td>'+
                                            '</td>'+
                                            '<td>'+                                                
                                                '<input class="span12"  id="inputnextid'+countElement+'" name="aElements['+countElement+'][nextid]"   type="text" name="" value=""></td>'+
                                            '</td>'+
                                            '<td>'+
                                                '<input class="span12"  id="inputtelmovil'+countElement+'" name="aElements['+countElement+'][telmovil]"   type="text" name="" value=""></td>'+
                                            '</td>'+
                                            
                                            '<td>'+
                                                '<input class="span12"  id="input24hrs'+countElement+'" name="aElements['+countElement+'][movil24hrs]"   type="text" name="" value=""></td>'+
                                            '</td>'+
                                        '</tr>');
    $('#inputfecnac'+countElement).datetimepicker({
        format: "yyyy-mm-dd",
        showMeridian: false,
        autoclose: true,
        todayBtn: true,
        minView: 2,
        startDate: '1940-01-01',
        endDate  : fechaFin
    });
    countElement++;
    $("#inputCountElements").val(countElement);
    validatePersons(2);
}

function validatePersons(option){
    var totalElements = $("#inputCountElements").val();
        totalElements = totalElements-1;
    if(option==1){
        
        for(i=0;i<=totalElements;i++){
           $("#inputOrden"+i).rules( "add", {
              required: true,
              messages: {
                required: "Requerido"
              }
            });

             $("#inputclave"+i).rules( "add", {
              required: true,
              messages: {
                required: "Requerido"
              }
            });  

            $("#inputemail"+i).rules( "add", 
                {
                    required: true,
                    email: true,
                    messages: {
                        required: "Campo Requerido",
                        email: "Debe de ingresar un mail válido"
                }
            });  

            $("#inputempreas"+i).rules( "add", {
              required: true,
              messages: {
                required: "Requerido"
              }
            });

            $("#inputnombre"+i).rules( "add", {
              required: true,
              messages: {
                required: "Requerido"
              }
            });       
            
            $("#inputpuesto"+i).rules( "add", {
              required: true,
              messages: {
                required: "Requerido"
              }
            });   

            $("#inputpriori"+i).rules( "add", {
              required: true,
              messages: {
                required: "Requerido"
              }
            });        
            
            $("#inputisposc"+i).rules( "add", {
              required: true,
              messages: {
                required: "Requerido"
              }
            });   

            $("#inputfecnac"+i).rules( "add", {
              required: true,
              messages: {
                required: "Requerido"
              }
            });   

            $("#inputofna"+i).rules( "add", 
                {
                    required: true,
                    number: true,
                    minlength: 10,
                    maxlength: 10,
                    messages: {
                        required  : "Campo Requerido",
                        number    : "Este campo acepta solo números",
                        minlength : "El Teléfono debe de ser de 10 dígitos",
                        maxlength : "El Teléfono debe de ser de 10 dígitos"
                    }
            });
            
            $("#input24hrs"+i).rules( "add", {
              required: true,
              messages: {
                required: "Requerido"
              }
            });   
              
            $("#inputtelmovil"+i).rules( "add", 
                {               
                    number: true,
                    minlength: 10,
                    maxlength: 10,
                    messages: {
                        number    : "Este campo acepta solo números",
                        minlength : "El Teléfono debe de ser de 10 dígitos",
                        maxlength : "El Teléfono debe de ser de 10 dígitos"
                    }
            });
        }
    }else{
        var i = totalElements;
        
        $("#inputOrden"+i).rules( "add", {
              required: true,
              messages: {
                required: "Requerido"
              }
            });

             $("#inputclave"+i).rules( "add", {
              required: true,
              messages: {
                required: "Requerido"
              }
            });  

            $("#inputemail"+i).rules( "add", 
                {
                    required: true,
                    email: true,
                    messages: {
                        required: "Campo Requerido",
                        email: "Debe de ingresar un mail válido"
                }
            });  

            $("#inputempreas"+i).rules( "add", {
              required: true,
              messages: {
                required: "Requerido"
              }
            });

            $("#inputnombre"+i).rules( "add", {
              required: true,
              messages: {
                required: "Requerido"
              }
            });       
            
            $("#inputpuesto"+i).rules( "add", {
              required: true,
              messages: {
                required: "Requerido"
              }
            });   

            $("#inputpriori"+i).rules( "add", {
              required: true,
              messages: {
                required: "Requerido"
              }
            });        
            
            $("#inputisposc"+i).rules( "add", {
              required: true,
              messages: {
                required: "Requerido"
              }
            });   

            $("#inputfecnac"+i).rules( "add", {
              required: true,
              messages: {
                required: "Requerido"
              }
            });   

            $("#inputofna"+i).rules( "add", 
                {
                    required: true,
                    number: true,
                    minlength: 10,
                    maxlength: 10,
                    messages: {
                        required  : "Campo Requerido",
                        number    : "Este campo acepta solo números",
                        minlength : "El Teléfono debe de ser de 10 dígitos",
                        maxlength : "El Teléfono debe de ser de 10 dígitos"
                    }
            });
            
            $("#input24hrs"+i).rules( "add", {
              required: true,
              messages: {
                required: "Requerido"
              }
            });   
              
            $("#inputtelmovil"+i).rules( "add", 
                {               
                    number: true,
                    minlength: 10,
                    maxlength: 10,
                    messages: {
                        number    : "Este campo acepta solo números",
                        minlength : "El Teléfono debe de ser de 10 dígitos",
                        maxlength : "El Teléfono debe de ser de 10 dígitos"
                    }
            });        
    }
}

function backToMainModule(){
    var mainPage = $("#hRefLinkMain").val();
    location.href= mainPage;
}