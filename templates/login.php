<form class="form container<?= $form_login_error_class['login_form']; ?>" action="/login.php" method="post"> <!-- form--invalid -->
    <h2>Вход</h2>
    <div class="form__item<?= $form_login_error_class['email']; ?>"> <!-- form__item--invalid -->
        <label for="email">E-mail <sup>*</sup></label>
        <?php $value = isset($login_form['email']) ? ' value="' . $login_form['email'] . '"' : ""; ?>
        <input id="email" type="text" name="email" placeholder="Введите e-mail"<?= $value; ?>>
        <span class="form__error"><?= $login_errors['email']; ?></span>
    </div>
    <div class="form__item form__item--last<?= $form_login_error_class['password']; ?>">
        <label for="password">Пароль <sup>*</sup></label>
        <?php $value = isset($login_form['password']) ? ' value="' . $login_form['password'] . '"' : ""; ?>
        <input id="password" type="password" name="password" placeholder="Введите пароль"<?= $value; ?>>
        <span class="form__error"><?= $login_errors['password']; ?></span>
    </div>
    <button type="submit" class="button">Войти</button>
</form>