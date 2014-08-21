var OffCalendar = {};

OffCalendar.user = {
    id: null,
    name: '',
    email: '',
    password: ''
};

/**
 * Updates event stored in the db. IMPORTANT: make sure toUpdate properties are of correct types
 * @param eventRemoteId int 
 * @param toUpdate object, event properties to update
 * @returns undefined
 */
OffCalendar.updateEvent = function(eventRemoteId, toUpdate){
    
    eventRemoteId = parseInt(eventRemoteId, 10);
    
    toUpdate.remote_timestamp = currentTimestamp();
    
    IndexedDB.updateEvent(eventRemoteId, toUpdate, function(Event){
        
        // TODO KO: insert correct logic
        
    });
    
};

/**
 * @param userId int
 * @param startTimestamp int
 * @param endTimestamp int
 * @param description string
 * @param sendNotification int 1 = yes, 0 = no
 * @returns undefined
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

    IndexedDB.addEvent(Event, function(remoteEventId){
        
        // TODO KO: insert correct logic
        
    });
    
};


// TODO KO: Where to put helper functions ?

function currentTimestamp() {

    return Math.round(new Date().getTime() / 1000);
    
}