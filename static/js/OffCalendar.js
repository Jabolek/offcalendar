var OffCalendar = {};

OffCalendar.user = {
    id: null,
    name: '',
    email: '',
    password: ''
};

OffCalendar.homeUrl = '/';
OffCalendar.dashboardUrl = '/welcome/dashboard';
OffCalendar.loginApiUrl = '/apis/users_api/login';
OffCalendar.registerApiUrl = '/apis/users_api/register';
OffCalendar.isAuthorizedApiUrl = '/apis/users_api/is_authorized';

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

    var user_email = localStorage.user_email;
    var user_password = localStorage.user_password;

    if (typeof user_email === undefined)
        OffCalendar.unauthorizedRedirect();

    if (typeof user_password === undefined)
        OffCalendar.unauthorizedRedirect();

    $.ajax({
        type: 'POST',
        datatype: 'json',
        async: false,
        url: OffCalendar.isAuthorizedApiUrl,
        data: ({
            email: user_email,
            password: user_password
        }),
        success: function(result) {

            if (!result)
                OffCalendar.unauthorizedRedirect();
        }
    });
};

/**
 * Logging out user, clears localStorage user data.
 */
OffCalendar.logout = function() {

    localStorage.clear();
    window.redirect = OffCalendar.homeUrl;

};

/**
 * Redirect unauthorized user to specific view.
 */
OffCalendar.unauthorizedRedirect = function() {

    window.location = '/welcome/unauthorized';

}

/**
 * Saves credentials returned from API to LocalStorage.
 * @param {object} apiData
 */
OffCalendar.saveCredentialsToLocalStorage = function(apiData) {

    var user_id = apiData.id;
    var user_email = apiData.email;
    var user_password = apiData.password;
    var user_name = apiData.name;

    if (typeof user_id !== undefined)
        localStorage.setItem('user_id', user_id);

    if (typeof user_email !== undefined)
        localStorage.setItem('user_email', user_email);

    if (typeof user_password !== undefined)
        localStorage.setItem('user_password', user_password);

    if (typeof user_name !== undefined)
        localStorage.setItem('user_name', user_name);

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
 * @param {int} userId
 * @param {int} startTimestamp
 * @param {int} endTimestamp
 * @param {string} description
 * @param {int} sendNotification 1 = yes, 0 = no
 * @returns {undefined}
 */
OffCalendar.addEvent = function(userId, startTimestamp, endTimestamp, description, sendNotification) {

    var currTimestamp = currentTimestamp();

    // TODO KO: remove if unneccesary
    userId = parseInt(userId, 10);
    startTimestamp = parseInt(startTimestamp, 10);
    endTimestamp = parseInt(endTimestamp, 10);

    var Event = {
        id: null,
        user_id: userId,
        start_timestamp: startTimestamp,
        end_timestamp: endTimestamp,
        description: description,
        send_notification: sendNotification,
        voided: 0,
        created_timestamp: currTimestamp,
        remote_timestamp: currTimestamp
    };

    IndexedDB.addEvent(Event, function(remoteEventId) {

        if (remoteEventId === null) {

            // TODO KO: insert correct logic

        } else {

            // TODO KO: insert correct logic

        }

    });

};

OffCalendar.showLocalStorage = function() {

    console.log('User local data:');
    console.log(localStorage);

};