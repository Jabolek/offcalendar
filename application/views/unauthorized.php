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
            <br>
            Unauthorized access! Please <a href="/">sign in</a>!
        </div>
    </body>
</html>