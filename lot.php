<?php
declare(strict_types=1);
require_once 'init.php';

$is_auth = rand(0, 1);

$user_name = 'Николай'; // укажите здесь ваше имя

$categories = get_categories($link);
if (isset($_GET['id'])) {
    $lot_id = (int)$_GET['id'];
} else {
    header("Location: /404.php");
}
check_id($link, $lot_id);

$lots_name = get_lots_name_from_id($link, $lot_id);
$current_lot = get_current_lot($link, $lot_id);
/*Сборка шаблона страницы лота*/
$page_content = include_template('lot.php', ['current_lot' => $current_lot, 'categories' => $categories]);
$layout_content = include_template('layout.php', [
    'content' => $page_content,
    'categories' => $categories,
    'title' => $lots_name
]);
print($layout_content);