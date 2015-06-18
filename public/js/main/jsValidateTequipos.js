$().ready(function() {
	$("#FormData").validate({
        rules: {
            inputNoPart	: "required",
            inputNombre	: "required",
            inputEstatus: "required",
            inputDesc   : "required"
        },
        messages: {
            inputNoPart	: "Campo Requerido",
            inputNombre	: "Campo Requerido",
            inputEstatus: "Campo Requerido",
            inputDesc   : "Campo Requerido"
        },
        
        submitHandler: function(form) {
            form.submit();
        }
    });	
});

function backToMainModule(){
	var mainPage = $("#hRefLinkMain").val();
	location.href= mainPage;
}