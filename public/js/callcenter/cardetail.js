$( document ).ready(function() {
    $("#FormData").validate({
        rules: {
            inputSerie  : "required",
            inputColor  : "required",
            inputAno    : "required",
            inputPlacas : "required"
        },
        messages: {
            inputSerie  : "Campo Requerido",
            inputColor  : "Campo Requerido",
            inputAno    : "Campo Requerido",
            inputPlacas : "Campo Requerido"
        },
        
        submitHandler: function(form) {
            form.submit();
        }
    });     
});