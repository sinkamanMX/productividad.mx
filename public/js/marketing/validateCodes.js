
function backToMainModule(){
	var mainPage = $("#hRefLinkMain").val();
	location.href= mainPage;
}

$().ready(function() {
	$("#FormData").validate({
        rules: {
            txtTotalCodes   : {
                required: true,
                number: true,
                min: 1,
                max: 60
            }
        },        
        // Se especifica el texto del mensaje a mostrar
        messages: {
            txtTotalCodes    : {
                required  : "Campo Requerido",
                number    : "Este campo acepta solo números",
                min 	  : "La cantidad mínima es 1",
                max	 	  : "La cantidad máxima es 60"
            }
        },
        
        submitHandler: function(form) {
            form.submit();
        }
    });	

    $('#dataTable').dataTable( {
        "sDom": "<'row'<'span6'l><'span6'f>r>t<'row'<'span6'i><'span6'p>>",
        "sPaginationType": "bootstrap",
        "bDestroy": true,
        "bLengthChange": false,
        "bPaginate": true,
        "bFilter": true,
        "bSort": true,
        "bJQueryUI": true,
        "iDisplayLength": 10,      
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
});