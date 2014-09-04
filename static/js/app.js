(function($) {

    var calendar = null;

    var options = {
        events_source: [],
        view: 'month',
        tmpl_path: 'tmpls/',
        tmpl_cache: false,
        onAfterEventsLoad: function(events) {

            if (!events) {
                return;
            }

            var list = $('#eventlist');
            list.html('');

            $.each(events, function(key, val) {

                $(document.createElement('li'))
                        .html('<a href="' + val.url + '">' + val.title + '</a>')
                        .appendTo(list);

            });
        },
        onAfterViewLoad: function(view) {

            $('.page-header h3').text(this.getTitle());
            $('.btn-group button').removeClass('active');
            $('button[data-calendar-view="' + view + '"]').addClass('active');
        },
        classes: {
            months: {
                general: 'label'
            }
        }
    };

    var userId = OffCalendar.user.id;

    if (userId) {

        IndexedDB.open(function() {

            IndexedDB.getUserEvents(userId, function(events) {

                if (events !== null) {

                    options.events_source = events;

                    calendar = $('#calendar').calendar(options);

                    OffCalendar.setCalendarNavigation(calendar);

                    OffCalendar.organizeEventsByDate(events);

                    OffCalendar.setupProfileDetails();
                }
            });
        });
    }

}(jQuery));