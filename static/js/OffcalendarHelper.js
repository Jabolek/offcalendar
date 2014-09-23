var OffCalendarHelper = {};

OffCalendarHelper.currentTimestamp = function () {

    return new Date().getTime();

};

OffCalendarHelper.getDateFromTimestamp = function (timestamp, forInput) {

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

OffCalendarHelper.getTimestampFromDate = function (date) {

    var timestamp = date.replace('T', ' ');
    timestamp = new Date(timestamp).getTime();

    return parseInt(timestamp, 10);

};

OffCalendarHelper.mapEventTypeToClassName = function (eventType) {

    var eventTypeToClassNameMap = {
        'regular': 'event-inverse',
        'important': 'event-important',
        'celebration': 'event-success',
        'warning': 'event-warning',
        'party': 'event-special'
    };

    return eventTypeToClassNameMap[eventType];
};

OffCalendarHelper.mapEventClassNameToType = function (className) {

    var eventClassNameToTypeMap = {
        'event-inverse': 'regular',
        'event-important': 'important',
        'event-success': 'celebration',
        'event-warning': 'warning',
        'event-special': 'party'
    };

    return eventClassNameToTypeMap[className];
};

OffCalendarHelper.setSelectValue = function (value, inputName) {

    $('select[name=' + inputName + '] option').filter(function () {

        return $(this).text() === value;

    }).prop('selected', true);

};

OffCalendarHelper.setCheckboxValue = function (value, inputId) {

    $obj = $('#' + inputId);

    if (value === '1') {

        $obj.prop('checked', 'checked');

    } else {

        $obj.prop('checked', '');

    }

};