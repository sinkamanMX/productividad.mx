function getoptionsCbo(idCboTo,classObject,idObject,chosen,options){	
	$("#div"+idCboTo).html("Cargando Información");
    var classChosen = (chosen) ? 'chosen-select': '';
    var optionSelect= (options!='') ? 'getoptionsCbo("'+options+'","'+options+'",this.value,false,"");': '';
    var optsCP      = (idCboTo=='colonia') ? 'getCPdir(this.value,"inputCP");': '';
    $.ajax({
        url: "/main/dashboard/getselect",
        type: "GET",
        data: { catId : idObject, 
        		oprDb : classObject },
        success: function(data) { 
        	$("#div"+idCboTo).html("");
        	var dataCbo = '<select class=" m-wrap '+classChosen+'" id="input'+idCboTo+'" name="input'+idCboTo+'" onChange=\''+optionSelect+' '+optsCP+'\'>';
        	if(data!="no-info"){
        		dataCbo += '<option value="">Seleccionar una opción</option>'+data+'</select>';
        	}else{
				dataCbo += '<option value="">Sin Información</option>';
        	}
        	dataCbo += '</select>';
									
        	$("#div"+idCboTo).html(dataCbo);
			$(".chosen-select").chosen({disable_search_threshold: 10});
        }
    });  	
}

function getCPdir(idObject,nameObject){
    var mun = $("#inputMunicipio").val();
    $.ajax({
        url: "/main/dashboard/getcp",
        type: "GET",
        dataType : 'json',
        data: { catId : idObject ,
                munId : mun},
        success: function(data) { 
            var result = data.answer; 
            $("#"+nameObject).val(result);
        }
    });
}

function getHorariosCbo(inDate){    
    $("#divhorario").html("Cargando Información");
    $.ajax({
        url: "/main/dashboard/gethorarios",
        type: "GET",
        data: { dateID : inDate },
        success: function(data) { 
            $("#divhorario").html("");

            var dataCbo = '<select class=" m-wrap id="inputhorario" name="inputhorario">';
            if(data!="no-info"){
                dataCbo += '<option value="">Seleccionar una opción</option>'+data+'</select>';
            }else{
                dataCbo += '<option value="">Sin Información</option>';
            }
            dataCbo += '</select>';
                                    
            $("#divhorario").html(dataCbo);
        }
    });     
}

function backToMain(){
    location.href='/callcenter/newservice/cancel';
}