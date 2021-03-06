<?php
declare(strict_types=1);
require_once 'init.php';

if (isset($_SESSION['user'])) {
    header("Location: /index.php");
    exit();
}
$categories = get_categories($link);

$sign_up_required_error_messages = [
    'email' => 'Введите e-mail',
    'password' => 'Введите пароль',
    'name' => 'Введите имя',
    'message' => 'Напишите, как с вами связаться'
];

$sign_up_lot_fields = [
    'email' => 'Введите e-mail',
    'password' => 'Введите пароль',
    'name' => 'Введите имя',
    'message' => 'Напишите, как с вами связаться'
];

$sign_up_format_error_messages = [
    'email' => 'Данный email уже зарегистрирован на сайте, введите другой email',
    'password' => 'Введите пароль без пробелов',
    'name' => 'В это поле можно ввести не более 255 символов',
    'message' => 'В это поле можно ввести не более 500 символов',
    'avatar' => 'Загрузите изображение аватара в правильном формате (png или jpeg)'
];

$sign_up_form_error_class = [
    'email' => '',
    'password' => '',
    'name' => '',
    'message' => '',
    'avatar' => '',
    'form_add_user' => ''
];

$sign_up_errors = [
    'email' => null,
    'password' => null,
    'name' => null,
    'message' => null,
    'avatar' => null,
    'form_add_user' => null
];

$sign_up_num_errors = 0;

/*проверка на отправленность формы*/
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_user = $_POST;
    /*Проверяем наличие и заполненность обязательных полей в массиве $_POST.
    Если поле не заполнено, добавляем имя этого поля в массив с ошибками*/
    $sign_up_error_class = " form__item--invalid";
    foreach ($sign_up_lot_fields as $key => $error_note) {
        /*Проверяем обязательные поля*/
        if (isset($sign_up_required_error_messages[$key]) && (isset($_POST[$key]) && empty(trim($_POST[$key])) || empty($_POST[$key]))) {
            $sign_up_errors[$key] = $error_note;
            $sign_up_form_error_class[$key] = $sign_up_error_class;
            $sign_up_num_errors += 1;
            /*Проверяем соответствие значения поля email в массиве $_POST валидному email-адресу, а также его уникальность.
    Если поле заполнено не правильно, добавляем имя этого поля в массив с ошибками*/
        } elseif ($key === "email" && (filter_var($_POST[$key], FILTER_VALIDATE_EMAIL) === false)) {
            $sign_up_errors[$key] = 'Введите email в правильном формате: info@domain.com';
            $sign_up_form_error_class[$key] = $sign_up_error_class;
            $sign_up_num_errors += 1;
        } elseif ($key === "email" && (check_unique_email($link, $_POST[$key]) === false)) {
            $sign_up_errors[$key] = $sign_up_format_error_messages[$key];
            $sign_up_form_error_class[$key] = $sign_up_error_class;
            $sign_up_num_errors += 1;
            /*Проверяем пароль на наличие пробелов*/
        } elseif ($key === "password" && ($_POST[$key] !== str_replace(' ', '', $_POST[$key]))) {
            $sign_up_errors[$key] = $sign_up_format_error_messages[$key];
            $sign_up_form_error_class[$key] = $sign_up_error_class;
            $sign_up_num_errors += 1;
            /*Проверить на предельное значение VARCHAR(255)*/
        } elseif ($key === "name" && mb_strlen($_POST[$key]) > 255) {
            $sign_up_errors[$key] = $sign_up_format_error_messages[$key];
            $sign_up_form_error_class[$key] = $sign_up_error_class;
            $sign_up_num_errors += 1;
            /*Проверить на предельное значение VARCHAR(500)*/
        } elseif ($key === "message" && mb_strlen($_POST[$key]) > 500) {
            $sign_up_errors[$key] = $sign_up_format_error_messages[$key];
            $sign_up_form_error_class[$key] = $sign_up_error_class;
            $sign_up_num_errors += 1;
        }
    }
    /*получаем имя и путь к файлу изображения аватара из массива $_FILES при их наличии в массиве*/
    if (isset($_FILES['avatar']['name']) && $_FILES['avatar']['name'] !== "" && $_FILES['avatar']['tmp_name'] !== "") {
        $tmp_name = $_FILES['avatar']['tmp_name'];
        $path = $_FILES['avatar']['name'];
        $file_type = mime_content_type($tmp_name);
        /*Проверяем файл картинки аватара*/
        if ($file_type !== "image/jpeg" && $file_type !== "image/png") {
            $sign_up_errors['avatar'] = $sign_up_format_error_messages['avatar'];
            $sign_up_form_error_class['avatar'] = $sign_up_error_class;
            $sign_up_num_errors += 1;
        }
        /*в случае правильного формата  и отсутствия ошибок в форме переименовываем и перемещаем файл в папку uploads*/
        if ($file_type === "image/jpeg" && $sign_up_num_errors === 0) {
            $path = uniqid() . ".jpg";
            move_uploaded_file($tmp_name, 'uploads/' . $path);
            $new_user['path'] = $path;
        } elseif ($sign_up_num_errors === 0) {
            $path = uniqid() . ".png";
            move_uploaded_file($tmp_name, 'uploads/' . $path);
            $new_user['path'] = $path;
        }
    }
    if ($sign_up_num_errors !== 0) {
        /*Подключаем шаблон страницы регистрации пользователя с формой,
        передаем в шаблон список ошибок, справочник с названиями и данные из формы*/
        $sign_up_form_error_class['form_add_user'] = " form--invalid";
        $top_menu = include_template('top-menu.php',
            ['categories' => $categories]);
        $page_content = include_template('sign-up.php',
            [
                'categories' => $categories,
                'sign_up_errors' => $sign_up_errors,
                'new_user' => $new_user,
                'sign_up_form_error_class' => $sign_up_form_error_class,
                'user_session' => $user_session
            ]);
        $layout_content = include_template('layout.php', [
            'top_menu' => $top_menu,
            'content' => $page_content,
            'categories' => $categories,
            'sign_up_errors' => $sign_up_errors,
            'new_user' => $new_user,
            'sign_up_form_error_class' => $sign_up_form_error_class,
            'user_session' => $user_session,
            'pagination' => '',
            'title' => 'Регистрация'
        ]);
        print($layout_content);
    } else {
        $new_user['password'] = password_hash($new_user['password'], PASSWORD_DEFAULT);
        add_new_user($link, $new_user);
        header('Location: /login.php');
    }
} else {
    /*Сборка шаблона страницы регистрации нового пользователя*/
    $top_menu = include_template('top-menu.php',
        ['categories' => $categories]);
    $page_content = include_template('sign-up.php',
        [
            'categories' => $categories,
            'sign_up_errors' => $sign_up_errors,
            'sign_up_form_error_class' => $sign_up_form_error_class,
            'user_session' => $user_session
        ]);
    $layout_content = include_template('layout.php', [
        'top_menu' => $top_menu,
        'content' => $page_content,
        'categories' => $categories,
        'sign_up_errors' => $sign_up_errors,
        'sign_up_form_error_class' => $sign_up_form_error_class,
        'user_session' => $user_session,
        'pagination' => '',
        'title' => 'Регистрация'
    ]);
    print($layout_content);
}
