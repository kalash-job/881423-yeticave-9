<?php
declare(strict_types=1);
require_once 'init.php';

$is_auth = rand(0, 1);

$user_name = 'Николай'; // укажите здесь ваше имя


$categories = get_categores($link);
$items = get_items($link);

/*Сборка шаблона Главной страницы*/
$page_content = include_template('index.php', ['items' => $items, 'categories' => $categories]);
$layout_content = include_template('layout.php', [
    'content' => $page_content,
    'categories' => $categories,
    'title' => 'Главная'
]);
print($layout_content);