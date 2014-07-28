$( document ).ready(function() {
    var nowTemp = new Date();
    var now = new Date(nowTemp.getFullYear(), nowTemp.getMonth(), nowTemp.getDate(), 0, 0, 0, 0);

    $('#inputDate').datepicker({
        format: 'yyyy-mm-dd',
        autoclose: true,
          onRender: function(date) {
            return date.valueOf() < now.valueOf() ? 'disabled' : '';
          }
    }).on('changeDate', function(ev) {
		getHorariosCbo(this.value);
	});

    $("#FormData").validate({
        rules: {
            inputDate    : {
              required: true,
              date: true
            },   
            inputhorario    : "required",
            inputTelContacto    : {
              required: true,
              number: true,
              minlength: 10,
              maxlength: 10
            },
            inputContacto: "required",
        },
        messages: {
            inputDate    : {
                 required: "Campo Requerido",
                 date: "Ingresar una fecha válida"
            },   
            inputhorario    : "Campo Requerido",
            inputContacto   : "required",
            inputTelContacto: {
              required  : "Campo Requerido",
              number    : "Este campo acepta solo números",
              minlength : "El Teléfono debe de ser de 10 dígitos",
              maxlength : "El Teléfono debe de ser de 10 dígitos"
            }           
        },
        
        submitHandler: function(form) {
            form.submit();
        }
    });     	
});    