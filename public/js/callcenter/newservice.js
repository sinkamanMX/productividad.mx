$( document ).ready(function() {
    var nowTemp = new Date();
    var now = new Date(nowTemp.getFullYear(), nowTemp.getMonth(), nowTemp.getDate(), 0, 0, 0, 0);

    $('#inputNac').datepicker({
        format: 'yyyy-mm-dd',
        autoclose: true,
          onRender: function(date) {
            return date.valueOf() > now.valueOf() ? 'disabled' : '';
          }
    });

    $("#FormData").validate({
        rules: {
            inputNombre : "required",
            inputApps   : "required",
            inputStreet : "required",
            inputEstado : "required",
            inputMunicipio: "required",
            inputcolonia: "required",
            inputCP     : "required",
            inputDom    : "required",
            inputNoExt  : "required",
            inputNac    : {
              required: true,
              date: true
            },   
            inputGenero : "required",
            inputTel    : {
              required: true,
              number: true,
              minlength: 10,
              maxlength: 10
            },
            inputEmail  : {
              required: true,
              email: true
            }            
        },
        // Se especifica el texto del mensaje a mostrar
        messages: {
            inputNombre : "Campo Requerido",
            inputApps   : "Campo Requerido",
            inputStreet : "Campo Requerido",
            inputEstado : "Campo Requerido",
            inputMunicipio: "Campo Requerido",
            inputcolonia: "Campo Requerido",
            inputCP     : "Campo Requerido",
            inputDom    : "Campo Requerido",
            inputNoExt  : "Campo Requerido",
            inputNac    : {
                 required: "Campo Requerido",
                 date: "Ingresar una fecha válida"
            },   
            inputGenero : "required",
            inputTel    : {
              required  : "Campo Requerido",
              number    : "Este campo acepta solo números",
              minlength : "El Teléfono debe de ser de 10 dígitos",
              maxlength : "El Teléfono debe de ser de 10 dígitos"
            },
            inputEmail  : {
              required: "Campo Requerido",
              email: "Debe de ingresar un mail válido"
            } 
        },
        
        submitHandler: function(form) {
            form.submit();
        }
    });     
});