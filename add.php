<?php
declare(strict_types=1);
require_once 'init.php';

$is_auth = rand(0, 1);

$user_name = 'Николай'; // укажите здесь ваше имя

$categories = get_categories($link);

$required = [
    'lot_name' => 'Введите наименование лота',
    'category' => 'Выберите категорию',
    'message' => 'Напишите описание лота',
    'lot_rate' => 'Введите начальную цену',
    'lot_step' => 'Введите шаг ставки',
    'lot_date' => 'Введите дату завершения торгов'
];

$format_errors = [
    'category' => 'Выберите категорию',
    'lot_rate' => 'В это поле нужно ввести число больше нуля',
    'lot_step' => 'В это поле нужно ввести число больше нуля',
    'lot_date' => 'Дату нужно ввести в формате ГГГГ-ММ-ДД'
];

$form_item_error_class = [
    'lot_name' => '',
    'category' => '',
    'message' => '',
    'lot_rate' => '',
    'lot_step' => '',
    'lot_image' => '',
    'form_add_lot' => '',
    'lot_date' => ''
];

$errors = [];
/*проверка на отправленность формы*/
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $new_lot = $_POST;
    /*ПРоверяем наличие и заполненность обязательных полей в массиве $_POST.
    Если поле не заполнено, добавляем имя этого поля в массив с ошибками*/
    $error_class = " form__item--invalid";
    foreach ($required as $key => $error_note) {
        if (empty($_POST[$key])) {
            $errors[$key] = $error_note;
            $form_item_error_class[$key] = $error_class;
            /*Проверяем соответствие формата и значений полей в массиве $_POST техническому заданию.
    Если поле заполнено не правильно, добавляем имя этого поля в массив с ошибками*/
        } elseif ($key === "lot_rate" && (gettype((int)$_POST[$key]) !== "integer" || (int)$_POST[$key] <= 0)) {
            $errors[$key] = $format_errors[$key];
            $form_item_error_class[$key] = $error_class;
        } elseif ($key === "lot_step" && (gettype((int)$_POST[$key]) !== "integer" || (int)$_POST[$key] <= 0)) {
            $errors[$key] = $format_errors[$key];
            $form_item_error_class[$key] = $error_class;
        } elseif ($key === "lot_date" && is_date_valid((string)$_POST[$key]) === false) {
            $errors[$key] = $format_errors[$key];
            $form_item_error_class[$key] = $error_class;
        } elseif ($key === "category" && $_POST[$key] === "Выберите категорию") {
            $errors[$key] = $format_errors[$key];
            $form_item_error_class[$key] = $error_class;
        }
    }
    /*получаем имя и путь к файлу изображения лота из массива $_FILES при их наличии в массиве*/
    if (isset($_FILES['lot_image']['name'])) {
        if ($_FILES['lot_image']['name'] !== "" && $_FILES['lot_image']['tmp_name'] !== "") {
            $tmp_name = $_FILES['lot_image']['tmp_name'];
            $path = $_FILES['lot_image']['name'];
            /*Проверяем файл картинки лота*/
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $file_type = finfo_file($finfo, $tmp_name);
            if ($file_type !== "image/jpeg" && $file_type !== "image/png") {
                $errors['lot_image'] = 'Загрузите изображение лота в правильном формате (png или jpeg)';
                $form_item_error_class['lot_image'] = $error_class;
            } else {
                /*в случае правильного формата переименовываем и перемещаем файл в папку uploads*/
                if ($file_type === "image/jpeg") {
                    $path = uniqid() . ".jpg";
                } else {
                    $path = uniqid() . ".png";
                }
                move_uploaded_file($tmp_name, 'uploads/' . $path);
                $new_lot['path'] = $path;
            }
        } else {
            $errors['lot_image'] = 'Вы не загрузили файл';
            $form_item_error_class['lot_image'] = $error_class;
        }

    } else {
        $errors['lot_image'] = 'Вы не загрузили файл';
        $form_item_error_class['lot_image'] = $error_class;
    }
    if (count($errors)) {
        /*Подключаем шаблон страницы добавления лота с формой,
        передаем в шаблон список ошибок, справочник с названиями и данные из формы*/
        $form_item_error_class['form_add_lot'] = " form--invalid";
        $page_content = include_template('add.php',
            ['categories' => $categories, 'errors' => $errors, 'new_lot' => $new_lot, 'form_item_error_class' => $form_item_error_class]);
        $layout_content = include_template('layout.php', [
            'content' => $page_content,
            'categories' => $categories,
            'new_lot' => $new_lot,
            'errors' => $errors,
            'form_item_error_class' => $form_item_error_class,
            'title' => 'Добавление лота'
        ]);
        print($layout_content);
    } else {
        add_new_lot($link, $new_lot);
    }
} else {
    /*Сборка шаблона страницы добавления лота*/
    $page_content = include_template('add.php', ['categories' => $categories, 'errors' => $errors, 'form_item_error_class' => $form_item_error_class]);
    $layout_content = include_template('layout.php', [
        'content' => $page_content,
        'categories' => $categories,
        'errors' => $errors,
        'form_item_error_class' => $form_item_error_class,
        'title' => 'Добавление лота'
    ]);
    print($layout_content);
}
