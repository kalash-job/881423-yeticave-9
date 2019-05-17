<?php
declare(strict_types=1);
require_once 'init.php';

if (!isset($_SESSION['user'])) {
    header("Location: /403.php");
    exit();
}
$users_bids = get_list_of_users_bids($link, $_SESSION['user']);
$categories = get_categories($link);
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