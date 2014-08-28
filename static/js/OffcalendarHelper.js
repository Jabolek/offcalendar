var OffCalendarHelper = {};

OffCalendarHelper.currentTimestamp = function() {

    return Math.round(new Date().getTime() / 1000);

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