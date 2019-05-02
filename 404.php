<?php
declare(strict_types=1);
require_once 'init.php';

$is_auth = rand(0, 1);

$user_name = 'Николай'; // укажите здесь ваше имя


$categories = get_categories($link);

/*Сборка шаблона страницы ошибки 404*/
$page_content = include_template('404.php', ['categories' => $categories]);
$layout_content = include_template('layout.php', [
    'content' => $page_content,
    'categories' => $categories,
    'title' => '404 Страница не найдена'
]);
print($layout_content);