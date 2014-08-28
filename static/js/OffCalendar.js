var OffCalendar = {};

OffCalendar.user = {
    id: parseInt(localStorage.getItem('user_id'), 10),
    name: localStorage.getItem('user_name'),
    email: localStorage.getItem('user_email'),
    password: localStorage.getItem('user_password')
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

    var userEmail = localStorage.user_email;
    var userPassword = localStorage.user_password;

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
OffCalendar.updateEvent = function(eventRemoteId, toUpdate) {

    eventRemoteId = parseInt(eventRemoteId, 10);

    toUpdate.remote_timestamp = currentTimestamp();

    IndexedDB.updateEvent(eventRemoteId, toUpdate, function(Event) {

        if (Event === null) {

            // TODO KO: insert correct logic

        } else {

            // TODO KO: insert correct logic

        }

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

    var $form = $('#add-event-form');

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

        var sendNotification = 0;

        var Event = {
            id: 2, //todo
            user_id: userId,
            start: startTimestamp,
            end: endTimestamp,
            title: description,
            url: url,
            class: eventClass,
            send_notification: sendNotification,
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