<?php
declare(strict_types=1);
require_once 'init.php';
require_once 'data.php';
$is_auth = rand(0, 1);

$user_name = 'Николай'; // укажите здесь ваше имя

$page_content = include_template('index.php', ['items' => $items, 'categories' => $categories]);
$layout_content = include_template('layout.php', [
    'content' => $page_content,
    'categories' => $categories,
    'title' => 'Главная'
]);
print($layout_content);