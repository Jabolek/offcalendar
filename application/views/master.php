<!DOCTYPE html>
<html>
    <head>
        <meta charset=utf-8 />
        <base href="<?= base_url() ?>" />
        <title>IndexedDB TODO List</title>
        <link rel="stylesheet" type="text/css" media="screen" href="static/bootstrap.min.css" />
        <link rel="stylesheet" type="text/css" media="screen" href="static/bootstrap-theme.css" />
        <link rel="stylesheet" type="text/css" media="screen" href="static/style.css" />
        <script type="text/javascript" src="static/js/jquery.js"></script>
        <script type="text/javascript" src="static/js/jquery.jeditable.js"></script>
        <script type="text/javascript" src="static/js/todo.js"></script>
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    </head>
    <body>
        <div style="width:300px; margin: 15px auto;">

            <h3 class="text-center">IndexedDB Todo List</h3>

            <p id="buttons">
                <a href="<?= current_url() ?>#" data-ajaxurl="<?= base_url() ?>todos/synchronize" id="synchronize" class="btn btn-primary btn-block">Synchronize</a>
            </p>

            <table class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th>Todo</th>
                        <th style="width:100px;">Action</th>
                    </tr>
                </thead>
                <tbody id="todoItems"></tbody>
                <tfoot>
                    <tr>
                        <td colspan="2">
                            <form action="#" method="POST" id="addtodo">
                                <input type="text" id="todo" name="todo" placeholder="What do you need to do?" class="form-control" style="margin-bottom:8px;">
                                <input type="submit" value="Add Todo Item" class="btn btn-default btn-block">
                            </form>
                        </td>
                    </tr>
                </tfoot>
            </table>

        </div>
    </body>
</html>