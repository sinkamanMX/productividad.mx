$().ready(function() {
    $("#btnSearchIdSap").click(function() { openSearch(1); return false; });
    $("#btnDelRelIdSap").click(function() { deleteRowRelIdSap(); return false; });

    $("#btnSearchAlm").click(function() { openSearch(2); return false; });
    $("#btnDelRelAlm").click(function() { deleteRowRelIdAlmacen(); return false; });    

	$("#FormData").validate({
        rules: {
            
            inputUsuario:   "required",
            inputPassword:  "required",
            inputPasswordC: {
                required: true,
                equalTo: "#inputPassword",
            },  
            inputNombre :   "required",
            inputApps   :   "required",          
            inputEstatus:   "required",            
            inputEmail  : {
                required: true,
                email: true
            },              
            inputMovil    : {
                required: true,
                number: true,
                minlength: 10,
                maxlength: 10
            }       
        },
        messages: {
            inputUsuario    : "Campo Requerido",
            inputPassword   : "Campo Requerido",
            inputNombre     : "Campo Requerido",
            inputApps       : "Campo Requerido",
            inputEstatus    : "Campo Requerido",            
            inputEmail      : {
                required: "Campo Requerido",
                email: "Debe de ingresar un mail válido"
            },
            inputPasswordC  : {
                required    : "Campo Requerido",
                equalTo     : "La contraseña no coincide."
            }, 
            inputMovil    : {
                required  : "Campo Requerido",
                number    : "Este campo acepta solo números",
                minlength : "El Teléfono debe de ser de 10 dígitos",
                maxlength : "El Teléfono debe de ser de 10 dígitos"
            },                           
        },
        submitHandler: function(form) {
            form.submit();
        }
    });	

    if($("#catId").val()>-1){
        $("#inputPassword").rules("remove", "required");
        $("#inputPasswordC").rules("remove", "required");          
    }
});

function backToMain(){
  var mainPage = $("#hRefLinkMain").val();
  location.href= mainPage;
}


function addValidatePass(valueInput){
    if(valueInput!=""){
        $("#inputPassword").rules("add",  {required:true});
        $("#inputPasswordC").rules("add", {required: true,equalTo: "#inputPassword"});   
    }else{
        $("#inputPassword").rules("remove", "required");
        $("#inputPasswordC").rules("remove", "required");
    }
}
