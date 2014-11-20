$().ready(function() {
    $("#btnSearchIdSap").click(function() { openSearch(1); return false; });
    $("#btnDelRelIdSap").click(function() { deleteRowRelIdSap(); return false; });

    $("#btnSearchAlm").click(function() { openSearch(2); return false; });
    $("#btnDelRelAlm").click(function() { deleteRowRelIdAlmacen(); return false; });    

	$("#FormData").validate({
        rules: {
            inputSucursal:      "required",
            inputUsuario:   "required",
            inputPassword:  "required",
            inputPasswordC: {
                required: true,
                equalTo: "#inputPassword",
            },  
            inputNombre :   "required",
            inputApps   :   "required",          
            inputEstatus:   "required",
            inputOperaciones:"required",
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
            inputSucursal   : "Campo Requerido",
            inputUsuario    : "Campo Requerido",
            inputPassword   : "Campo Requerido",
            inputNombre     : "Campo Requerido",
            inputApps       : "Campo Requerido",
            inputEstatus    : "Campo Requerido",
            inputOperaciones: "Campo Requerido",
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

function addValidatePass(valueInput){
    if(valueInput!=""){
        $("#inputPassword").rules("add",  {required:true});
        $("#inputPasswordC").rules("add", {required: true,equalTo: "#inputPassword"});   
    }else{
        $("#inputPassword").rules("remove", "required");
        $("#inputPasswordC").rules("remove", "required");
    }
}

function openSearch(option){
    if(option==1){
        $('#iFrameSearch').attr('src','/main/users/searchsap');
    }else{
        $('#iFrameSearch').attr('src','/main/users/searchalm');
    }

    $("#MyModalSearch").modal("show");
}

function deleteRowRelIdSap(){   
    var idItem = $("#catId").val();
    $.ajax({
        url: "/main/users/getinfo",
        type: "GET",
        dataType : 'json',
        data: { catId : idItem, 
                optReg: 'deleteRelIdSap'},
        success: function(data) {
            var result = data.answer; 

            if(result == 'deleted'){
                location.href = '/main/users/getinfo?catId='+idItem;
            }else if(result == 'problem'){
                alert("hubo problema");          
            }else{
                alert("no hay data");          
            }
        }
    });    
}


function deleteRowRelIdAlmacen(){   
    var idItem = $("#catId").val();
    $.ajax({
        url: "/main/users/getinfo",
        type: "GET",
        dataType : 'json',
        data: { catId : idItem, 
                optReg: 'deleteRelIdAlmacen'},
        success: function(data) {
            var result = data.answer; 

            if(result == 'deleted'){
                location.href = '/main/users/getinfo?catId='+idItem;
            }else if(result == 'problem'){
                alert("hubo problema");          
            }else{
                alert("no hay data");          
            }
        }
    });    
}



function assignValue(nameValue,IdValue,option){
    if(option==1){
        $("#inputIdSap").val(IdValue);
        $("#inputSearchIdSap").val(nameValue);
    }else{
        $("#inputIdAlm").val(IdValue);
        $("#inputSearchIdAlm").val(nameValue);
    }
    $("#MyModalSearch").modal("hide");
}

function backToMain(){
  var mainPage = $("#hRefLinkMain").val();
  location.href= mainPage;
}

function optionAll(inputCheck){
    if(inputCheck){
        $('.chkOn').prop('checked', true);         
    }else{
        $('.chkOn').prop('checked', false);
    }
}

function showDivClaves(option){
    if(option=="1"){
        $("#divClaves").show('slow');
    }else{
        $("#divClaves").hide('slow');
    }
}
