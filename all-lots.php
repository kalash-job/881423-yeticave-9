<?php
declare(strict_types=1);
require_once 'init.php';

$categories = get_categories($link);

if (isset($_GET['category'])) {
    $category_id = (int)$_GET['category'];
    $category_name = get_category_name($link, $category_id);
    if ($category_name === null) {
        http_response_code(404);
        /*Сборка шаблона страницы ошибки 404*/
        $top_menu = include_template('top-menu.php',
            ['categories' => $categories]);
        $page_content = include_template('404.php', ['categories' => $categories, 'user_session' => $user_session]);
        $layout_content = include_template('layout.php', [
            'top_menu' => $top_menu,
            'content' => $page_content,
            'categories' => $categories,
            'user_session' => $user_session,
            'pagination' => '',
            'title' => '404 Страница не найдена'
        ]);
        print($layout_content);
        exit();
    }
} else {
    http_response_code(404);
    /*Сборка шаблона страницы ошибки 404*/
    $top_menu = include_template('top-menu.php',
        ['categories' => $categories]);
    $page_content = include_template('404.php', ['categories' => $categories, 'user_session' => $user_session]);
    $layout_content = include_template('layout.php', [
        'top_menu' => $top_menu,
        'content' => $page_content,
        'categories' => $categories,
        'user_session' => $user_session,
        'pagination' => '',
        'title' => '404 Страница не найдена'
    ]);
    print($layout_content);
    exit();
}

$items_num = get_lots_count($link, $category_id); //к-во лотов, которые показываем в категории
$items_num = $items_num[0]['result_num'] ?? null;
$page_items = 9; //Количество лотов на одной странице

$current_page = $_GET['page'] ?? 1;
$current_page = (int)$current_page;
$pages_count = (int)ceil($items_num / $page_items); //количество страниц для показа всех лотов категории
if ($current_page > $pages_count) {
    $current_page = $pages_count;
} elseif ($current_page === 0) {
    $current_page = 1;
}
$offset = ($current_page - 1) * $page_items;
$pages = range(1, $pages_count);
$items = get_lots_by_category($link, $category_id, $page_items, $offset);
$part_of_path = "all-lots.php?category=" . $category_id . "&";

/*Сборка шаблона страницы лотов по категориям*/
$pagination = include_template('pagination.php',
    [
        'pages_count' => $pages_count,
        'current_page' => $current_page,
        'pages' => $pages,
        'part_of_path' => $part_of_path
    ]);
$top_menu = include_template('top-menu.php',
    ['categories' => $categories]);
$page_content = include_template('all-lots.php',
    [
        'items' => $items,
        'categories' => $categories,
        'category_name' => $category_name,
        'user_session' => $user_session
    ]);
$layout_content = include_template('layout.php', [
    'top_menu' => $top_menu,
    'pagination' => $pagination,
    'content' => $page_content,
    'categories' => $categories,
    'user_session' => $user_session,
    'pages_count' => $pages_count,
    'pages' => $pages,
    'current_page' => $current_page,
    'title' => 'Все лоты'
]);
print($layout_content);
