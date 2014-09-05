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
OffCalendar.eventsSyncApiUrl = '/apis/events_api/synchronize';
OffCalendar.syncInterval = 60000 * 3;
OffCalendar.alertInterval = 1000 * 5;

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
 * Logging out user, clears localStorage user data.
 */
OffCalendar.logout = function() {

    localStorage.clear();
    window.location = OffCalendar.homeUrl;

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
        error: function() {

            window.location = OffCalendar.unauthorizedUrl;

        }
    });

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
            send_notifications: sendNotifications ? 1 : 0,
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

OffCalendar.handleEventAdd = function() {

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
            send_notifications: sendNotifications ? 1 : 0,
            voided: 0,
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

OffCalendar.handleEventUpdate = function() {

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

OffCalendar.handleEventDelete = function() {

    $('.delete-event').click(function() {

        var eventRemoteId = $(this).attr('data-event-id');

        OffCalendar.deleteEvent(eventRemoteId);

    });

};

OffCalendar.deleteEvent = function(eventRemoteId) {

    var toUpdate = {
        voided: 1
    };

    eventRemoteId = parseInt(eventRemoteId, 10);

    IndexedDB.updateEvent(eventRemoteId, toUpdate, function(Event) {

        if (Event !== null) {

            window.location = OffCalendar.dashboardUrl;

        }

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

    $container.empty();

    var evLength = events.length;

    for (var i = 0; i < evLength; i++) {

        var html = '';
        var ev = events[i];

        if (!ev.voided) {

            var start = OffCalendarHelper.getDateFromTimestamp(ev.start);
            var end = OffCalendarHelper.getDateFromTimestamp(ev.end);

            html = '<div class="events-list-item alert ' + ev.class + '" role="alert">' + start + ' - ' + end + '<br><br>' + ev.title + '</div>';

            $container.append(html);

        }

    }

    if ($container.is(':empty'))
        $container.html('<div class="alert-danger alert" role="alert">There are no events to show in this section.</div>');
};

OffCalendar.setupProfileDetails = function() {

    var name = OffCalendar.user.name;
    var email = OffCalendar.user.email;

    var eventsCount = 0;
    var eventsWithNotifications = 0;

    var events = OffCalendar.events.pastEvents.concat(OffCalendar.events.ongoingEvents, OffCalendar.events.upcomingEvents);

    for (var i = 0; i < events.length; i++) {

        if (!events[i]['voided']) {

            if (events[i]['send_notifications'])
                eventsWithNotifications++;

            eventsCount++;

        }

    }

    $('#profile-cont #profile-name .panel-body').text(name);
    $('#profile-cont #profile-email .panel-body').text(email);
    $('#profile-cont #profile-events .panel-body').text(eventsCount);
    $('#profile-cont #profile-events-notif .panel-body').text(eventsWithNotifications);

};

OffCalendar.setCalendarNavigation = function(calendar) {

    $('.btn-group button[data-calendar-nav]').each(function() {

        var $this = $(this);

        $this.click(function() {
            calendar.navigate($this.data('calendar-nav'));
        });

    });

    $('.btn-group button[data-calendar-view]').each(function() {

        var $this = $(this);

        $this.click(function() {
            calendar.view($this.data('calendar-view'));
        });

    });

    $('li.offcalendar-menu').click(function() {

        var containerId = $(this).attr('id');
        $('.offcalendar-container').hide();
        $('#' + containerId + '-cont').show();
        $('.offcalendar-menu').removeClass('active');
        $(this).addClass('active');

    });

};

OffCalendar.handleAddUpdateWindowClose = function() {

    $('#add-update-event-window-dismiss').click(function() {

        $('#add-update-event-window').fadeOut();

    });

    $(document).keyup(function(e) {

        if (e.keyCode === 27)
            $('#add-update-event-window').fadeOut();

    });

};

OffCalendar.synchronizeEvents = function() {

    if (Offline.state === 'up') {

        var userId = OffCalendar.user.id;
        var userEmail = OffCalendar.user.email;
        var userPassword = OffCalendar.user.password;
        var eventsSyncApiUrl = OffCalendar.eventsSyncApiUrl;

        $('.sync-alert').hide();

        $('#sync-progress').show();

        IndexedDB.synchronize(userId, userEmail, userPassword, eventsSyncApiUrl, function(itemsSynced) {

            if (itemsSynced !== null) {

                $('.sync-alert').hide();

                $('#sync-success .badge.alert-success').text(itemsSynced);

                $('#sync-success').show().delay(OffCalendar.alertInterval).fadeOut();

            } else {

                $('.sync-alert').hide();

                $('#sync-failed').show().delay(OffCalendar.alertInterval).fadeOut();
            }

            console.log('Synchronization finished.');

        });

    } else {

        console.log('Synchronization failed, connection down.')

    }

};

OffCalendar.initSearch = function() {

    var $form = $('form[name=search_event]');

    $form.submit(function(event) {

        event.preventDefault();

        var formData = $form.serializeArray();

        console.log(formData);

    });

};