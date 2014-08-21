
<?php if (isset($message)) : ?>
    <div class="message"><?= $message ?></div>
<?php endif; ?>

<form action="<?= base_url() ?>" method="POST" class="form-signin medium">

    <h2 class="form-signin-heading">Sign up</h2>

    <?php if (isset($errmsg)) : ?><p class="error"><?= $errmsg ?></p><?php endif; ?>

    <br>
    <fieldset>
        <input type="name" class="form-control" placeholder="Name" required="" autofocus="" value="<?= set_value('name') ?>" name="name">
        <span class="err"><?= form_error('name') ?></span>
        <br>
        <input type="email" class="form-control" placeholder="Email address" required="" autofocus="" value="<?= set_value('email') ?>" name="email">
        <span class="err"><?= form_error('email') ?></span>
        <br>
        <input type="password" class="form-control" placeholder="Password" required="" value="<?= set_value('password') ?>" name="password" />
        <span class="err"><?= form_error('password') ?></span>
        <br>
        <input type="password" class="form-control" placeholder="Repeat password" required="" value="<?= set_value('password') ?>" name="password_confirm" />
        <span class="err"><?= form_error('password_confirm') ?></span>
        <br>
        <button class="btn btn-lg btn-primary btn-block" type="submit">Sign up</button>
    </fieldset>

</form>