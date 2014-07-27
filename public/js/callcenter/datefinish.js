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
            inputhorario    : "required"
        },
        messages: {
            inputDate    : {
                 required: "Campo Requerido",
                 date: "Ingresar una fecha válida"
            },   
            inputhorario : "Campo Requerido",
        },
        
        submitHandler: function(form) {
            form.submit();
        }
    });     	
});    