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

                    OffCalendar.organizeEventsByDate(events);

                }
            });
        });
    }

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

    $('#first_day').change(function() {

        var value = $(this).val();
        value = value.length ? parseInt(value) : null;
        calendar.setOptions({first_day: value});
        calendar.view();

    });

    $('#language').change(function() {

        calendar.setLanguage($(this).val());
        calendar.view();

    });

    $('#events-in-modal').change(function() {

        var val = $(this).is(':checked') ? $(this).val() : null;
        calendar.setOptions({modal: val});

    });

    $('li.offcalendar-menu').click(function() {

        var containerId = $(this).attr('id');
        $('.offcalendar-container').hide();
        $('#' + containerId + '-cont').show();
        $('.offcalendar-menu').removeClass('active');
        $(this).addClass('active');

    });

}(jQuery));