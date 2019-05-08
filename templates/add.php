<link href="../css/flatpickr.min.css" rel="stylesheet">

<form class="form form--add-lot container<?= $form_item_error_class['form_add_lot']; ?>" action="/add.php" method="post"
      enctype="multipart/form-data">
    <!-- form--invalid -->
    <h2>Добавление лота</h2>
    <div class="form__container-two">
        <div class="form__item<?= $form_item_error_class['lot_name']; ?>"> <!-- form__item--invalid -->
            <label for="lot-name">Наименование <sup>*</sup></label>
            <?php $value = isset($new_lot['lot_name']) ? ' value="' . $new_lot['lot_name'] . '"' : ""; ?>
            <input id="lot-name" type="text" name="lot_name" placeholder="Введите наименование лота"<?= $value; ?>>
            <span class="form__error"><?= $errors['lot_name']; ?></span>
        </div>
        <div class="form__item<?= $form_item_error_class['category']; ?>">
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
            <span class="form__error"><?= $errors['category']; ?></span>
        </div>
    </div>
    <div class="form__item form__item--wide<?= $form_item_error_class['message']; ?>">
        <label for="message">Описание <sup>*</sup></label>
        <?php $value = isset($new_lot['message']) ? $new_lot['message'] : ""; ?>
        <textarea id="message" name="message" placeholder="Напишите описание лота"><?= $value; ?></textarea>
        <span class="form__error"><?= $errors['message']; ?></span>
    </div>
    <div class="form__item form__item--file<?= $form_item_error_class['lot_image']; ?>">
        <label>Изображение <sup>*</sup></label>
        <div class="form__input-file">
            <input class="visually-hidden" type="file" id="lot-img" value="" name="lot_image">
            <label for="lot-img">
                Добавить
            </label>
            <span class="form__error"><?= $errors['lot_image']; ?></span>
        </div>
    </div>
    <div class="form__container-three">
        <div class="form__item form__item--small<?= $form_item_error_class['lot_rate']; ?>">
            <label for="lot-rate">Начальная цена <sup>*</sup></label>
            <?php $value = isset($new_lot['lot_rate']) ? ' value="' . $new_lot['lot_rate'] . '"' : ""; ?>
            <input id="lot-rate" type="text" name="lot_rate" placeholder="0"<?= $value; ?>>
            <span class="form__error"><?= $errors['lot_rate']; ?></span>
        </div>
        <div class="form__item form__item--small<?= $form_item_error_class['lot_step']; ?>">
            <label for="lot-step">Шаг ставки <sup>*</sup></label>
            <?php $value = isset($new_lot['lot_step']) ? ' value="' . $new_lot['lot_step'] . '"' : ""; ?>
            <input id="lot-step" type="text" name="lot_step" placeholder="0"<?= $value; ?>>
            <span class="form__error"><?= $errors['lot_step']; ?></span>
        </div>
        <div class="form__item<?= $form_item_error_class['lot_date']; ?>">
            <label for="lot-date">Дата окончания торгов <sup>*</sup></label>
            <?php $value = isset($new_lot['lot_date']) ? ' value="' . $new_lot['lot_date'] . '"' : ""; ?>
            <input class="form__input-date" id="lot-date" type="text" name="lot_date"
                   placeholder="Введите дату в формате ГГГГ-ММ-ДД"<?= $value; ?>>
            <span class="form__error"><?= $errors['lot_date']; ?></span>
        </div>
    </div>
    <span class="form__error form__error--bottom">Пожалуйста, исправьте ошибки в форме.</span>
    <button type="submit" class="button">Добавить лот</button>
</form>
