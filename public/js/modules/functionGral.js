function getoptionsCbo(idCboTo,classObject,idObject,chosen,options){	
	$("#div"+idCboTo).html("Cargando Información");
    var classChosen = (chosen) ? 'chosen-select': '';
    var claseFind   = (options=='coloniaO') ? 'colonia': options;
    var optionSelect= (options!='') ? 'getoptionsCbo("'+options+'","'+claseFind+'",this.value,false,"");': '';
    var optsCP      = (idCboTo=='colonia' || idCboTo=='coloniaO') ? 'getCPdir(this.value,"'+idCboTo+'");': '';
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
    var mun      = '';    
    var toObject = '';
    if(nameObject=='colonia'){
        mun      = $("#inputMunicipio").val();
        toObject = "inputCP";
    }else if(nameObject=='coloniaO'){
        mun = $("#inputMunicipioO").val();
        toObject = "inputCPO";
    }

    $.ajax({
        url: "/main/dashboard/getcp",
        type: "GET",
        dataType : 'json',
        data: { catId : idObject ,
                munId : mun},
        success: function(data) { 
            var result = data.answer; 
            $("#"+toObject).val(result);
        }
    });
}

function backToMain(){
    location.href='/callcenter/newservice/cancel';
}

var DrawNotifications = true;
$( document ).ready(function() {    
    getStatusExt(); 
}); 

function getStatusExt(){ 
    $("#dTravelMonitor").html('<img src="/images/assets/loading.gif" alt="loading gif"/>');
    $.ajax({
        url: "/main/main/validatemonitor",
        type: "GET",
        dataType : 'json',
        data: { 
            typaction: 'validate'
        },
        success: function(data) {
            $("#dTravelMonitor").html('');
            var iTotalTravels =  0;
            var aTravels     = data.notifs;            

            if(data.answer=='pendings'){
                var table = $('<table id="tableNotif" cellpadding="0" cellspacing="0" border="0" class="table table-striped table-bordered"></table>');
                table.append('<thead><tr><td>Clave Cliente</td><td>Descripción</td><td>Fecha</td><td></td></tr></thead>' );
                table.append('<tbody></tbody>');              

                $.each(aTravels, function(idx, obj) {
                    var row = $('<tr></tr>');
                    row.append('<td>'+obj.COD_CLIENTE+'</td>');
                    row.append('<td>'+obj.TITULO_MSG+'</td>');
                    row.append('<td>'+obj.FECHA_CREADO+'</td>');
                    row.append('<td class="text-center"><a href="/main/main/readnotification?strInput='+obj.ID_MAILING+'&catId='+obj.ID+'">'+
                                '<button class="btn-info"> <i class="icon-info-sign icon-white"></i></button>'+
                                '</a></td>');
                    row.appendTo(table);
                    iTotalTravels = iTotalTravels+1;           
                });

                table.appendTo($("#dTravelMonitor"));
            }

            if(iTotalTravels>0){
                $("#lblTravelsPen").html(iTotalTravels);
                $("#spanTravelsPen").show("slow");
                $('#tableNotif').dataTable( {
                    "sDom": "<'row'<'span6'l><'span6'f>r>t<'row'<'span6'i><'span6'p>>",
                    "sPaginationType": "bootstrap",
                    "bDestroy": true,
                    "bLengthChange": false,
                    "bPaginate": true,
                    "bFilter": true,
                    "bSort": true,
                    "bJQueryUI": true,
                    "iDisplayLength": 8,      
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
            }else{
                $("#lblTravelsPen").html("");
                $("#spanTravelsPen").hide("slow");
            }

            callTimer();
        }
    });
}

function callTimer(){                   
    var timeoutId = setTimeout(function(){   
      getStatusExt();   
    },30000);
}

function showTravelPen(){
    $("#mTravelMonitor").modal('show');     
}