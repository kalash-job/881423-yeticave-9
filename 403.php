<?php
declare(strict_types=1);
http_response_code(403);
require_once 'init.php';

$categories = get_categories($link);

/*Сборка шаблона страницы ошибки 403*/
$top_menu = include_template('top-menu.php',
    ['categories' => $categories]);
$page_content = include_template('403.php', ['categories' => $categories, 'user_session' => $user_session]);
$layout_content = include_template('layout.php', [
    'top_menu' => $top_menu,
    'content' => $page_content,
    'categories' => $categories,
    'user_session' => $user_session,
    'title' => '403 Доступ к странице запрещен'
]);
print($layout_content);