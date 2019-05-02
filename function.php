<?php
declare(strict_types=1);
/* Функция проверяет наличие запрашиваемого id в таблице lot
- принимает ресурс соединения и ID лота
- вызывает переход на страницу ошибки 404 или просто завершает работу*/
function check_id($link, $lot_id)
{
    $sql = 'SELECT COUNT(l.id) AS count_of_lines
FROM lot l
WHERE l.id = ' . '?';
    $stmt = db_get_prepare_stmt($link, $sql, [$lot_id]);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    if ($result !== false) {
        $num_of_lines_with_id = mysqli_fetch_all($result, MYSQLI_ASSOC);
        foreach ($num_of_lines_with_id as $value) {
            if ($value["count_of_lines"] === 0) {
                header("Location: /404.php");
            }
            return;
        }
    }
    $error = mysqli_error($link);
    $content = include_template('error.php', ['error' => $error]);
    print($content);
    die();
}

function format_cost(float $cost): string
{
    $result = "";
    $cost = ceil($cost);
    if ($cost >= 1000) {
        $result = number_format($cost, 0, "", " ");
    } else {
        $result = $cost;
    }
    return $result . " <b class=\"rub\">р</b>";
}

/* Функция для получения остатка времени до завершения лота
- принимает оставшееся время в секундах
- возвращает строку формата ЧЧ:ММ*/
function time_to_closing_date(int $timestamp_to_closing_date): string
{
    $minut = intdiv($timestamp_to_closing_date, 60) % 60;
    if ($minut < 10) {
        $minut = "0" . (string)$minut;
    }
    $hour = intdiv($timestamp_to_closing_date, 3600);
    if ($hour < 10) {
        $hour = "0" . (string)$hour;
    }
    return $hour . ":" . $minut;
}

function second_to_closing_date(int $timestamp_of_end): int
{
    $timestamp_to_closing_date = $timestamp_of_end - strtotime("now");
    return ($timestamp_to_closing_date);
}

/* Функция для получения остатка времени до завершения лота
- принимает оставшееся время в секундах
- возвращает в случае, если до завершения лота осталось меньше часа, дополнительный класс для окраски времени в красный цвет*/
function color_hour_to_closing_date(int $timestamp_to_closing_date): string
{
    if ($timestamp_to_closing_date <= 3600) {
        $result = " timer--finishing";
        return ($result);
    }
    return "";
}

/* Функция для получения $categories
- принимает ресурс соединения
- возвращает массив с категориями или страницу ошибки*/
function get_categories($link): array
{
    $sql = "SELECT id, name, css_class FROM category";
    $stmt = db_get_prepare_stmt($link, $sql);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    if ($result !== false) {
        return mysqli_fetch_all($result, MYSQLI_ASSOC);
    }
    $error = mysqli_error($link);
    $content = include_template('error.php', ['error' => $error]);
    print($content);
    die();
}

/* Функция для получения $items
- принимает ресурс соединения
- возвращает массив с лотами или страницу ошибки*/
function get_items($link): array
{
    $sql = 'SELECT l.name,
       l.price,
       l.url,
       c.name                                                                          AS category,
       l.creation_date,
       l.id,
       UNIX_TIMESTAMP(l.completion_date) - UNIX_TIMESTAMP(now()) AS timestamp_to_clos_date
FROM lot l
         LEFT JOIN category c
                   ON l.category_id = c.id
WHERE l.completion_date > now()
ORDER BY l.creation_date DESC
LIMIT 9';
    $stmt = db_get_prepare_stmt($link, $sql);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    if ($result !== false) {
        return mysqli_fetch_all($result, MYSQLI_ASSOC);
    }
    $error = mysqli_error($link);
    $content = include_template('error.php', ['error' => $error]);
    print($content);
    die();
}

/* Функция для получения массива $current_lot
- принимает ресурс соединения и ID лота
- возвращает массив с данными по лоту или страницу ошибки*/
function get_current_lot($link, int $lot_id): array
{
    $sql = 'SELECT l.name,
       l.price,
       l.url,
       c.name                                                                          AS category,
       l.creation_date,
       l.id,
       l.description,
       UNIX_TIMESTAMP(l.completion_date) - UNIX_TIMESTAMP(now()) AS timestamp_to_clos_date,
       MAX(GREATEST(COALESCE(l.price, b.bid_amount), COALESCE(b.bid_amount, l.price))) AS current_price,
       MAX(GREATEST(COALESCE(l.price, b.bid_amount), COALESCE(b.bid_amount, l.price))) + l.bid_step AS min_bid
FROM lot l
         LEFT JOIN category c
                   ON l.category_id = c.id
LEFT JOIN bid b
                   ON l.id = b.lot_id
WHERE l.id = ' . '?' .
        ' GROUP BY l.id, l.name, l.url, l.price, l.creation_date, c.name, l.completion_date, l.bid_step, l.description';

    $stmt = db_get_prepare_stmt($link, $sql, [$lot_id]);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    if ($result !== false) {
        return mysqli_fetch_all($result, MYSQLI_ASSOC);
    }
    $error = mysqli_error($link);
    $content = include_template('error . php', ['error' => $error]);
    print($content);
    die();
}

/* Функция для получения названия лота по id
- принимает ресурс соединения и ID лота
- возвращает название лота или страницу ошибки*/
function get_lots_name_from_id($link, int $lot_id)
{
    $sql = 'SELECT l.name
FROM lot l
WHERE l.id = ' . '?';

    $stmt = db_get_prepare_stmt($link, $sql, [$lot_id]);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    if ($result !== false) {
        $lots_name_array = mysqli_fetch_all($result, MYSQLI_ASSOC);
        foreach ($lots_name_array as $value) {
            return $value["name"];
        }
    }
    $error = mysqli_error($link);
    $content = include_template('error . php', ['error' => $error]);
    print($content);
    die();
}
