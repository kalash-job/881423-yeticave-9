<?php
declare(strict_types=1);
require_once 'init.php';

$categories = get_categories($link);


/*Сборка шаблона страницы входа зарегистрированного пользователя*/
$page_content = include_template('login.php',
    ['categories' => $categories]);
$layout_content = include_template('layout.php', [
    'content' => $page_content,
    'categories' => $categories,
    'title' => 'Вход'
]);
print($layout_content);
