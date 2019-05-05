<link href="../css/flatpickr.min.css" rel="stylesheet">
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
    <?php
    $form_errors = "";
    $lot_name_error = "";
    $lot_name_error_note = "";
    $lot_category_error = "";
    $lot_category_error_note = "";
    $lot_message_error = "";
    $lot_message_error_note = "";
    $lot_rate_error = "";
    $lot_rate_error_note = "";
    $lot_step_error = "";
    $lot_step_error_note = "";
    $lot_date_error = "";
    $lot_date_error_note = "";
    $lot_image_error = "";
    $lot_image_error_note = "";
    if (isset($errors)) {
        if (count($errors)) {
            $form_errors = " form--invalid";
            if (isset($errors['lot_name'])) {
                $lot_name_error = " form__item--invalid";
                $lot_name_error_note = $errors['lot_name'];
            }
            if (isset($errors['category'])) {
                $lot_category_error = " form__item--invalid";
                $lot_category_error_note = $errors['category'];
            }
            if (isset($errors['message'])) {
                $lot_message_error = " form__item--invalid";
                $lot_message_error_note = $errors['message'];
            }
            if (isset($errors['lot_rate'])) {
                $lot_rate_error = " form__item--invalid";
                $lot_rate_error_note = $errors['lot_rate'];
            }
            if (isset($errors['lot_step'])) {
                $lot_step_error = " form__item--invalid";
                $lot_step_error_note = $errors['lot_step'];
            }
            if (isset($errors['lot_date'])) {
                $lot_date_error = " form__item--invalid";
                $lot_date_error_note = $errors['lot_date'];
            }
            if (isset($errors['lot_image'])) {
                $lot_image_error = " form__item--invalid";
                $lot_image_error_note = $errors['lot_image'];
            }
        } else {
            $form_errors = "";
        }
    }
    ?>
    <form class="form form--add-lot container<?= $form_errors; ?>" action="/add.php" method="post"
          enctype="multipart/form-data">
        <!-- form--invalid -->
        <h2>Добавление лота</h2>
        <div class="form__container-two">
            <div class="form__item<?= $lot_name_error; ?>"> <!-- form__item--invalid -->
                <label for="lot-name">Наименование <sup>*</sup></label>
                <?php $value = isset($new_lot['lot_name']) ? ' value="' . $new_lot['lot_name'] . '"' : ""; ?>
                <input id="lot-name" type="text" name="lot_name" placeholder="Введите наименование лота"<?= $value; ?>>
                <span class="form__error"><?= $lot_name_error_note; ?></span>
            </div>
            <div class="form__item<?= $lot_category_error; ?>">
                <label for="category">Категория <sup>*</sup></label>
                <select id="category" name="category">
                    <option>Выберите категорию</option>
                    <?php foreach ($categories as $categories_option): ?>
                        <?php if (isset($new_lot['category'])) {
                            if ((int)$new_lot['category'] === $categories_option["id"]) {
                                $value = ' selected';
                            } else {
                                $value = '';
                            }
                        } else {
                            $value = '';
                        } ?>
                        <option<?= $value; ?>
                                value="<?= $categories_option["id"]; ?>"><?= $categories_option["name"]; ?></option>
                    <?php endforeach ?>
                </select>
                <span class="form__error"><?= $lot_category_error_note; ?></span>
            </div>
        </div>
        <div class="form__item form__item--wide<?= $lot_message_error; ?>">
            <label for="message">Описание <sup>*</sup></label>
            <?php $value = isset($new_lot['message']) ? $new_lot['message'] : ""; ?>
            <textarea id="message" name="message" placeholder="Напишите описание лота"><?= $value; ?></textarea>
            <span class="form__error"><?= $lot_message_error_note; ?></span>
        </div>
        <div class="form__item form__item--file<?= $lot_image_error; ?>">
            <label>Изображение <sup>*</sup></label>
            <div class="form__input-file">
                <input class="visually-hidden" type="file" id="lot-img" value="" name="lot_image">
                <label for="lot-img">
                    Добавить
                </label>
                <span class="form__error"><?= $lot_image_error_note; ?></span>
            </div>

        </div>
        <div class="form__container-three">
            <div class="form__item form__item--small<?= $lot_rate_error; ?>">
                <label for="lot-rate">Начальная цена <sup>*</sup></label>
                <?php $value = isset($new_lot['lot_rate']) ? ' value="' . $new_lot['lot_rate'] . '"' : ""; ?>
                <input id="lot-rate" type="text" name="lot_rate" placeholder="0"<?= $value; ?>>
                <span class="form__error"><?= $lot_rate_error_note; ?></span>
            </div>
            <div class="form__item form__item--small<?= $lot_step_error; ?>">
                <label for="lot-step">Шаг ставки <sup>*</sup></label>
                <?php $value = isset($new_lot['lot_step']) ? ' value="' . $new_lot['lot_step'] . '"' : ""; ?>
                <input id="lot-step" type="text" name="lot_step" placeholder="0"<?= $value; ?>>
                <span class="form__error"><?= $lot_step_error_note; ?></span>
            </div>
            <div class="form__item<?= $lot_date_error; ?>">
                <label for="lot-date">Дата окончания торгов <sup>*</sup></label>
                <?php $value = isset($new_lot['lot_date']) ? ' value="' . $new_lot['lot_date'] . '"' : ""; ?>
                <input class="form__input-date" id="lot-date" type="text" name="lot_date"
                       placeholder="Введите дату в формате ГГГГ-ММ-ДД"<?= $value; ?>>
                <span class="form__error"><?= $lot_date_error_note; ?></span>
            </div>
        </div>
        <span class="form__error form__error--bottom">Пожалуйста, исправьте ошибки в форме.</span>
        <button type="submit" class="button">Добавить лот</button>
    </form>
</main>
