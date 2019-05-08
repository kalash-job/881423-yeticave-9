<main>
    <nav class="nav">
        <ul class="nav__list container">
            <?php foreach ($categories as $categories_option): ?>
                <li class="nav__item">
                    <a href="pages/all-lots.html"><?= $categories_option["name"]; ?></a>
                </li>
            <?php endforeach ?>
        </ul>
    </nav>
    <form class="form container<?= $sign_up_form_error_class['form_add_user']; ?>" action="/sign-up.php" method="post"
          autocomplete="off" enctype="multipart/form-data"> <!-- form--invalid -->
        <h2>Регистрация нового аккаунта</h2>
        <div class="form__item<?= $sign_up_form_error_class['email']; ?>"> <!-- form__item--invalid -->
            <label for="email">E-mail <sup>*</sup></label>
            <?php $value = isset($new_user['email']) ? ' value="' . $new_user['email'] . '"' : ""; ?>
            <input id="email" type="text" name="email" placeholder="Введите e-mail"<?= $value; ?>>
            <span class="form__error"><?= $sign_up_errors['email']; ?></span>
        </div>
        <div class="form__item<?= $sign_up_form_error_class['password']; ?>">
            <label for="password">Пароль <sup>*</sup></label>
            <?php $value = isset($new_user['password']) ? ' value="' . $new_user['password'] . '"' : ""; ?>
            <input id="password" type="password" name="password" placeholder="Введите пароль"<?= $value; ?>>
            <span class="form__error"><?= $sign_up_errors['password']; ?></span>
        </div>
        <div class="form__item<?= $sign_up_form_error_class['name']; ?>">
            <label for="name">Имя <sup>*</sup></label>
            <?php $value = isset($new_user['name']) ? ' value="' . $new_user['name'] . '"' : ""; ?>
            <input id="name" type="text" name="name" placeholder="Введите имя"<?= $value; ?>>
            <span class="form__error"><?= $sign_up_errors['name']; ?></span>
        </div>
        <div class="form__item<?= $sign_up_form_error_class['message']; ?>">
            <label for="message">Контактные данные <sup>*</sup></label>
            <?php $value = isset($new_user['message']) ? $new_user['message'] : ""; ?>
            <textarea id="message" name="message" placeholder="Напишите как с вами связаться"><?= $value; ?></textarea>
            <span class="form__error"><?= $sign_up_errors['message']; ?></span>
        </div>
        <div class="form__item form__item--file<?= $sign_up_form_error_class['avatar']; ?>">
            <label>Аватар</label>
            <div class="form__input-file">
                <input class="visually-hidden" type="file" id="avatar" value="" name="avatar">
                <label for="avatar">
                    Добавить
                </label>
                <span class="form__error"><?= $sign_up_errors['avatar']; ?></span>
            </div>
        </div>
        <span class="form__error form__error--bottom">Пожалуйста, исправьте ошибки в форме.</span>
        <button type="submit" class="button">Зарегистрироваться</button>
        <a class="text-link" href="/login.php">Уже есть аккаунт</a>
    </form>
</main>