<?php
declare(strict_types=1);
http_response_code(404);
require_once 'init.php';

$categories = get_categories($link);

/*Сборка шаблона страницы ошибки 404*/
$page_content = include_template('404.php', ['categories' => $categories]);
$layout_content = include_template('layout.php', [
    'content' => $page_content,
    'categories' => $categories,
    'title' => '404 Страница не найдена'
]);
print($layout_content);