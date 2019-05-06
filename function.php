<?php
declare(strict_types=1);

/** Функция форматирует стоимость лота
 * Добавляет пробел для отделения трех последних цифр, если число больше/равно 1000
 * Добавляет значок рубля в конце стоимости лота.
 * @param float $cost
 * @return string
 */
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

/** Функция для получения остатка времени до завершения лота
 * Принимает оставшееся время в секундах.
 * Возвращает строку формата ЧЧ:ММ.
 * @param int $timestamp_to_closing_date
 * @return string
 */
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

/** Функция для добавления класса при условии, что осталось менее часа до завершения лота
 * Принимает оставшееся время в секундах.
 * Возвращает дополнительный класс для окраски времени в красный цвет, в случае, если до завершения лота осталось меньше часа
 * @param int $timestamp_to_closing_date
 * @return string
 */
function color_hour_to_closing_date(int $timestamp_to_closing_date): string
{
    if ($timestamp_to_closing_date <= 3600) {
        $result = " timer--finishing";
        return ($result);
    }
    return "";
}

/** Функция для получения $categories.
 * Принимает ресурс соединения.
 * Возвращает массив с категориями или страницу ошибки.
 * @param $link
 * @return array
 */
function get_categories($link): array
{
    $sql = "SELECT id, name, css_class FROM category";
    $stmt = db_get_prepare_stmt($link, $sql);
    return select($stmt);
}

/** Функция для получения $items.
 * Принимает ресурс соединения.
 * Возвращает массив с лотами для вывода на главную страницу, или страницу ошибки.
 * @param $link
 * @return array
 */
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
    return select($stmt);
}

/** Функция для получения массива $current_lot.
 * Принимает ресурс соединения и ID лота.
 * Возвращает массив с данными по лоту для вывода на страницу лота, или страницу ошибки.
 * @param $link
 * @param int $lot_id
 * @return array|null
 */
function get_current_lot($link, int $lot_id): ?array
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
    return select($stmt);
}

/** Функция для работы с подготовленным выражением с параметрами при SELECT-запросах
 * Получает подготовленное выражение с параметрами, исполняет его и фетчит результат, и возвращает его, либо умирает с показом ошибки.
 * @param mysqli_stmt $stmt
 * @return array|null
 */
function select(mysqli_stmt $stmt): ?array
{
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

/** Функция получения id нового лота в БД.
 * Получает ресурс соединения, массив $new_lot с данными по лоту, и id пользователя.
 * Приводит элемены массива $new_lot, отвечающие за дату завершения лота и путь к картинке лота, к нужному формату.
 * Проверяет успешность добавления лота в БД. Уточняет и возвращает id нового лота.
 * Если ничего не добавилось, функция показывает ошибку и умирает.
 * @param $link
 * @param array $new_lot
 * @param int $user_id
 * @return int|null
 */
function get_new_lot_id($link, array $new_lot, int $user_id): ?int
{
    $new_lot['lot_date'] = $new_lot['lot_date'] . " 00:00:00";
    $new_lot['path'] = "uploads/" . $new_lot['path'];
    $sql = 'INSERT INTO lot
(name, description, url, price, completion_date, bid_step, category_id, user_id)
VALUES (?, ?, ?, ?, ?, ?, ?, ?)';
    $stmt = db_get_prepare_stmt($link, $sql, [
        $new_lot['lot_name'],
        $new_lot['message'],
        $new_lot['path'],
        $new_lot['lot_rate'],
        $new_lot['lot_date'],
        $new_lot['lot_step'],
        $new_lot['category'],
        $user_id
    ]);
    $new_id = insert($stmt);
    if ($new_id !== null) {
        /*возвращаем id добавленной записи*/
        return $new_id;
    }
    $error = mysqli_error($link);
    $content = include_template('error.php', ['error' => $error]);
    print($content);
    die();
}

/** Функция для работы с подготовленным выражением с параметрами при INSERT-запросах
 * Получает подготовленное выражение с параметрами, исполняет его и фетчит результат, и возвращает его, либо умирает с показом ошибки.
 * @param mysqli_stmt $stmt
 * @return int|null
 */
function insert(mysqli_stmt $stmt): ?int
{
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_affected_rows($stmt);
    if ($result !== 0) {
        /*возвращаем id добавленной записи*/
        return mysqli_stmt_insert_id($stmt);
    }
    $error = mysqli_error($link);
    $content = include_template('error.php', ['error' => $error]);
    print($content);
    die();
}
