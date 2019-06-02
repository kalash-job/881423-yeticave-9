<?php
declare(strict_types=1);
require_once 'init.php';

$categories = get_categories($link);
if (!isset($_SESSION['user'])) {
    http_response_code(403);
    /*Сборка шаблона страницы ошибки 403*/
    $top_menu = include_template('top-menu.php',
        ['categories' => $categories]);
    $page_content = include_template('403.php', ['categories' => $categories, 'user_session' => $user_session]);
    $layout_content = include_template('layout.php', [
        'top_menu' => $top_menu,
        'content' => $page_content,
        'categories' => $categories,
        'user_session' => $user_session,
        'pagination' => '',
        'title' => '403 Доступ к странице запрещен'
    ]);
    print($layout_content);
    exit();
}

$users_bids = get_list_of_users_bids($link, $_SESSION['user']);
/*Сборка шаблона страницы мои ставки*/
$top_menu = include_template('top-menu.php',
    ['categories' => $categories]);
$page_content = include_template('my-bets.php',
    [
        'categories' => $categories,
        'users_bids' => $users_bids,
        'user_session' => $user_session
    ]);
$layout_content = include_template('layout.php', [
    'top_menu' => $top_menu,
    'content' => $page_content,
    'categories' => $categories,
    'users_bids' => $users_bids,
    'user_session' => $user_session,
    'pagination' => '',
    'title' => 'Мои ставки'
]);
print($layout_content);