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

                // EVENT ADD TEST

                var userId = 4;
                var start = currentTimestamp();
                var end = start + 3600;
                var description = "This is cool event";
                var sendNotification = 0;

                OffCalendar.addEvent(userId, start, end, description, sendNotification);
                
                // GET EVENTS TEST

                IndexedDB.getUserEvents(userId, function(events) {

                    if (events === null) {
                        console.error("Error getting events");
                    } else {
                        console.log("Returned events:");
                        console.log(events);
                    }
                    
                });

            });

        </script>
    </head>
    <body>
        IndexedDB Test. Open console for output.
    </body>
</html>