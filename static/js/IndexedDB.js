var DB_NAME = 'offcalendar';
var DB_VERSION = 8;
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

        store.createIndex('id', 'id', {unique: true});
        store.createIndex('user_id', 'user_id', {unique: false});
        store.createIndex('voided', 'voided', {unique: false});
        store.createIndex('remote_timestamp', 'remote_timestamp', {unique: false});

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
 * @param {eventToUpdate} Event event to add
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
        console.error(e);

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

    var trans = IndexedDB.db.transaction([DB_STORE_NAME], DB_TRANS_MODE_READ_WRITE);

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

IndexedDB.updateEventById = function(eventId, eventToUpdate, callback) {

    console.log('Updating event by ID #' + eventId + ':');
    console.log(eventToUpdate);

    var trans = IndexedDB.db.transaction([DB_STORE_NAME], DB_TRANS_MODE_READ_WRITE);

    var store = trans.objectStore(DB_STORE_NAME);

    var cursorRequest = store.index('id').openCursor(IDBKeyRange.only(eventId));

    cursorRequest.onsuccess = function(e) {

        var cursor = cursorRequest.result || e.result;

        if (cursor === undefined) {

            IndexedDB.addEvent(eventToUpdate, callback);

        } else {

            var Event = cursor.value;

            for (var property in eventToUpdate) {
                Event[property] = eventToUpdate[property];
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

        }

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

        console.log('IndexedDB events sent to sync:');
        console.log(resultSet);

        callback(resultSet);

    };

    cursorRequest.onerror = function(event) {

        console.error('Error getting user events');

        callback(null);

    };
};

IndexedDB.getUserEventsForSync = function(userId, lastRemoteSyncTimestamp, callback) {

    var resultSet = [];

    var transaction = IndexedDB.db.transaction([DB_STORE_NAME], DB_TRANS_MODE_READ_ONLY);

    var store = transaction.objectStore(DB_STORE_NAME);

    var keyRange = IDBKeyRange.only(userId);

    var cursorRequest = store.index('user_id').openCursor(keyRange);

    cursorRequest.onsuccess = function(e) {

        var result = e.target.result;

        if (!!result === false)
            return;

        if (result.value.remote_timestamp > lastRemoteSyncTimestamp) {
            resultSet.push(result.value);
        }

        result.continue();
    };

    transaction.oncomplete = function(event) {

        console.log('IndexedDB events to sync:');
        console.log(resultSet);

        callback(resultSet);

    };

    cursorRequest.onerror = function(event) {

        console.error('Error getting user events');

        callback(null);

    };
};


// --------------------- IndexedDB Synchronize methods --------------------- //

IndexedDB.sync = {};

IndexedDB.sync.inProgress = false;
IndexedDB.sync.userId = 0;
IndexedDB.sync.remoteTimestamp = 0;
IndexedDB.sync.timestamp = 0;
IndexedDB.sync.email = '';
IndexedDB.sync.password = '';
IndexedDB.sync.url = '';
IndexedDB.sync.eventsToUpdate = [];
IndexedDB.sync.itemsSynced = 0;
IndexedDB.sync.callback = function() {
};

/**
 * 
 * @param {int} userId
 * @param {string} userEmail
 * @param {string} userPassword
 * @param {string} eventsSyncApiUrl
 * @param {function} callback
 * @returns {undefined}
 */
IndexedDB.synchronize = function(userId, userEmail, userPassword, eventsSyncApiUrl, callback) {

    if (IndexedDB.sync.inProgress === true) {
        console.log('IndexedDB sync already in progress');
        return;
    }

    IndexedDB.sync.inProgress = true;
    IndexedDB.sync.userId = userId;
    IndexedDB.sync.remoteTimestamp = OffCalendarHelper.currentTimestamp();
    IndexedDB.sync.itemsSynced = 0;
    IndexedDB.sync.email = userEmail;
    IndexedDB.sync.password = userPassword;
    IndexedDB.sync.url = eventsSyncApiUrl;
    IndexedDB.sync.callback = callback;

    var lastRemoteSyncTimestamp = IndexedDB.sync.getLastRemoteSyncTimestamp(userId);

    console.log('Last remote sync timestamp: ' + lastRemoteSyncTimestamp);

    IndexedDB.getUserEventsForSync(userId, lastRemoteSyncTimestamp, IndexedDB.sync.eventsFetched);


};

IndexedDB.sync.failed = function(msg) {

    console.error('IndexedDB sync error: ' + msg);

    IndexedDB.sync.inProgress = false;

    IndexedDB.sync.callback(null);

};

IndexedDB.sync.eventsFetched = function(events) {

    if (events === null) {
        IndexedDB.sync.failed('Error fetching events from db.');
    }

    var lastSyncTimestamp = IndexedDB.sync.getLastSyncTimestamp();

    var request = $.ajax({
        url: IndexedDB.sync.url,
        dataType: "json",
        type: "POST",
        data: {
            email: IndexedDB.sync.email,
            password: IndexedDB.sync.password,
            last_sync_timestamp: lastSyncTimestamp,
            events: JSON.stringify(events)
        }
    });

    request.done(function(response, textStatus, jqXHR) {

        console.log('Response from the server received:');
        console.log(response);

        IndexedDB.sync.processData(response);

    });

    request.fail(function(jqXHR, textStatus, errorThrown) {

        var msg;

        if (jqXHR.responseJSON && jqXHR.responseJSON.error) {
            msg = jqXHR.responseJSON.error;
        } else {
            msg = jqXHR.status + ' - ' + errorThrown;
        }

        IndexedDB.sync.failed(msg);

    });

};

IndexedDB.sync.processData = function(data) {

    IndexedDB.sync.eventsToUpdate = data.events_to_update;
    
    IndexedDB.sync.timestamp = data.sync_timestamp;

    console.log("Updating events in DB.");

    IndexedDB.sync.updateEvents();

};

IndexedDB.sync.success = function() {

    IndexedDB.sync.inProgress = false;

    IndexedDB.sync.setLastRemoteSyncTimestamp(IndexedDB.sync.remoteTimestamp);
    
    IndexedDB.sync.setLastSyncTimestamp(IndexedDB.sync.timestamp)

    // TODO: update last sync timestamp and 

    console.log(IndexedDB.sync.itemsSynced + ' events updated.');

    IndexedDB.sync.callback(IndexedDB.sync.itemsSynced);

};

IndexedDB.sync.updateEvents = function() {

    if (IndexedDB.sync.eventsToUpdate.length > 0) {

        var Event = IndexedDB.sync.eventsToUpdate.pop();

        if (Event.id) {
            IndexedDB.updateEventById(Event.id, Event, IndexedDB.sync.updateEvents);
        } else {
            IndexedDB.updateEvent(Event.remote_id, Event, IndexedDB.sync.updateEvents);
        }

        IndexedDB.sync.itemsSynced++;

    } else {

        IndexedDB.sync.success();

    }

};

IndexedDB.sync.getLastRemoteSyncTimestampKey = function(userId) {
    
    return 'last_remote_sync_timestamp_' + userId;
    
};

IndexedDB.sync.getLastRemoteSyncTimestamp = function() {
    
    var key = IndexedDB.sync.getLastRemoteSyncTimestampKey(IndexedDB.sync.userId);

    var timestamp = localStorage.getItem(key);

    if (timestamp) {
        return parseInt(timestamp, 10);
    }

    return 0;

};

IndexedDB.sync.setLastRemoteSyncTimestamp = function(timestamp) {
    
    var key = IndexedDB.sync.getLastRemoteSyncTimestampKey(IndexedDB.sync.userId);

    localStorage.setItem(key, timestamp);

};

IndexedDB.sync.getLastSyncTimestampKey = function(userId) {
    
    return 'last_sync_timestamp_' + userId;
    
};

IndexedDB.sync.getLastSyncTimestamp = function() {
    
    var key = IndexedDB.sync.getLastSyncTimestampKey(IndexedDB.sync.userId);

    var timestamp = localStorage.getItem(key);

    if (timestamp) {
        return parseInt(timestamp, 10);
    }

    return 0;

};

IndexedDB.sync.setLastSyncTimestamp = function(timestamp) {
    
    var key = IndexedDB.sync.getLastSyncTimestampKey(IndexedDB.sync.userId);

    localStorage.setItem(key, timestamp);

};