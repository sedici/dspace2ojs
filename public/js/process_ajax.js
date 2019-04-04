console.log("cargue");


jQuery(document).ready(function($){

    var buttonProcessFile = $('.process_file');
    var buttonProcessFiles = $('.process_files');


    buttonProcessFile.click(function(evt){
       process_file(this)
    });
    buttonProcessFiles.click(function(evt){
        console.log($(this).removeClass('process_files').attr('class'));
            var class_button=$(this).removeClass('process_files').attr('class');
            console.log(class_button);  
            let buttonProcessFile=$(`.parent_${class_button}`);
                buttonProcessFile.each(function(){
                    process_file(this)
                });

    });
});


function process_file(elem){ 
    var data= $(elem).attr('value');
        
        var id='#'+$(elem).attr('id_td');
    $.ajax({
        url: '/home/process_file',
        data: {"data" : data},
        success: function(respuesta) {
            var name= respuesta.response.split("/").pop();
            $(id).html(`<a href="/${respuesta.response}">${name}</a>`);
            console.log(respuesta);
        },
        error: function(error) {
            console.log(error);

        },
    });    
    $(id).html(`<div class="loader"></div>`); 
}