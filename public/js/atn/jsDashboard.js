var adataSource = [];

$( document ).ready(function() {

    $(".gantt").gantt({
        source: adataSource,
        navigate: "scroll",
        maxScale: "months",
        minScale: "hours",
        scale:    "hours",
        itemsPerPage: 20,
        scrollToToday: true,
        onItemClick: function(data) {    
            showDetail(data);
        },
        onAddClick: function(dt, rowId) {
            /*alert("Empty space clicked - add an item!");*/
        },
        onRender: function() {
            //if (window.console && typeof console.log === "function") {
            //    console.log("chart rendered");
            //}
        }
    });

    /*
    $(".gantt").popover({
        selector: ".bar",
        title: "I'm a popover",
        content: "And I'm the content of said popover.",
        trigger: "hover"
    });*/

    $('#iFrameDetCita').on('load', function () {        
        $('#loader').hide();
        $('#iFrameDetCita').show();
    });                
});

function showDetail(idDate){
    $('#loader').show();  

    $("#myModalinfoVis").modal("show");        
    $('#iFrameDetCita').attr('src','/atn/main/citadetalle?strInput='+idDate);    
}

function submitForm(){
    $( "#FormData" ).submit();
}