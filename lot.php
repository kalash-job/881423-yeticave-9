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
    if (($user_session['is_auth'] === 1) && (check_last_bid_user($link, $_SESSION['user'],
                (int)$_GET['id'])) && $_SESSION['user'] !== $current_lot['user_id'] && $current_lot['timestamp_to_clos_date'] > 0) {
        $new_bid_adding['show_block'] = true;
    } else {
        $new_bid_adding['show_block'] = false; // ограничит показ блока добавления ставок в данном лоте для данного пользователя с открытой сессией
    }
    /*Сборка шаблона страницы лота*/
    $top_menu = include_template('top-menu.php',
        ['categories' => $categories]);
    $page_content = include_template('lot.php',
        [
            'current_lot' => $current_lot,
            'categories' => $categories,
            'user_session' => $user_session,
            'new_bid_adding' => $new_bid_adding
        ]);
    $layout_content = include_template('layout.php', [
        'top_menu' => $top_menu,
        'content' => $page_content,
        'categories' => $categories,
        'user_session' => $user_session,
        'new_bid_adding' => $new_bid_adding,
        'title' => $current_lot['name']
    ]);
    print($layout_content);
}
