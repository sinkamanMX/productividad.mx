$().ready(function() {
    $("#FormData").validate({
        rules: {
            inputDescripcion    : "required",
            inputRazonSocial    : "required",            
            inputRFC            : "required",            
            inputEstatus        : "required"
        },
        messages: {
            inputDescripcion    : "Campo Requerido",
            inputRazonSocial    : "Campo Requerido",
            inputRFC            : "Campo Requerido",            
            inputEstatus        : "Campo Requerido"
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
