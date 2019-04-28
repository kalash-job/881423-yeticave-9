<?php
declare(strict_types=1);
require_once 'init.php';
require_once 'data.php';
$is_auth = rand(0, 1);

$user_name = 'Николай'; // укажите здесь ваше имя
/* Для вывода списка категорий*/
if (!$link) {
    $error = mysqli_connect_error();
    $content = include_template('error.php', ['error' => $error]);
} else {
    $sql = 'SELECT id, name, css_class FROM category';
    $result = mysqli_query($link, $sql);

    if ($result) {
        $categories = mysqli_fetch_all($result, MYSQLI_ASSOC);
    } else {
        $error = mysqli_error($link);
        $content = include_template('error.php', ['error' => $error]);
    }
}

/* Для получения items*/
if (!$link) {
    $error = mysqli_connect_error();
    $content = include_template('error.php', ['error' => $error]);
} else {
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
    $result = mysqli_query($link, $sql);

    if ($result) {
        $items = mysqli_fetch_all($result, MYSQLI_ASSOC);
    } else {
        $error = mysqli_error($link);
        $content = include_template('error.php', ['error' => $error]);
    }
}

$page_content = include_template('index.php', ['items' => $items, 'categories' => $categories]);
$layout_content = include_template('layout.php', [
    'content' => $page_content,
    'categories' => $categories,
    'title' => 'Главная'
]);
print($layout_content);