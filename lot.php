<?php
declare(strict_types=1);
require_once 'init.php';
/*Попробовать сначала обработать данные ПОСТ-запроса, а затем редирект на ту же страницу, чтобы не потерять данные ГЕТ-запроса*/
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
}
/*Проверка условий показа блока добавления ставок*/
if (($user_session['is_auth'] === 1) && (check_last_bid_user($link, $_SESSION['user'],
        (int)$_GET['id'])) && $_SESSION['user'] !== $current_lot['user_id'] && $current_lot['timestamp_to_clos_date'] > 0) {
    $new_bid_adding['show_block'] = true;
} else {
    $new_bid_adding['show_block'] = false; // ограничит показ блока добавления ставок в данном лоте для данного пользователя с открытой сессией
}
$new_bid_adding['error_note'] = '';
$new_bid_adding['form_error_class'] = '';
/*проверка на отправленность формы*/
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $new_bid = $_POST;
    $new_bid['cost'] = (int)$new_bid['cost'];
    /*Проверка залогиненности пользователя*/
    if (($user_session['is_auth'] === 0)) {
        header("Location: /login.php");
    }
    /*Проверяем обязательное поле*/
    if (empty($new_bid['cost'])) {
        $new_bid_adding['error_note'] = 'Введите размер ставки';
        $new_bid_adding['form_error_class'] = ' form__item--invalid';
        /*Проверка типа значения и размера введенной ставки*/
    } elseif (is_int($new_bid['cost']) && $new_bid['cost'] >= $current_lot['min_bid']) {
        /*Добавляем ставку в таблицу ставок с привязкой к лоту и пользователю*/
        $new_bid['user_id'] = $_SESSION['user'];
        $new_bid['lot_id'] = (int)$_GET['id'];
        add_new_bid($link, $new_bid);
        header("Location: /lot.php?id=" . $new_bid['lot_id']);
    } else {
        $new_bid_adding['error_note'] = 'Введите целое число, размером не меньше минимальной ставки';
        $new_bid_adding['form_error_class'] = ' form__item--invalid';
    }
}

$bids_by_lot = get_list_of_lots_bids($link, (int)$_GET['id']);
/*Сборка шаблона страницы лота*/
$top_menu = include_template('top-menu.php',
    ['categories' => $categories]);
$page_content = include_template('lot.php',
    [
        'current_lot' => $current_lot,
        'categories' => $categories,
        'user_session' => $user_session,
        'bids_by_lot' => $bids_by_lot,
        'new_bid_adding' => $new_bid_adding
    ]);
$layout_content = include_template('layout.php', [
    'top_menu' => $top_menu,
    'content' => $page_content,
    'categories' => $categories,
    'user_session' => $user_session,
    'new_bid_adding' => $new_bid_adding,
    'bids_by_lot' => $bids_by_lot,
    'pagination' => '',
    'title' => $current_lot['name']
]);
print($layout_content);

