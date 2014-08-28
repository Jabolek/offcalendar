var DB_NAME = 'offcalendar';
var DB_VERSION = 3;
var DB_STORE_NAME = 'events';
var DB_TRANS_MODE_READ_WRITE = 'readwrite';
var DB_TRANS_MODE_READ_ONLY = 'readonly';

var IndexedDB = {};

/**
 * Opens connection to the db.
 * @param {function} callback function to call after execution
 * @returns {bool} true on success, false otherwise
 */
IndexedDB.open = function(callback) {

    var request = indexedDB.open(DB_NAME, DB_VERSION);

    request.onupgradeneeded = function(e) {

        console.log('IndexedDB upgrade triggered.');

        var db = e.target.result;

        e.target.transaction.onerror = IndexedDB.onerror;

        if (db.objectStoreNames.contains(DB_STORE_NAME)) {
            db.deleteObjectStore(DB_STORE_NAME);
        }

        var store = db.createObjectStore(DB_STORE_NAME, {keyPath: "remote_id", autoIncrement: true});

        store.createIndex('user_id', 'user_id', {unique: false});
        store.createIndex('voided', 'voided', {unique: false});

    };

    request.onsuccess = function(e) {

        IndexedDB.db = e.target.result;

        console.log('IndexedDB opened successfully.');

        callback(true);
    };

    request.onerror = function(event) {

        callback(false);

    };
};

/**
 * Adds Event to database.
 * @param {Event} Event event to add
 * @param {function} callback to call after execution
 * @returns {mixed} remote_id of created event on success, null otherwise
 */
IndexedDB.addEvent = function(Event, callback) {

    console.log('Adding event:');
    console.log(Event);

    var db = IndexedDB.db;

    var trans = db.transaction([DB_STORE_NAME], DB_TRANS_MODE_READ_WRITE);

    var store = trans.objectStore(DB_STORE_NAME);

    var request = store.put(Event);

    request.onsuccess = function(e) {

        console.log('IndexedDB event #' + e.target.result + ' added successfully.');

        callback(e.target.result);

    };

    request.onerror = function(e) {

        console.error('IndexedDB event add error:');
        console.error(e.value);

        callback(null);

    };
};

/**
 * Updates event in database.
 * @param {int} eventRemoteId
 * @param {object} eventPropertiesToUpdate
 * @param {function} callback
 * @returns {mixed} Event on success, null otherwise
 */
IndexedDB.updateEvent = function(eventRemoteId, eventPropertiesToUpdate, callback) {

    console.log('Updating event #' + eventRemoteId + ' properties:');
    console.log(eventPropertiesToUpdate);

    var trans = IndexedDB.db.transaction([DB_STORE_NAME], "readwrite");

    var store = trans.objectStore(DB_STORE_NAME);

    var cursorRequest = store.openCursor(IDBKeyRange.only(eventRemoteId));

    cursorRequest.onsuccess = function(e) {

        var cursor = cursorRequest.result || e.result;

        console.log(cursor);

        var Event = cursor.value;

        for (var property in eventPropertiesToUpdate) {
            Event[property] = eventPropertiesToUpdate[property];
        }

        var request = cursor.update(Event);

        request.onerror = function(e) {

            console.error('Event update failed.');

            callback(null);

        };

        request.onsuccess = function(e) {

            console.log('Event updated successfully:');
            console.log(Event);

            callback(Event);

        };
    };
};

/**
 * Get all user events.
 * @param {int} userId
 * @param {function} callback
 * @returns {mixed} array of Events on success, null otherwise
 */
IndexedDB.getUserEvents = function(userId, callback) {

    var resultSet = [];

    var transaction = IndexedDB.db.transaction([DB_STORE_NAME], DB_TRANS_MODE_READ_ONLY);

    var store = transaction.objectStore(DB_STORE_NAME);

    var keyRange = IDBKeyRange.only(userId);

    var cursorRequest = store.index('user_id').openCursor(keyRange);

    cursorRequest.onsuccess = function(e) {

        var result = e.target.result;

        if (!!result === false)
            return;

        resultSet.push(result.value);

        result.continue();
    };

    transaction.oncomplete = function(event) {

        console.log('User events returned:');
        console.log(resultSet);

        callback(resultSet);

    };

    cursorRequest.onerror = function(event) {

        console.error('Error getting user events');

        callback(null);

    };
};

// OLD STUFF TO UPDATE BELOW

IndexedDB.sync = function() {

    var items = OffCalendar.events;

    var el = $("#synchronize");

    var url = el.attr('data-ajaxurl');

    // fire off the request to /form.php
    var request = $.ajax({
        url: url,
        type: "POST",
        data: {todos: JSON.stringify(items)}
    });

    request.done(function(response, textStatus, jqXHR) {

        IndexedDB.processSyncData(JSON.parse(response));
    });

    request.fail(function(jqXHR, textStatus, errorThrown) {

        OffCalendar.renderingEnabled = true;

        if (errorThrown === 'Not Found') {
            alert('Invalid URL to Synchronize service.');
        } else {
            alert('Unknown error');
        }
    });

    request.always(function() {

        el.removeAttr('data-ajaxdisabled');
    });
};

IndexedDB.processSyncData = function(data) {

    for (var index in data.add) {

        IndexedDB.addEvent(data.add[index]);
    }

    for (var index in data.update) {

        var timeStamp = data.update[index].timeStamp;

        IndexedDB.updateTodo(timeStamp, data.update[index]);
    }

    setTimeout(function() {
        IndexedDB.getActiveUserEvents(function() {
        }, true);
    }, 300);
};

IndexedDB.synchronize = function(event) {

    if ($(this).attr('data-ajaxdisabled') === 'yes')
        return;

    $(this).attr('data-ajaxdisabled', 'yes');

    // abort any pending request
    if (request) {
        request.abort();
    }

    OffCalendar.renderingEnabled = false;

    IndexedDB.getActiveUserEvents(IndexedDB.sync);

    event.preventDefault();

};

