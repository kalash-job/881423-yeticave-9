<?php
declare(strict_types=1);
require_once 'init.php';

$categories = get_categories($link);
$search = $_GET['search'] ?? '';
if (trim($search) !== '') {
    $current_page = $_GET['page'] ?? 1;
    $current_page = (int)$current_page;
    $page_items = 9; //Количество лотов на одной странице
    $search_result_num = get_search_num($link, trim($search)); //к-во лотов, которые показываем в результатах поиска
    $search_result_num = $search_result_num[0]['result_num'] ?? null;
    $pages_count = (int)ceil($search_result_num / $page_items); //количество страниц для показа всех рез-в поиска
    $offset = ($current_page - 1) * $page_items;
    $pages = range(1, $pages_count);
    $items = get_search_result($link, trim($search), $page_items, $offset);
    $part_of_path = "search.php?search=" . $search . "&";
} else {
    $pages_count = null;
    $pages = null;
    $current_page = null;
    $items = null;
    $part_of_path = null;
}

/*Сборка шаблона страницы результатов поиска*/
$pagination = include_template('pagination.php',
    [
        'pages_count' => $pages_count,
        'current_page' => $current_page,
        'pages' => $pages,
        'part_of_path' => $part_of_path
    ]);
$top_menu = include_template('top-menu.php',
    ['categories' => $categories]);
$page_content = include_template('search.php',
    [
        'items' => $items,
        'categories' => $categories,
        'user_session' => $user_session,
        'search' => $search
    ]);
$layout_content = include_template('layout.php', [
    'top_menu' => $top_menu,
    'pagination' => $pagination,
    'content' => $page_content,
    'categories' => $categories,
    'user_session' => $user_session,
    'search' => $search,
    'pages_count' => $pages_count,
    'pages' => $pages,
    'current_page' => $current_page,
    'title' => 'Результаты поиска'
]);
print($layout_content);