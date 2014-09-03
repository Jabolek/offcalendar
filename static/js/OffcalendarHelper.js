var OffCalendarHelper = {};

OffCalendarHelper.currentTimestamp = function() {

    return new Date().getTime();

};

OffCalendarHelper.getDateFromTimestamp = function(timestamp, forInput) {

    var date = new Date(parseInt(timestamp, 10));

    var year = date.getFullYear();
    var month = date.getMonthFormatted();
    var day = date.getDateFormatted();

    var hours = date.getHours();
    var hoursFormatted = (hours < 10 ? '0' : '') + hours;

    var minutes = date.getMinutes();
    var minutesFormatted = (minutes < 10 ? '0' : '') + minutes;

    var space = ' ';

    (typeof forInput === 'undefined') ? space = ' ' : space = 'T';

    return year + '-' + month + '-' + day + space + hoursFormatted + ':' + minutesFormatted;

};

OffCalendarHelper.getTimestampFromDate = function(date) {

    var timestamp = date.replace('T', ' ');
    timestamp = new Date(timestamp).getTime();

    return parseInt(timestamp, 10);

};

OffCalendarHelper.mapEventTypeToClassName = function(eventType) {

    var eventTypeToClassNameMap = {
        'regular': 'event-inverse',
        'important': 'event-important',
        'celebration': 'event-success',
        'warning': 'event-warning',
        'party': 'event-special'
    };

    return eventTypeToClassNameMap[eventType];
};

OffCalendarHelper.mapEventClassNameToType = function(className) {

    var eventClassNameToTypeMap = {
        'event-inverse': 'regular',
        'event-important': 'important',
        'event-success': 'celebration',
        'event-warning': 'warning',
        'event-special': 'party'
    };

    return eventClassNameToTypeMap[className];
};

OffCalendarHelper.setSelectValue = function(value, inputName) {

    $('select[name=' + inputName + '] option').filter(function() {

        return $(this).text() === value;

    }).prop('selected', true);

};

OffCalendarHelper.setCheckboxValue = function(value, inputId) {

    $obj = $('#' + inputId);

    if (value === 'true') {

        $obj.prop('checked', 'checked');

    } else {

        $obj.prop('checked', '');

    }

};

OffCalendarHelper.handleEventAdd = function() {

    $('.add-event-text').click(function() {

        $('form#add-update-event-form').attr('name', 'offcalendar_add_event');
        $('#add-update-button').html('Add');
        $('#add-update-event-window h3').text('Add event');

        var start = $(this).attr('data-event-start');

        $('#add-update-event-window input[name=start_time]').val(start);
        $('#add-update-event-window input[name=end_time]').val('');
        $('#add-update-event-window textarea[name=description]').val('');
        $('#add-update-event-window textarea[name=external_url]').val('');

        OffCalendarHelper.setSelectValue('regular', 'event-class');

        OffCalendarHelper.setCheckboxValue(true, 'send_notifications');

        $('#add-update-event-window').fadeIn();

        OffCalendar.initEventAdd();

    });

};

OffCalendarHelper.handleEventUpdate = function() {

    $('.edit-event').click(function() {

        $('form#add-update-event-form').attr('name', 'offcalendar_update_event');
        $('#add-update-button').html('Update');
        $('#add-update-event-window h3').text('Update event');

        var start = OffCalendarHelper.getDateFromTimestamp($(this).attr('data-event-start'), true);
        var end = OffCalendarHelper.getDateFromTimestamp($(this).attr('data-event-end'), true);

        var desc = $(this).attr('data-event-desc');
        var url = $(this).attr('data-event-url');
        var evClass = OffCalendarHelper.mapEventClassNameToType($(this).attr('data-event-class'));
        var notif = $(this).attr('data-event-notif');

        var eventRemoteId = $(this).attr('data-event-id');

        $('#add-update-event-window input[name=start_time]').val(start);
        $('#add-update-event-window input[name=end_time]').val(end);
        $('#add-update-event-window textarea[name=description]').val(desc);
        $('#add-update-event-window textarea[name=external_url]').val(url);

        $('#event-remote-id').val(eventRemoteId);

        OffCalendarHelper.setSelectValue(evClass, 'event-class');

        OffCalendarHelper.setCheckboxValue(notif, 'send_notifications');

        $('#add-update-event-window').fadeIn();

        OffCalendar.initEventUpdate();

    });

};

OffCalendarHelper.handleAddUpdateWindowClose = function() {

    $('#add-update-event-window-dismiss').click(function() {

        $('#add-update-event-window').fadeOut();

    });

    $(document).keyup(function(e) {

        if (e.keyCode === 27)
            $('#add-update-event-window').fadeOut();

    });

};