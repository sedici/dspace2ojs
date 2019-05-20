

jQuery(document).ready(function ($) {


    var buttonProcessFile = $('.process_file');
    var buttonProcessFiles = $('.process_files');
    var buttonArchivedFile = $('.archived_file');
    var buttonArchivedFiles = $('.archived_files');
    buttonArchivedFile.click(function (evt) {
        archived_file(this);
    });
    buttonProcessFile.click(function (evt) {
        process_file(this);
    });
    buttonProcessFiles.click(function (evt) {
        console.log($(this).removeClass('process_files').attr('class'));
        var class_button = $(this).removeClass('process_files').attr('class');
        console.log(class_button);
        let buttonProcessFile = $(`.parent_${class_button}`);
        buttonProcessFile.each(function () {
            process_file(this);
        });

    });
    buttonArchivedFiles.click(function (evt) {
        // console.log($(this).removeClass('archived_files').attr('class'));
        var class_button = $(this).removeClass('archived_files').attr('class');
        // console.log(class_button);
        let buttonArchivedFile = $(`.parent_${class_button}`);
        buttonArchivedFile.each(function () {
            archived_file(this);
        });

    });
});



function archived_file(elem) {
    var data = $(elem).attr('value');
    var id = '#tr_' + $(elem).attr('id_td');
    $.ajax({
        url: '/home/archived_file',
        data: { "data": data },
        success: function (respuesta) {
            // console.log(respuesta);
            $(id).remove();
        },
        error: function (error) {
            console.log(error);

        },
    });

}
function process_file(elem) {
    var data = $(elem).attr('value');

    var id = '#' + $(elem).attr('id_td');
    $.ajax({
        url: '/home/process_file',
        data: { "data": data },
        success: function (respuesta) {
            var name = respuesta.response.split("/").pop();
            $(id).html(`<a href="/${respuesta.response}">${name}</a>`);
            // console.log(respuesta);
        },
        error: function (error) {
            console.log(error);

        },
    });
    $(id).html(`<div class="loader"></div>`);


}