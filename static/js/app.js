(function ($) {

    var calendar = null;

    var options = {
        events_source: [],
        view: 'month',
        tmpl_path: 'tmpls/',
        tmpl_cache: false,
        onAfterViewLoad: function (view) {

            $('.page-header h3').text(this.getTitle());
            $('.btn-group button').removeClass('active');
            $('button[data-calendar-view="' + view + '"]').addClass('active');
        }
    };

    var userId = OffCalendar.user.id;

    if (userId) {

        IndexedDB.open(function () {

            IndexedDB.getUserEvents(userId, function (events) {

                if (events !== null) {

                    options.events_source = events;

                    calendar = $('#calendar').calendar(options);

                    OffCalendar.setCalendarNavigation(calendar);

                    OffCalendar.organizeEventsByDate(events);

                    OffCalendar.setupProfileDetails();
                }

            });

            OffCalendar.synchronizeEvents();

            setInterval(function () {

                OffCalendar.synchronizeEvents();

            }, OffCalendar.syncInterval);

        });

    }

}(jQuery));