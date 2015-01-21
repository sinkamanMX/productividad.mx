$().ready(function() {
    $('.upper').keyup(function()
    {
        $(this).val($(this).val().toUpperCase());
    });      
	$("#FormData").validate({
        rules: {
            inputNombre   :   "required",
            inputAps      :   "required",
            inputGenero   :   "required",
            inputSucursal :   "required",
            inputPuesto   :   "required",
            inputTel : {
                required: true,
                number: true,
                minlength: 10,
                maxlength: 10
            },
            inputTelOfna: {
                required: true,
                number: true,
                minlength: 10,
                maxlength: 10
            },
            inputRFC     :   "required",
            inputEmail: {
                required: true,
                email: true
            }
        },
        messages: {
            inputNombre   :   "Campo Requerido",
            inputAps      :   "Campo Requerido",
            inputGenero   :   "Campo Requerido",
            inputSucursal :   "Campo Requerido",
            inputPuesto   :   "Campo Requerido",
            inputTel : {
                required  : "Campo Requerido",
                number    : "Este campo acepta solo números",
                minlength: 10,
                maxlength: 10
            },
            inputTelOfna: {
                required  : "Campo Requerido",
                number    : "Este campo acepta solo números",
                minlength : "El Teléfono debe de ser de 10 dígitos",
                maxlength : "El Teléfono debe de ser de 10 dígitos"
            },
            inputRFC     :  "Campo Requerido",
            inputEmail: {
                required: "Campo Requerido",
                email   : "Debe de ingresar un mail válido"
            }            
        },
        
        submitHandler: function(form) {
            form.submit();
        }
    });	    
});