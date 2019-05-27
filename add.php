<?php
declare(strict_types=1);
require_once 'init.php';

if (!isset($_SESSION['user'])) {
    header("Location: /403.php");
    exit();
}
$categories = get_categories($link);

$required_error_messages = [
    'lot_name' => 'Введите наименование лота',
    'category' => 'Выберите категорию',
    'message' => 'Напишите описание лота',
    'lot_rate' => 'Введите начальную цену',
    'lot_step' => 'Введите шаг ставки',
    'lot_date' => 'Введите дату завершения торгов'
];

$lot_fields = [
    'lot_name' => 'Введите наименование лота',
    'category' => 'Выберите категорию',
    'message' => 'Напишите описание лота',
    'lot_rate' => 'Введите начальную цену',
    'lot_step' => 'Введите шаг ставки',
    'lot_date' => 'Введите дату завершения торгов'
];

$format_error_messages = [
    'category' => 'Выберите категорию',
    'lot_rate' => 'В это поле нужно ввести число от 1 до 4294967295',
    'lot_step' => 'В это поле нужно ввести целое число от 1 до 4294967295',
    'lot_date' => 'Дату нужно ввести, начиная с завтрашней, в формате ГГГГ-ММ-ДД',
    'lot_name' => 'В это поле можно ввести не более 255 символов',
    'message' => 'В это поле можно ввести не более 2000 символов'
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

$errors = [
    'lot_name' => null,
    'category' => null,
    'message' => null,
    'lot_rate' => null,
    'lot_step' => null,
    'lot_image' => null,
    'form_add_lot' => null,
    'lot_date' => null
];
$num_errors = 0;
/*проверка на отправленность формы*/
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_lot = $_POST;
    /*ПРоверяем наличие и заполненность обязательных полей в массиве $_POST.
    Если поле не заполнено, добавляем имя этого поля в массив с ошибками*/
    $error_class = " form__item--invalid";
    foreach ($lot_fields as $key => $error_note) {
        /*Проверяем заполненность обязательных полей, исключая пробелы*/
        if (isset($required_error_messages[$key]) && empty(trim($_POST[$key]))) {
            $errors[$key] = $error_note;
            $form_item_error_class[$key] = $error_class;
            $num_errors += 1;
            /*Проверяем соответствие формата и значений полей в массиве $_POST техническому заданию.
    Если поле заполнено неправильно, добавляем имя этого поля в массив с ошибками*/
            /*Проверить на предельное значение (с предварительным переводом во float, округлением, переводом в int),
            на возможность преобразования из строки во float без потери значения.*/
        } elseif ($key === "lot_rate" && (((int)ceil((float)str_replace(',', '.',
                        $_POST[$key])) <= 0) || (int)ceil((float)str_replace(',', '.', $_POST[$key])) > 2147483647)) {
            $errors[$key] = $format_error_messages[$key];
            $form_item_error_class[$key] = $error_class;
            $num_errors += 1;
            /*Проверить на предельное значение, на наличие посторонних для int символов,
            на возможность преобразования из строки в int без потери значения.*/
        } elseif ($key === "lot_step" && ((int)$_POST[$key] <= 0 || (int)($_POST[$key]) > 2147483647 || preg_replace('/[0-9]/',
                    '', trim($_POST[$key])) !== '')) {
            $errors[$key] = $format_error_messages[$key];
            $form_item_error_class[$key] = $error_class;
            $num_errors += 1;
            /*Проверить на формат даты и, чтобы дата была не раньше завтрашнего дня*/
        } elseif ($key === "lot_date" && (is_date_valid((string)$_POST[$key]) === false || is_date_after_today((string)$_POST[$key]) === false)) {
            $errors[$key] = $format_error_messages[$key];
            $form_item_error_class[$key] = $error_class;
            $num_errors += 1;
            /*Проверить на наличие категории*/
        } elseif ($key === "category" && get_category_name($link, (int)$_POST[$key]) === null) {
            $errors[$key] = $format_error_messages[$key];
            $form_item_error_class[$key] = $error_class;
            $num_errors += 1;
            /*Проверить на предельное значение VARCHAR(255)*/
        } elseif ($key === "lot_name" && mb_strlen($_POST[$key]) > 255) {
            $errors[$key] = $format_error_messages[$key];
            $form_item_error_class[$key] = $error_class;
            $num_errors += 1;
            /*Проверить на предельное значение VARCHAR(2000)*/
        } elseif ($key === "message" && mb_strlen($_POST[$key]) > 2000) {
            $errors[$key] = $format_error_messages[$key];
            $form_item_error_class[$key] = $error_class;
            $num_errors += 1;
        }
    }
    /*получаем имя и путь к файлу изображения лота из массива $_FILES при их наличии в массиве*/
    if (isset($_FILES['lot_image']['name']) && $_FILES['lot_image']['name'] !== "" && $_FILES['lot_image']['tmp_name'] !== "") {
        $tmp_name = $_FILES['lot_image']['tmp_name'];
        $path = $_FILES['lot_image']['name'];
        /*Проверяем файл картинки лота*/
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $file_type = finfo_file($finfo, $tmp_name);
        if ($file_type !== "image/jpeg" && $file_type !== "image/png") {
            $errors['lot_image'] = 'Загрузите изображение лота в правильном формате (png или jpeg)';
            $form_item_error_class['lot_image'] = $error_class;
            $num_errors += 1;
        }
        /*в случае правильного формата и отсутствия ошибок в форме переименовываем и перемещаем файл в папку uploads*/
        if ($file_type === "image/jpeg" && $num_errors === 0) {
            $path = uniqid() . ".jpg";
            move_uploaded_file($tmp_name, 'uploads/' . $path);
            $new_lot['path'] = $path;
        } elseif ($num_errors === 0) {
            $path = uniqid() . ".png";
            move_uploaded_file($tmp_name, 'uploads/' . $path);
            $new_lot['path'] = $path;
        }
    } else {
        $errors['lot_image'] = 'Вы не загрузили файл';
        $form_item_error_class['lot_image'] = $error_class;
        $num_errors += 1;
    }
    if ($num_errors !== 0) {
        /*Подключаем шаблон страницы добавления лота с формой,
        передаем в шаблон список ошибок, справочник с названиями и данные из формы*/
        $form_item_error_class['form_add_lot'] = " form--invalid";
        $top_menu = include_template('top-menu.php',
            ['categories' => $categories]);
        $page_content = include_template('add.php',
            [
                'categories' => $categories,
                'errors' => $errors,
                'new_lot' => $new_lot,
                'form_item_error_class' => $form_item_error_class,
                'user_session' => $user_session
            ]);
        $layout_content = include_template('layout.php', [
            'top_menu' => $top_menu,
            'content' => $page_content,
            'categories' => $categories,
            'new_lot' => $new_lot,
            'errors' => $errors,
            'form_item_error_class' => $form_item_error_class,
            'user_session' => $user_session,
            'pagination' => '',
            'title' => 'Добавление лота'
        ]);
        print($layout_content);
    } else {
        $user_id = $_SESSION['user'];
        /* Корректируем данные, полученные из формы перед вставкой в БД, приводим их к правильным типам*/
        $new_lot['lot_rate'] = (int)ceil((float)str_replace(',', '.', $new_lot['lot_rate']));
        $new_lot['lot_step'] = (int)$new_lot['lot_step'];
        $new_lot['lot_name'] = trim($new_lot['lot_name']);
        $new_lot['message'] = trim($new_lot['message']);
        $new_id = get_new_lot_id($link, $new_lot, $user_id);
        $path_lot_page = "Location: /lot.php?id=" . (string)$new_id;
        header($path_lot_page);
    }
} else {
    /*Сборка шаблона страницы добавления лота*/
    $top_menu = include_template('top-menu.php',
        ['categories' => $categories]);
    $page_content = include_template('add.php',
        [
            'categories' => $categories,
            'errors' => $errors,
            'form_item_error_class' => $form_item_error_class,
            'user_session' => $user_session
        ]);
    $layout_content = include_template('layout.php', [
        'top_menu' => $top_menu,
        'content' => $page_content,
        'categories' => $categories,
        'errors' => $errors,
        'form_item_error_class' => $form_item_error_class,
        'user_session' => $user_session,
        'pagination' => '',
        'title' => 'Добавление лота'
    ]);
    print($layout_content);
}
