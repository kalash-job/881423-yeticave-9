<?php
declare(strict_types=1);
require_once 'init.php';

$categories = get_categories($link);
if (isset($_GET['id'])) {
    $lot_id = (int)$_GET['id'];
} else {
    header("Location: /404.php");
}
$current_lot = get_current_lot($link, $lot_id);
$current_lot = $current_lot[0] ?? null;
if ($current_lot == null) {
    header("Location: /404.php");
} else {
    /*Проверка условий показа блока добавления ставок*/
    if (($user_session['is_auth'] === 1) && (show_adding_new_bid($link, $_SESSION['user'],
                (int)$_GET['id']) === true)) {
        $user_session['new_bid'] = true;
    } else {
        $user_session['new_bid'] = false; // ограничит показ блока добавления ставок в данном лоте для данного пользователя с открытой сессией
    }
    /*Сборка шаблона страницы лота*/
    $top_menu = include_template('top-menu.php',
        ['categories' => $categories]);
    $page_content = include_template('lot.php',
        ['current_lot' => $current_lot, 'categories' => $categories, 'user_session' => $user_session]);
    $layout_content = include_template('layout.php', [
        'top_menu' => $top_menu,
        'content' => $page_content,
        'categories' => $categories,
        'user_session' => $user_session,
        'title' => $current_lot['name']
    ]);
    print($layout_content);
}
