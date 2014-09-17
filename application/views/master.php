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

            OffCalendar.initSearch();

            Offline.options = {
                checks: {
                    image: {
                        url: 'http://reviewconcierge.com/static/email/blank.png?_=' + (Math.floor(Math.random() * 1000000000))
                    },
                    active: 'image'
                },
                reconnect: false,
                checkOnLoad: true
            };

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
                        <li id="profile" class="offcalendar-menu"><a href="javascript:void(0)">Your Profile</a></li>
                        <li><a href="#" onclick="OffCalendar.logout();">Logout</a></li>
                    </ul>

                    <div class="col-sm-3 col-md-3 pull-right">

                        <form class="navbar-form" name="search_event">
                            <div class="input-group">
                                <input type="text" class="form-control" placeholder="Search" name="srch-term">

                                <div class="input-group-btn">
                                    <button class="btn btn-default" type="submit"><i class="glyphicon glyphicon-search"></i></button>
                                </div>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </div>

        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-3 col-md-2 sidebar">
                    <ul class="nav nav-sidebar">
                        <li id="main" class="active offcalendar-menu"><a href="javascript:void(0)">Calendar</a></li>
                        <li id="upcoming_events" class="offcalendar-menu"><a href="javascript:void(0)">Upcoming Events</a></li>
                        <li id="ongoing_events" class="offcalendar-menu"><a href="javascript:void(0)">Ongoing Events</a></li>
                        <li id="past_events" class="offcalendar-menu"><a href="javascript:void(0)">Past Events</a></li>
                    </ul>
                </div>

                <div class="col-sm-9 col-sm-offset-3 col-md-9 col-md-offset-2 main">
                    <div class="container">

                        <div id="sync-progress" class="alert alert-info medium-big center-block sync-alert" role="alert" style="display: none;">
                            Synchronizing events...
                            <img src="../../static/img/sync-loader.gif" alt="loading">
                        </div>

                        <div id="sync-success" class="alert alert-success medium-big center-block sync-alert" role="alert" style="display: none;">
                            Success! Events synchronized: <span class="badge alert-success"></span>
                        </div>

                        <div id="sync-failed" class="alert alert-danger medium-big center-block sync-alert" role="alert" style="display: none;">
                            Synchronization failed.
                        </div>

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

                            <div class="span11">
                                <div id="calendar"></div>
                            </div>

                        </div>

                        <div id="upcoming_events-cont" class="offcalendar-container" style="display: none;"></div>
                        <div id="ongoing_events-cont" class="offcalendar-container" style="display: none;"></div>
                        <div id="past_events-cont" class="offcalendar-container" style="display: none;"></div>

                        <div id="profile-cont" class="offcalendar-container" style="display: none;">
                            <div id="profile-name" class="panel panel-info">
                                <div class="panel-heading">
                                    <h3 class="panel-title">Your name:</h3>
                                </div>

                                <div class="panel-body"></div>
                            </div>

                            <div id="profile-email" class="panel panel-info">
                                <div class="panel-heading">
                                    <h3 class="panel-title">Your email:</h3>
                                </div>

                                <div class="panel-body"></div>
                            </div>

                            <div id="profile-events" class="panel panel-success">
                                <div class="panel-heading">
                                    <h3 class="panel-title">Your events count:</h3>
                                </div>

                                <div class="panel-body"></div>
                            </div>

                            <div id="profile-events-notif" class="panel panel-success">
                                <div class="panel-heading">
                                    <h3 class="panel-title">Your events with notifications count:</h3>
                                </div>

                                <div class="panel-body"></div>
                            </div>
                        </div>

                        <div id="search-cont" class="offcalendar-container" style="display: none;">
                            Search results:
                        </div>
                    </div>
                </div>                
            </div>
        </div>

    </body>

</html>