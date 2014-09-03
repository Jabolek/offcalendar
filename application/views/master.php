<!DOCTYPE html>
<html>
    <head>
        <meta charset=utf-8 />
        <base href="<?= base_url() ?>" />
        <title><?php if (isset($page_title)) : ?><?= $page_title . ' | ' ?><?php endif; ?>OffCalendar</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">

        <link rel="stylesheet" type="text/css" media="screen" href="static/bootstrap.min.css" />
        <link rel="stylesheet" type="text/css" media="screen" href="static/bootstrap-theme.css" />
        <link rel="stylesheet" type="text/css" media="screen" href="static/bootstrap-responsive.css">
        <link rel="stylesheet" type="text/css" media="screen" href="static/style.css" />
        <link rel="stylesheet" type="text/css" media="screen" href="static/calendar.css" />
        <link href="static/offline-language-english-indicator.css" rel="stylesheet" type="text/css"/>
        <link href="static/offline-language-english.css" rel="stylesheet" type="text/css"/>
        <link href="static/offline-theme-default-indicator.css" rel="stylesheet" type="text/css"/>
        <link href="static/offline-theme-default.css" rel="stylesheet" type="text/css"/>

        <script type="text/javascript" src="static/js/components/jquery/jquery.min.js"></script>
        <script type="text/javascript" src="static/js/components/underscore/underscore-min.js"></script>
        <script type="text/javascript" src="static/js/components/bootstrap2/js/bootstrap.min.js"></script>
        <script type="text/javascript" src="static/js/components/jstimezonedetect/jstz.min.js"></script>

        <script type="text/javascript" src="static/js/calendar.js"></script>

        <script type="text/javascript" src="static/js/OffCalendar.js"></script>
        <script type="text/javascript" src="static/js/IndexedDB.js"></script>
        <script type="text/javascript" src="static/js/OffCalendarHelper.js"></script>

        <script type="text/javascript" src="static/js/app.js"></script>

        <script src="static/js/offline/offline.js" type="text/javascript"></script>
        <script src="static/js/offline/requests.js" type="text/javascript"></script>
        <script src="static/js/offline/ui.js" type="text/javascript"></script>

        <script type="text/javascript">

            OffCalendar.isAuthorized();

        </script>
    </head>

    <body>
        <div class="navbar navbar-inverse navbar-fixed-top" role="navigation">
            <div class="container-fluid">
                <div class="navbar-header">
                    <a class="navbar-brand" href="javascript:void(0)">
                        <span class="logo" style="font-size: 35px;">OffCalendar</span>
                    </a>
                </div>

                <div class="navbar-collapse collapse">
                    <ul class="nav navbar-nav navbar-right">
                        <li id="settings" class="offcalendar-menu"><a href="javascript:void(0)">Settings</a></li>
                        <li id="profile" class="offcalendar-menu"><a href="javascript:void(0)">Your Profile</a></li>
                        <li id="help" class="offcalendar-menu"><a href="javascript:void(0)">Help</a></li>
                    </ul>

                    <form class="navbar-form navbar-right">
                        <input type="text" class="form-control" placeholder="Search...">
                    </form>
                </div>
            </div>
        </div>

        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-3 col-md-2 sidebar">
                    <ul class="nav nav-sidebar">
                        <li id="main" class="active offcalendar-menu"><a href="javascript:void(0)">Calendar</a></li>
                        <li id="notifications" class="offcalendar-menu"><a href="javascript:void(0)">Notifications <span class="badge">3</span></a></li>
                        <li id="upcoming_events" class="offcalendar-menu"><a href="javascript:void(0)">Upcoming Events</a></li>
                        <li id="ongoing_events" class="offcalendar-menu"><a href="javascript:void(0)">Ongoing Events</a></li>
                        <li id="past_events" class="offcalendar-menu"><a href="javascript:void(0)">Past Events</a></li>
                        <li><a href="#" onclick="OffCalendar.logout();">Logout</a></li>
                    </ul>
                </div>

                <div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
                    <div class="container">
                        <div id="main-cont" class="offcalendar-container">
                            <div class="page-header">
                                <div class="pull-right form-inline">
                                    <div class="btn-group">
                                        <button class="btn btn-success" data-calendar-view="day">Add event</button>
                                        <button class="btn btn-primary" data-calendar-nav="prev"><< Prev</button>
                                        <button class="btn" data-calendar-nav="today">Today</button>
                                        <button class="btn btn-primary" data-calendar-nav="next">Next >></button>
                                    </div>
                                    <div class="btn-group">
                                        <button class="btn btn-warning" data-calendar-view="year">Year</button>
                                        <button class="btn btn-warning active" data-calendar-view="month">Month</button>
                                        <button class="btn btn-warning" data-calendar-view="week">Week</button>
                                        <button class="btn btn-warning" data-calendar-view="day">Day</button>
                                    </div>
                                </div>

                                <h3></h3>
                            </div>

                            <div class="row">
                                <div class="span9">
                                    <div id="calendar"></div>
                                </div>
                            </div>
                        </div>

                        <div id="notifications-cont" class="offcalendar-container" style="display: none;">notifications</div>

                        <div id="upcoming_events-cont" class="offcalendar-container" style="display: none;"></div>
                        <div id="ongoing_events-cont" class="offcalendar-container" style="display: none;"></div>
                        <div id="past_events-cont" class="offcalendar-container" style="display: none;"></div>
                        <div id="settings-cont" class="offcalendar-container" style="display: none;">settings</div>
                        <div id="profile-cont" class="offcalendar-container" style="display: none;">your profile</div>
                        <div id="help-cont" class="offcalendar-container" style="display: none;">help</div>
                    </div>
                </div>                
            </div>
        </div>

        <script type="text/javascript" src="static/js/components/jquery/jquery.min.js"></script>
        <script type="text/javascript" src="static/js/components/underscore/underscore-min.js"></script>
        <script type="text/javascript" src="static/js/components/bootstrap2/js/bootstrap.min.js"></script>
        <script type="text/javascript" src="static/js/components/jstimezonedetect/jstz.min.js"></script>

        <script type="text/javascript" src="static/js/calendar.js"></script>
        <script type="text/javascript" src="static/js/app.js"></script>

        <script type="text/javascript" src="static/js/OffCalendar.js"></script>

        <script src="static/js/offline/offline.js" type="text/javascript"></script>
        <script src="static/js/offline/requests.js" type="text/javascript"></script>
        <script src="static/js/offline/ui.js" type="text/javascript"></script>

        <script type="text/javascript">

                            Offline.options = {
                                checks: {
                                    image: {
                                        url: 'http://reviewconcierge.com/static/email/blank.png?_=' + (Math.floor(Math.random() * 1000000000))
                                    },
                                    active: 'image'
                                },
                                reconnect: false
                            };

        </script>
    </body>

</html>