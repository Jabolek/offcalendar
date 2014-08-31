<?php

function base_url() {
    return 'http://devoffcalendar.pl/';
}
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset=utf-8 />
        <base href="<?= base_url() ?>" />
        <title>IndexedDB TODO List</title>
        <script type="text/javascript" src="static/js/jquery.js"></script>
        <script type="text/javascript" src="static/js/IndexedDB.js"></script>
        <script type="text/javascript" src="static/js/OffCalendar.js"></script>
        <script type="text/javascript">

            IndexedDB.open(function() {
                
                var userId = 1;
                var userEmail = 'lorem@ipsum.com';
                var userPassword = 'lorem123';
                var eventsSyncApiUrl = '<?= base_url() ?>apis/events_api/synchronize';
                
                <?php if(isset($_GET['sync'])) : ?>
                    
                    IndexedDB.synchronize(userId, userEmail, userPassword, eventsSyncApiUrl, function(){
                        
                        console.log('Synchronization finished.');
                        
                    });
                    
                <?php else : ?>
                

                // EVENT ADD TEST

                
                var start = currentTimestamp();
                var end = start + 3600;
                var description = "This is cool event";
                var sendNotification = 0;

                OffCalendar.addEvent(userId, start, end, description, sendNotification);


                // EVENT DIRECT ADD TEST

//                var currTimestamp = currentTimestamp();
//
//                var Event = {
//                    id: 5,
//                    user_id: userId,
//                    start_timestamp: start,
//                    end_timestamp: end,
//                    description: description,
//                    send_notification: sendNotification,
//                    voided: 0,
//                    created_timestamp: currTimestamp,
//                    remote_timestamp: currTimestamp,
//                    last_update_timestamp: 0,
//                };
//
//                IndexedDB.addEvent(Event, function(remoteEventId) {
//
//                    var Event = {
//                        id: 14,
//                        user_id: userId,
//                        start_timestamp: start,
//                        end_timestamp: end,
//                        description: "newest description",
//                        send_notification: sendNotification,
//                        voided: 0,
//                        created_timestamp: currTimestamp,
//                        remote_timestamp: currTimestamp,
//                        last_update_timestamp: 0,
//                    };
//                    
//                    IndexedDB.updateEventById(Event.id, Event, function(){
//                    });
//
//                });


//                // GET EVENTS TEST
//
//                IndexedDB.getUserEvents(userId, function(events) {
//
//                    if (events === null) {
//
//                        // TODO KO: ERROR
//
//                    } else {
//
//                        // TODO KO: SUCCESS
//
//                    }
//
//                });
//
//
//                // UPDATE EVENT TEST
//
//                var remoteEventId = 1;
//
//                var toUpdate = {
//                    description: "This is not so cool event!"
//                };
//
//                OffCalendar.updateEvent(remoteEventId, toUpdate);

                <?php endif; ?>

            });

        </script>
    </head>
    <body>
        IndexedDB Test. Open console for output.
    </body>
</html>