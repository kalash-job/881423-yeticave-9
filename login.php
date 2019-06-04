<?php
declare(strict_types=1);
require_once 'init.php';

if (isset($_SESSION['user'])) {
    header("Location: /index.php");
    exit();
}
$categories = get_categories($link);
$form_login_error_class = [
    'email' => '',
    'password' => '',
    'login_form' => ''
];

$login_errors = [
    'email' => null,
    'password' => null
];

$login_required_error_messages = [
    'email' => 'Введите e-mail',
    'password' => 'Введите пароль без пробелов'
];

$login_form = [];
$login_field_error_class = ' form__item--invalid';
$login_form_error_class = ' form--invalid';
$all_login_error_number = 0;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login_form = $_POST;

    $required = ['email', 'password'];
    foreach ($required as $key) {
        $login_error_number = 0;
        if (empty($login_form[$key]) || isset($login_form[$key]) && empty(trim($login_form[$key]))) {
            $login_errors[$key] = $login_required_error_messages[$key];
            $form_login_error_class[$key] = $login_field_error_class;
            $form_login_error_class['login_form'] = $login_form_error_class;
            $login_error_number += 1;
        }
        if ($login_error_number === 0 && $key === 'email') {
            $user = get_user_data($link, trim($login_form['email']));
            $user = $user[0] ?? null;
            /*Проверка наличия email в БД*/
            if ($user['email'] === null) {
                $login_errors['email'] = 'Вы ввели неверный email';
                $form_login_error_class['email'] = $login_field_error_class;
                $form_login_error_class['login_form'] = $login_form_error_class;
                $login_error_number += 1;
            }
        }
        $all_login_error_number += $login_error_number;
    }

    if ($all_login_error_number === 0) {
        /*Проверка правильности введенного пароля*/
        if (!password_verify(trim($login_form['password']), $user['password'])) {
            $login_errors['password'] = 'Вы ввели неверный пароль';
            $form_login_error_class['password'] = $login_field_error_class;
            $form_login_error_class['login_form'] = $login_form_error_class;
            $all_login_error_number += 1;
        } else {
            $_SESSION['user'] = $user['id'];
            header("Location: /index.php");
            exit();
        }
    }
}
/*Сборка шаблона страницы входа зарегистрированного пользователя*/
$top_menu = include_template('top-menu.php',
    ['categories' => $categories]);
$page_content = include_template('login.php',
    [
        'categories' => $categories,
        'login_errors' => $login_errors,
        'form_login_error_class' => $form_login_error_class,
        'login_form' => $login_form,
        'user_session' => $user_session
    ]);
$layout_content = include_template('layout.php', [
    'top_menu' => $top_menu,
    'content' => $page_content,
    'categories' => $categories,
    'login_errors' => $login_errors,
    'form_login_error_class' => $form_login_error_class,
    'login_form' => $login_form,
    'user_session' => $user_session,
    'pagination' => '',
    'title' => 'Вход'
]);
print($layout_content);
