<?php
declare(strict_types=1);
require_once 'init.php';

$categories = get_categories($link);


/*Сборка шаблона страницы регистрации нового пользователя*/
$page_content = include_template('sign-up.php',
    ['categories' => $categories/*, 'errors' => $errors, 'form_item_error_class' => $form_item_error_class*/]);
$layout_content = include_template('layout.php', [
    'content' => $page_content,
    'categories' => $categories,
    //'errors' => $errors,
    //'form_item_error_class' => $form_item_error_class,
    'title' => 'Регистрация'
]);
print($layout_content);
