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
    </head>

    <body>
        <div class="container medium">
            <span class="logo dark" style="font-size: 55px;">OffCalendar</span>

            <div id="alert-progress" class="alert alert-info medium center-block offcalendar-alert" role="alert" style="display: none;">

            </div>

            <div id="alert-success" class="alert alert-success medium center-block offcalendar-alert" role="alert" style="display: none;">

            </div>

            <div id="alert-failed" class="alert alert-danger medium center-block offcalendar-alert" role="alert" style="display: none;">

            </div>

            <form id="login_form" method="POST" class="form-signin">
                <h2 class="form-signin-heading">Sign in</h2><br>
                <div id="error-label" class="alert alert-danger" role="alert" style="display: none;"></div><br>

                <fieldset>
                    <input type="email" class="form-control" placeholder="Email address" required="" autofocus="" name="email"><br>
                    <input type="password" pattern=".{8,20}" required title="Password must contain 8 to 20 characters" class="form-control" placeholder="Password" required="" name="password" /><br>
                    <button class="btn btn-lg btn-primary btn-block" type="submit">Sign in</button>

                    <br>
                    <a href="/welcome/register">Sign up</a>

                    <div id="loaderImage">
                        <img src="../../static/img/ajax-loader.gif" alt="loading">
                    </div>
                </fieldset>
            </form>
        </div>

        <script type="text/javascript" src="static/js/components/jquery/jquery.min.js"></script>
        <script type="text/javascript" src="static/js/OffCalendar.js"></script>
        <script type="text/javascript" src="static/js/OffCalendarHelper.js"></script>

        <script type="text/javascript">

            OffCalendar.initLogin();

        </script>
    </body>
</html>