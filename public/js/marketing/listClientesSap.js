$( document ).ready(function() {

    $('#iFrameUnits').on('load', function () {        
        $('#loader').hide();
        $('#iFrameUnits').show();
    });

    $('#iFrameForms').on('load', function () {        
        $('#loaderForms').hide();
        $('#iFrameForms').show();
    });    
});

function migrateUnits(strCliente){
	$('#loader').show('slow');
    $("#divModalUnits").modal("show");        
    $('#iFrameUnits').attr('src','/marketing/sapclientes/migrateunits?strInput='+strCliente);   
}

function assignForms(strCliente){
	$('#loaderForms').show('slow');
    $("#divModalForms").modal("show");        
    $('#iFrameForms').attr('src','/marketing/sapclientes/clientforms?strInput='+strCliente);   	
}