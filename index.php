<?php
declare(strict_types=1);
require_once 'init.php';
require_once 'getwinner.php';

$categories = get_categories($link);
$items = get_items($link);

/*Сборка шаблона Главной страницы*/

$page_content = include_template('index.php',
    ['items' => $items, 'categories' => $categories, 'user_session' => $user_session]);
$layout_content = include_template('layout.php', [
    'top_menu' => null,
    'content' => $page_content,
    'categories' => $categories,
    'user_session' => $user_session,
    'pagination' => '',
    'title' => 'Главная'
]);
print($layout_content);