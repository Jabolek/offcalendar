<!DOCTYPE html>
<html>
    <head>
        <meta charset=utf-8 />
        <base href="<?= base_url() ?>" />
        <title>OffCalendar</title>
        <link rel="stylesheet" type="text/css" media="screen" href="static/bootstrap.min.css" />
        <link rel="stylesheet" type="text/css" media="screen" href="static/bootstrap-theme.css" />
        <link rel="stylesheet" type="text/css" media="screen" href="static/bootstrap-responsive.css">
        <link rel="stylesheet" type="text/css" media="screen" href="static/style.css" />
        <link rel="stylesheet" type="text/css" media="screen" href="static/calendar.css" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    </head>

    <body>
        <div class="navbar navbar-inverse navbar-fixed-top" role="navigation">
            <div class="container-fluid">
                <div class="navbar-header">
                    <a class="navbar-brand" href="#">
                        <span class="logo" style="font-size: 35px;">OffCalendar</span>
                    </a>
                </div>

                <div class="navbar-collapse collapse">
                    <ul class="nav navbar-nav navbar-right">
                        <li><a href="#">Settings</a></li>
                        <li><a href="#">Your Profile</a></li>
                        <li><a href="#">Help</a></li>
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
                        <li class="active"><a href="#">Calendar</a></li>
                        <li><a href="#">Notifications <span class="badge">3</span></a></li>
                        <li><a href="#">Upcoming Events</a></li>
                        <li><a href="#">Past Events</a></li>
                        <li><a href="#">Logout</a></li>
                    </ul>
                </div>

                <div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
                    Current state: <span class="label label-danger">OFFLINE</span>

                    <div class="container">
                        <div class="page-header">
                            <div class="pull-right form-inline">
                                <div class="btn-group">
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
                </div>
            </div>

            <script type="text/javascript" src="static/js/components/jquery/jquery.min.js"></script>
            <script type="text/javascript" src="static/js/components/underscore/underscore-min.js"></script>
            <script type="text/javascript" src="static/js/components/bootstrap2/js/bootstrap.min.js"></script>
            <script type="text/javascript" src="static/js/components/jstimezonedetect/jstz.min.js"></script>

            <script type="text/javascript" src="static/js/calendar.js"></script>
            <script type="text/javascript" src="static/js/app.js"></script>

            <script type="text/javascript" src="static/js/jquery.jeditable.js"></script>
            <script type="text/javascript" src="static/js/OffCalendar.js"></script>

        </div>
    </body>
</html>