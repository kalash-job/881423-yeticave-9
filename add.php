<?php
declare(strict_types=1);
require_once 'init.php';

$is_auth = rand(0, 1);

$user_name = 'Николай'; // укажите здесь ваше имя

$categories = get_categories($link);
/*проверка на отправленность формы*/
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $new_lot = $_POST;
    $required = ['lot_name', 'category', 'message', 'lot_rate', 'lot_step', 'lot_date'];
    $errors = [];
    /*ПРоверяем наличие и заполненность обязательных полей в массиве $_POST.
    Если поле не заполнено, добавляем имя этого поля в массив с ошибками*/
    foreach ($required as $key) {
        if (empty($_POST[$key])) {
            $errors[$key] = 'Это поле необходимо заполнить';
            /*Проверяем соответствие формата и значений полей в массиве $_POST техническому заданию.
    Если поле заполнено не правильно, добавляем имя этого поля в массив с ошибками*/
        } elseif ($key === "lot_rate" && (gettype((int)$_POST[$key]) !== "integer" || (int)$_POST[$key] <= 0)) {
            $errors[$key] = 'В это поле нужно ввести число больше нуля';
        } elseif ($key === "lot_step" && (gettype((int)$_POST[$key]) !== "integer" || (int)$_POST[$key] <= 0)) {
            $errors[$key] = 'В это поле нужно ввести число больше нуля';
        } elseif ($key === "lot_date" && is_date_valid((string)$_POST[$key]) === false) {
            $errors[$key] = 'Дату нужно ввести в формате ГГГГ-ММ-ДД';
        } elseif ($key === "category" && $_POST[$key] === "Выберите категорию") {
            $errors[$key] = 'Не выбрана категория';
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
                $errors['file'] = 'Загрузите изображение лота в правильном формате (png или jpeg)';
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
            $errors['file'] = 'Вы не загрузили файл';
        }

    } else {
        $errors['file'] = 'Вы не загрузили файл';
    }
    if (count($errors)) {
        /*Подключаем шаблон страницы добавления лота с формой,
        передаем в шаблон список ошибок, справочник с названиями и данные из формы*/
        $page_content = include_template('add.php', ['categories' => $categories, 'errors' => $errors, 'new_lot' => $new_lot]);
        $layout_content = include_template('layout.php', [
            'content' => $page_content,
            'categories' => $categories,
            'new_lot' => $new_lot,
            'errors' => $errors,
            'title' => 'Добавление лота'
        ]);
        print($layout_content);
    } else {
        add_new_lot($link, $new_lot);
    }
} else {
    /*Сборка шаблона страницы добавления лота*/
    $page_content = include_template('add.php', ['categories' => $categories]);
    $layout_content = include_template('layout.php', [
        'content' => $page_content,
        'categories' => $categories,
        'title' => 'Добавление лота'
    ]);
    print($layout_content);
}


