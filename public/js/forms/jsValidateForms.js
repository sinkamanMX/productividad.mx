$().ready(function() {
    $("#btnSearch").click(function() { openSearch(); return false; });
    $("#btnDelRel").click(function() { deleteRowRel(); return false; });

	$("#FormData").validate({
        rules: {
            inputTipo        : "required",
            inputTitulo      : "required",
            inputDescripcion : "required",
            inputOrden       : {
                number: true
            },
            inputIdOvision   : {
                number: true
            },            
            inputEstatus     : "required",
            inputLength      : {
                required     : true,
                number       : true
            },
            inputCliente     : true
        },
        messages: {
            inputTipo        : "Campo Requerido",
            inputTitulo      : "Campo Requerido",
            inputDescripcion : "Campo Requerido",
            inputEstatus     : "Campo Requerido",
            inputOrden       : {
                number       : "Este campo acepta solo números",
              }, 
            inputIdOvision  : {
                number    : "Este campo acepta solo números",
            },
            inputLength      : {
                required     : "Campo Requerido",
                number       : "Este campo acepta solo números",
            }, 
            inputCliente     : "Campo Requerido",
        },
        
        submitHandler: function(form) {
            form.submit();
        }
    });	

    var idUpdate = $("#catId").val();
    if(idUpdate==-1){
        //$("#inputPassword").rules("add",  {required:true});
        //$("#inputPasswordC").rules("add", {required: true,equalTo: "#inputPassword"});  
        $("#inputLength").rules("remove", "required");
        $("#inputCliente").rules("remove", "required");
    }
});

function oncChangeType(inputValue){
    if(inputValue!=""){
        if(inputValue=='M'){
            $("#inputLength").rules( "add", {
              required: true,
              number  : true,
              messages: {
                required: "Requerido",
                number  :  "Campo Requerido"
              }
            });

            $("#inputCliente").rules("add",  {required:true});
            $("#divControlNumber").show('slow');
            $("#divControlCliente").show('slow');
        }else{        
            $("#inputLength").val(0);
            $("#divControlNumber").hide('slow');
            $("#divControlCliente").hide('slow');
            $("#inputLength").rules("remove", "required");   
            $("#inputCliente").rules("remove", "required");
            $("#inputCliente").val("NULL");
        }
    }else{
        $("#inputLength").val(0);
        $("#divControlNumber").hide('slow');
        $("#divControlCliente").hide('slow');
        $("#inputLength").rules("remove", "required");   
        $("#inputCliente").rules("remove", "required");
        $("#inputCliente").val("NULL");
    }
}

function backToMainModule(){
	var mainPage = $("#hRefLinkMain").val();
	location.href= mainPage;
}

function deleteRowRel(){   
    var idItem = $("#catId").val();
    $.ajax({
        url: "/forms/main/getinfo",
        type: "GET",
        dataType : 'json',
        data: { catId : idItem, 
                optReg: 'deleteRel'},
        success: function(data) {
            var result = data.answer; 

            if(result == 'deleted'){
                location.href = '/forms/main/getinfo?catId='+idItem;
            }else if(result == 'problem'){
                alert("hubo problema");          
            }else{
                alert("no hay data");          
            }
        }
    });    
}

function openSearch(){
    $('#iFrameSearch').attr('src','/forms/main/searchicons');
    $("#MyModalSearch").modal("show");
}

function assignValue(nameValue,IdValue){
    $("#inputIdAssign").val(IdValue);
    $("#inputSearch").show('slow');
    $("#inputSearch").attr("src","/images/icons/"+nameValue);
    $("#MyModalSearch").modal("hide");
}


function addFieldForm(){
    var countElement = $("#inputCountElements").val();        
    var cboStatus  = $("#divSelectStatus").html();
    var cboOptions = $("#divSelectOptions").html();
    var cboTipo    = $("#divSelectTypes").html();
    var cboShowon  = $("#divShowon").html();

    $('#FormData3 tr:last').before('<tr><td>--</td>'+
                                        '<td>'+
                                            '<input name="aElements['+countElement+'][id]" type="hidden" value="-1"/>'+
                                            '<input id="inputOp'+countElement+'" name="aElements['+countElement+'][op]" type="hidden" value="new"/>'+
                                            '<input style="width:30px;" id="inputElement'+countElement+'" name="aElements['+countElement+'][orden]" type="text" class="span12"  value="'+(parseInt(countElement)+1)+'"  autocomplete="off">'+
                                        '</td>'+
                                        '<td>'+
                                            '<select class="span12"  id="inputTipo<?php echo $control?>" name="aElements['+countElement+'][tipo]" onChange="onChangeSelect(this.value,'+countElement+');">'+
                                                cboTipo+
                                            '</select>'+
                                        '</td>'+
                                        '<td>'+
                                            '<input id="inputDesc'+countElement+'" name="aElements['+countElement+'][desc]" type="text" class="span12"  value=""  autocomplete="off">'+
                                        '</td>'+
                                        '<td>'+
                                            '<select class="span12"  id="inputStat'+countElement+'" name="aElements['+countElement+'][status]">'+
                                                cboStatus+
                                            '</select>'+
                                        '</td>'+
                                        '<td>'+ 
                                            '<select class="span12"  id="inputReq'+countElement+'" name="aElements['+countElement+'][requerido]">'+
                                                cboOptions+
                                            '</select>'+
                                        '</td>'+
                                        '<td>'+ 
                                            '<select class="span12"  id="inputVal'+countElement+'" name="aElements['+countElement+'][validacion]">'+
                                                cboOptions+
                                            '</select>'+
                                        '</td>'+  
                                        '<td>'+
                                            '<div class="span12 no-margin-l">'+
                                                '<div class="btn-group">'+
                                                    '<button onClick="showCloseOptions('+countElement+');return false;" class="btn"> <i  id="spanOptions'+countElement+'" class="icon-chevron-down"></i></button>'+
                                                    '<button onClick="deleteFieldForm(this,'+countElement+');return false;" class="btn"><i class="icon-remove-sign"></i></button>'+
                                                '</div>'+
                                            '</div>'+ 
                                        '</td>'+                                                                     
                                    '</tr>'+
                                    
                                    '<tr id="trOptions'+countElement+'" style="background-color:#f5f5f5;display:none;">'+                                                
                                        '<td colspan="6">'+                                            
                                            '<div id="divOptions'+countElement+'" style="display:block;">'+
                                                '<textarea id="inputOps'+countElement+'" name="aElements['+countElement+'][options]" rows="6" class="span12 no-padding"></textarea>'+
                                                'Opciones (Delimitados por comas <i>ej:uno,dos,tres</i>):'+
                                            '</div>'+
                                        '</td>'+
                                        '<td colspan="3">'+
                                            '<table>'+
                                               '<tr>'+
                                                    '<td class="text-right" style="">'+
                                                        'Mostrar en '+
                                                    '</td>'+
                                                    '<td>'+                                                        
                                                        '<select class="span12"  id="inputShowon'+countElement+'" name="aElements['+countElement+'][showon]">'+
                                                            cboShowon+
                                                        '</select>'+
                                                    '</td>'+                                                            
                                                '</tr>'+                                            
                                                '<tr>'+
                                                    '<td class="text-right" style="">'+
                                                        'Depende de (# elemento)'+
                                                    '</td>'+
                                                    '<td>'+
                                                        '<input class="span12"  id="inputDepend'+countElement+'" name="aElements['+countElement+'][depend]" type="text" class="input-inline form-control col-xs-8 no-padding"  value=""  autocomplete="off"/>'+
                                                    '</td>'+                                                            
                                                '</tr>'+
                                                '<tr>'+
                                                    '<td class="text-right" style="">'+
                                                        'Cuando sea'+
                                                    '</td>'+
                                                    '<td colspan="2">'+
                                                        '<input  class="span12"  id="inputCuando'+countElement+'" name="aElements['+countElement+'][when]" type="text" class="input-inline form-control col-xs-8 no-padding"  value=""  autocomplete="off"/>'+
                                                    '</td>'+
                                                '</tr>'+
                                            '</table>'+
                                        '</td>'+
                                    '</tr>');
    countElement++;
    $("#inputCountElements").val(countElement);
}


function deleteFieldForm(objectTable,idInput){   
    $("#inputOp"+idInput).val('del');
    var td = $(objectTable).parent().parent().parent();    
    var tr = td.parent();
        tr.fadeOut(400, function(){
            tr.hide('slow');
            $("#trOptions"  +idInput).hide('slow');
        });
}

function showCloseOptions(idInput){
    var open  = $("#spanOptions"+idInput).hasClass('icon-chevron-down');
    var close = $("#spanOptions"+idInput).hasClass('icon-chevron-up');
    if(open && close == false){
        $("#spanOptions"+idInput).removeClass('icon-chevron-down').addClass('icon-chevron-up');
        $("#trOptions"  +idInput).show();
    }
    
    if(close && open == false){
        $("#spanOptions"+idInput).removeClass('icon-chevron-up').addClass('icon-chevron-down');
        $("#trOptions"  +idInput).hide();        
    }
}

//var aTypeShowOpts = [ "2","3","4","5","6","7","8","9","19","11","12" ];
var aTypeShowOpts = [ "3","4","5","12" ];

function onChangeSelect(valueInput,ObjectInput){
    var bExist = jQuery.inArray(valueInput,aTypeShowOpts);
    if(bExist>-1){
        $("#trOptions"+ObjectInput).show('slow');
    }else{
        $("#trOptions"+ObjectInput).hide('slow');
    }
}