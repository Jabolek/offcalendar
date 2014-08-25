$(document).ready(function() {

    $('li.offcalendar-menu').click(function() {

        var containerId = $(this).attr('id');
        $('.offcalendar-container').hide();
        $('#' + containerId + '-cont').show();
        $('.offcalendar-menu').removeClass('active');
        $(this).addClass('active');

    });

});

function currentTimestamp() {

    return Math.round(new Date().getTime() / 1000);

}