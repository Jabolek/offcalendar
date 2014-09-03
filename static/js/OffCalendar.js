var OffCalendar = {};

OffCalendar.user = {
    id: parseInt(localStorage.getItem('user_id'), 10),
    name: localStorage.getItem('user_name'),
    email: localStorage.getItem('user_email'),
    password: localStorage.getItem('user_password')
};

OffCalendar.events = {
    pastEvents: [],
    ongoingEvents: [],
    upcomingEvents: []
};
OffCalendar.homeUrl = '/';
OffCalendar.dashboardUrl = '/welcome/dashboard';
OffCalendar.loginApiUrl = '/apis/users_api/login';
OffCalendar.registerApiUrl = '/apis/users_api/register';
OffCalendar.isAuthorizedApiUrl = '/apis/users_api/is_authorized';
OffCalendar.unauthorizedUrl = '/welcome/unauthorized';

/**
 * Get email and password from inputs and sends to auth API.
 * Triggered by submitting login form.
 */

OffCalendar.initLogin = function() {

    $('#login_form').submit(function(event) {

        event.preventDefault();

        var email = $('input[name=email]').val();
        var password = $('input[name=password]').val();

        $('#loaderImage').show();

        $.ajax({
            type: 'POST',
            datatype: 'json',
            url: OffCalendar.loginApiUrl,
            data: ({
                email: email,
                password: password
            }),
            complete: function() {

                $('#loaderImage').hide();

            },
            success: function(result) {

                if (result && !result.error) {

                    result.password = password;

                    OffCalendar.saveCredentialsToLocalStorage(result);
                    window.location = OffCalendar.dashboardUrl;

                } else {

                    $('#error-label').text('Bad credentials. Try again!');
                    $('#error-label').show();
                }
            },
            error: function() {

                $('#error-label').text('API error, try again later.');
                $('#error-label').show();

            }
        });

        return false;
    });

};

/**
 * Get name, email and password from inputs and sends to auth API.
 * Triggered by submitting register form.
 */
OffCalendar.initRegister = function() {

    $('#register_form').submit(function(event) {

        event.preventDefault();

        var name = $('input[name=name]').val();
        var email = $('input[name=email]').val();
        var password = $('input[name=password]').val();

        $('#loaderImage').show();

        $.ajax({
            type: 'POST',
            datatype: 'json',
            url: OffCalendar.registerApiUrl,
            data: ({
                name: name,
                email: email,
                password: password
            }),
            complete: function() {

                $('#loaderImage').hide();

            },
            success: function(result) {

                if (result && !result.error) {

                    window.location = OffCalendar.homeUrl;

                } else {

                    $('#error-label').text('Please correct given data!');
                    $('#error-label').show();
                }
            },
            error: function() {

                $('#error-label').text('API error, try again later.');
                $('#error-label').show();

            }
        });

        return false;
    });

};

/**
 * Checking if user has permission to access main page.
 */
OffCalendar.isAuthorized = function() {

    var userEmail = OffCalendar.user.email;
    var userPassword = OffCalendar.user.password;

    if (typeof userEmail === undefined)
        window.location = OffCalendar.unauthorizedUrl;

    if (typeof userPassword === undefined)
        window.location = OffCalendar.unauthorizedUrl;

    $.ajax({
        type: 'POST',
        datatype: 'json',
        async: false,
        url: OffCalendar.isAuthorizedApiUrl,
        data: ({
            email: userEmail,
            password: userPassword
        }),
        success: function(result) {

            if (!result)
                window.location = OffCalendar.unauthorizedUrl;

        },
//        error: function() {
//
//            window.location = OffCalendar.unauthorizedUrl;
//
//        }
    });

};

/**
 * Logging out user, clears localStorage user data.
 */
OffCalendar.logout = function() {

    localStorage.clear();
    window.location = OffCalendar.homeUrl;

};

/**
 * Saves credentials returned from API to LocalStorage.
 * @param {object} apiData
 */
OffCalendar.saveCredentialsToLocalStorage = function(apiData) {

    var userId = apiData.id;
    var userEmail = apiData.email;
    var userPassword = apiData.password;
    var userName = apiData.name;

    if (typeof userId !== undefined)
        localStorage.setItem('user_id', userId);

    if (typeof userEmail !== undefined)
        localStorage.setItem('user_email', userEmail);

    if (typeof userPassword !== undefined)
        localStorage.setItem('user_password', userPassword);

    if (typeof userName !== undefined)
        localStorage.setItem('user_name', userName);

};

/**
 * Updates event stored in the db. IMPORTANT: make sure toUpdate properties are of correct types
 * @param {int} eventRemoteId  
 * @param {object} toUpdate event properties to update
 * @returns {undefined}
 */
OffCalendar.initEventUpdate = function() {

    var $form = $('form[name=offcalendar_update_event]');

    $form.submit(function(event) {

        event.preventDefault();

        var formData = $form.serializeArray();

        var currTimestamp = OffCalendarHelper.currentTimestamp();
        var userId = OffCalendar.user.id;

        var startTimestamp = OffCalendarHelper.getTimestampFromDate(formData[0]['value']);
        var endTimestamp = OffCalendarHelper.getTimestampFromDate(formData[1]['value']);

        var description = formData[2]['value'];

        var url = formData[3]['value'];

        var eventClass = OffCalendarHelper.mapEventTypeToClassName(formData[4]['value']);

        var sendNotifications = $('input#send_notifications').is(':checked');

        var toUpdate = {
            user_id: userId,
            start: startTimestamp,
            end: endTimestamp,
            title: description,
            url: url,
            class: eventClass,
            send_notifications: sendNotifications,
            voided: 0,
            created_timestamp: currTimestamp,
            remote_timestamp: OffCalendarHelper.currentTimestamp()
        };

        var eventRemoteId = formData[5]['value'];
        eventRemoteId = parseInt(eventRemoteId, 10);

        IndexedDB.updateEvent(eventRemoteId, toUpdate, function(Event) {

            if (Event !== null) {

                window.location = OffCalendar.dashboardUrl;

            }

        });
    });

};

/**
 * @param {int} startTimestamp
 * @param {int} endTimestamp
 * @param {string} description
 * @param {int} sendNotification 1 = yes, 0 = no
 * @param {strin} eventClass
 * @param {string} url
 * @returns {undefined}
 */
OffCalendar.initEventAdd = function() {

    var $form = $('form[name=offcalendar_add_event]');

    $form.submit(function(event) {

        event.preventDefault();

        var formData = $form.serializeArray();

        var currTimestamp = OffCalendarHelper.currentTimestamp();
        var userId = OffCalendar.user.id;

        var startTimestamp = formData[0]['value'];
        startTimestamp = startTimestamp.replace('T', ' ');
        startTimestamp = new Date(startTimestamp).getTime();
        startTimestamp = parseInt(startTimestamp, 10);

        var endTimestamp = formData[1]['value'];
        endTimestamp = endTimestamp.replace('T', ' ');
        endTimestamp = new Date(endTimestamp).getTime();
        endTimestamp = parseInt(endTimestamp, 10);

        var description = formData[2]['value'];

        var url = formData[3]['value'];

        var eventClass = OffCalendarHelper.mapEventTypeToClassName(formData[4]['value']);

        var sendNotifications = $('input#send_notifications').is(':checked');

        var Event = {
            user_id: userId,
            start: startTimestamp,
            end: endTimestamp,
            title: description,
            url: url,
            class: eventClass,
            send_notifications: sendNotifications,
            voided: 0,
            created_timestamp: currTimestamp,
            remote_timestamp: currTimestamp
        };

        IndexedDB.addEvent(Event, function(remoteEventId) {

            if (remoteEventId !== null) {

                window.location = OffCalendar.dashboardUrl;

            }

        });

    });
};

OffCalendar.organizeEventsByDate = function(events) {

    var currTimestamp = OffCalendarHelper.currentTimestamp();

    for (var i = 0; i < events.length; i++) {

        var ev = events[i];

        if (ev.start > currTimestamp && ev.end > currTimestamp)
            OffCalendar.events.upcomingEvents.push(ev);
        else if (ev.start <= currTimestamp && ev.end >= currTimestamp)
            OffCalendar.events.ongoingEvents.push(ev);
        else
            OffCalendar.events.pastEvents.push(ev);

    }

    OffCalendar.appendEvents();
};

OffCalendar.appendEvents = function() {

    var $pastEventsContainer = $('#past_events-cont');
    var $ongoingEventsContainer = $('#ongoing_events-cont');
    var $upcomingEventsContainer = $('#upcoming_events-cont');

    var pastEvents = OffCalendar.events.pastEvents;
    var ongoingEvents = OffCalendar.events.ongoingEvents;
    var upcomingEvents = OffCalendar.events.upcomingEvents;

    OffCalendar.appendEventsHTML($pastEventsContainer, pastEvents);
    OffCalendar.appendEventsHTML($ongoingEventsContainer, ongoingEvents);
    OffCalendar.appendEventsHTML($upcomingEventsContainer, upcomingEvents);

};

OffCalendar.appendEventsHTML = function($container, events) {

    var evLength = events.length;

    if (evLength) {

        for (var i = 0; i < events.length; i++) {

            var html = '';
            var ev = events[i];

            var start = OffCalendarHelper.getDateFromTimestamp(ev.start);
            var end = OffCalendarHelper.getDateFromTimestamp(ev.end);

            html = '<div class="events-list-item alert ' + ev.class + '" role="alert">' + start + ' - ' + end + '<br><br>' + ev.title + '</div>';

            $container.append(html);

        }

    } else {

        $container.html('There are no events to show in this section.');

    }
};