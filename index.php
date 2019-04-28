<?php
declare(strict_types=1);
require_once 'init.php';
require_once 'data.php';
$is_auth = rand(0, 1);

$user_name = 'Николай'; // укажите здесь ваше имя

/* Для получения $categories*/
$sql = "SELECT id, name, css_class FROM category";
$stmt = db_get_prepare_stmt($link, $sql);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
if ($result) {
    $categories = mysqli_fetch_all($result, MYSQLI_ASSOC);
} else {
    $error = mysqli_error($link);
    $content = include_template('error.php', ['error' => $error]);
}

/* Для получения $items*/
$sql = 'SELECT l.name,
       l.price,
       l.url,
       c.name                                                                          AS category,
       l.id,
       l.creation_date,
       MAX(GREATEST(COALESCE(l.price, b.bid_amount), COALESCE(b.bid_amount, l.price))) AS current_price
FROM lot l
         LEFT JOIN category c
                   ON l.category_id = c.id
         LEFT JOIN bid b
                   ON l.id = b.lot_id
WHERE l.completion_date > now()
GROUP BY l.id, l.name, l.url, l.price, l.creation_date, c.name
ORDER BY l.creation_date DESC
LIMIT 9';
$stmt = db_get_prepare_stmt($link, $sql);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
if ($result) {
    $items = mysqli_fetch_all($result, MYSQLI_ASSOC);
} else {
    $error = mysqli_error($link);
    $content = include_template('error.php', ['error' => $error]);
}

/*Сборка шаблона Главной страницы*/
$page_content = include_template('index.php', ['items' => $items, 'categories' => $categories]);
$layout_content = include_template('layout.php', [
    'content' => $page_content,
    'categories' => $categories,
    'title' => 'Главная'
]);
print($layout_content);