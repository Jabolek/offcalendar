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
    </head>

    <body>
        <div class="navbar navbar-inverse navbar-fixed-top" role="navigation">
            <div class="container-fluid">
                <div class="navbar-header">
                    <a class="navbar-brand" href="<?= base_url() ?>">
                        <span class="logo" style="font-size: 35px;">OffCalendar</span>
                    </a>
                </div>

                <div class="navbar-collapse collapse">
                    <ul class="nav navbar-nav navbar-right">
                        <li><a href="<?= base_url() ?>welcome/settings">Settings</a></li>
                        <li><a href="<?= base_url() ?>welcome/your_profile">Your Profile</a></li>
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
                        <li class="active"><a href="<?= base_url() ?>">Calendar</a></li>
                        <li><a href="<?= base_url() ?>welcome/notifications">Notifications <span class="badge">3</span></a></li>
                        <li><a href="<?= base_url() ?>welcome/upcoming_events">Upcoming Events</a></li>
                        <li><a href="<?= base_url() ?>welcome/past_events">Past Events</a></li>
                        <li><a href="#">Logout</a></li>
                    </ul>
                </div>

                <div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
                    <div class="container">

                        <?php if (isset($view)) : ?><?php $this->load->view($view); ?><?php endif ?>

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