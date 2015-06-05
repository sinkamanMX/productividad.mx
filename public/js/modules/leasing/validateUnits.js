$().ready(function() {
	$("#FormData").validate({
        rules: {
            inputEco	: "required",
            inputPlacas	: "required",
            inputIden	: "required",
            inputStatus	: "required",
            inputIden2: "required",
            inputEquipo: "required",
            inputVehiculo: "required",
        },
        
        // Se especifica el texto del mensaje a mostrar
        messages: {
            inputEco	: "Campo Requerido",
            inputPlacas	: "Campo Requerido",
            inputIden	: "Campo Requerido",
            inputStatus	: "Debe de seleccionar una opción",
            inputIden2  : "Campo Requerido",
            inputEquipo : "Campo Requerido",
            inputVehiculo : "Campo Requerido"
        },
        
        submitHandler: function(form) {
            form.submit();
        }
    });	

    $('.upperClass').keyup(function()
    {
        $(this).val($(this).val().toUpperCase());
    }); 
});

function backToMain(){
	var mainPage = $("#hRefLinkMain").val();
	location.href= mainPage;
}

function deleteRow(){	
	var idItem = $("#inputDelete").val();
    $.ajax({
        url: "/leasing/units/getinfo",
        type: "GET",
        dataType : 'json',
        data: { catId : idItem, 
        		optReg: 'delete'},
        success: function(data) {
            var result = data.answer; 

            if(result == 'deleted'){
            	$("#modalConfirmDelete").modal('hide'); 
            }else if(result == 'problem'){
                alert("hubo problema");          
            }else{
                alert("no hay data");          
            }
        }
    });    
}
