
<?php if (isset($message)) : ?>
    <div class="message"><?= $message ?></div>
<?php endif; ?>

<form action="<?= current_url() ?>" method="POST" class="form-signin medium">

    <h2 class="form-signin-heading">Sign in</h2>
    <br>
    
    <?php if (isset($errmsg)) : ?><div class="alert alert-danger" role="alert"><?= $errmsg ?></div><?php endif; ?>

    <br>
    <fieldset>
        <input type="email" class="form-control" placeholder="Email address" required="" autofocus="" value="<?= set_value('email') ?>" name="email">
        <span class="err"><?= form_error('email') ?></span>
        <br>
        <input type="password" class="form-control" placeholder="Password" required="" value="<?= set_value('password') ?>" name="password" />
        <span class="err"><?= form_error('password') ?></span>
        <br>
        <button class="btn btn-lg btn-primary btn-block" type="submit">Sign in</button>
    </fieldset>

</form>