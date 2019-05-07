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
    <form class="form container" action="https://echo.htmlacademy.ru" method="post" autocomplete="off"> <!-- form--invalid -->
        <h2>Регистрация нового аккаунта</h2>
        <div class="form__item"> <!-- form__item--invalid -->
            <label for="email">E-mail <sup>*</sup></label>
            <input id="email" type="text" name="email" placeholder="Введите e-mail">
            <span class="form__error">Введите e-mail</span>
        </div>
        <div class="form__item">
            <label for="password">Пароль <sup>*</sup></label>
            <input id="password" type="password" name="password" placeholder="Введите пароль">
            <span class="form__error">Введите пароль</span>
        </div>
        <div class="form__item">
            <label for="name">Имя <sup>*</sup></label>
            <input id="name" type="text" name="name" placeholder="Введите имя">
            <span class="form__error">Введите имя</span>
        </div>
        <div class="form__item">
            <label for="message">Контактные данные <sup>*</sup></label>
            <textarea id="message" name="message" placeholder="Напишите как с вами связаться"></textarea>
            <span class="form__error">Напишите как с вами связаться</span>
        </div>
        <span class="form__error form__error--bottom">Пожалуйста, исправьте ошибки в форме.</span>
        <button type="submit" class="button">Зарегистрироваться</button>
        <a class="text-link" href="/login.php">Уже есть аккаунт</a>
    </form>
</main>