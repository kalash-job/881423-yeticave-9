<?php
declare(strict_types=1);
require_once 'init.php';

$categories = get_categories($link);


/*Сборка шаблона страницы входа зарегистрированного пользователя*/
$top_menu = include_template('top-menu.php',
    ['categories' => $categories]);
$page_content = include_template('login.php',
    ['categories' => $categories]);
$layout_content = include_template('layout.php', [
    'top_menu' => $top_menu,
    'content' => $page_content,
    'categories' => $categories,
    'title' => 'Вход'
]);
print($layout_content);
