<?php
declare(strict_types=1);

/** Функция форматирует стоимость лота
 * Добавляет пробел для отделения трех последних цифр, если число больше/равно 1000
 * Добавляет значок рубля в конце стоимости лота.
 * @param int $cost
 * @return string
 */
function format_cost(int $cost): string
{
    $result = "";
    if ($cost >= 1000) {
        $result = number_format($cost, 0, "", " ");
    } else {
        $result = $cost;
    }
    return $result . " <b class=\"rub\">р</b>";
}

/** Функция форматирует стоимость лота для таблицы ставок пользователя
 * Добавляет пробел для отделения трех последних цифр, если число больше/равно 1000
 * Добавляет сокращение р в конце стоимости лота.
 * @param int $cost
 * @return string
 */
function format_cost_for_bids(int $cost): string
{
    $result = "";
    if ($cost >= 1000) {
        $result = number_format($cost, 0, "", " ");
    } else {
        $result = $cost;
    }
    return $result . " р";
}

/** Функция форматирует стоимость лота для блока ставок на странице лота
 * Добавляет пробел для отделения трех последних цифр, если число больше/равно 1000
 * @param int $cost
 * @return string
 */
function format_cost_for_bids_block(int $cost): string
{
    $result = "";
    if ($cost >= 1000) {
        $result = number_format($cost, 0, "", " ");
    } else {
        $result = $cost;
    }
    return (string)$result;
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
 * @param mysqli $link
 * @return array
 */
function get_categories(mysqli $link): array
{
    $sql = "SELECT id, name, css_class FROM category";
    $stmt = db_get_prepare_stmt($link, $sql);
    return select($stmt, $link);
}

/** Функция для получения $items.
 * Принимает ресурс соединения.
 * Возвращает массив с лотами для вывода на главную страницу, или страницу ошибки.
 * @param mysqli $link
 * @return array
 */
function get_items(mysqli $link): array
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
    return select($stmt, $link);
}

/** Функция для получения массива $current_lot.
 * Принимает ресурс соединения и ID лота.
 * Возвращает массив с данными по лоту для вывода на страницу лота, или страницу ошибки.
 * @param mysqli $link
 * @param int $lot_id
 * @return array|null
 */
function get_current_lot(mysqli $link, int $lot_id): ?array
{
    $sql = 'SELECT l.name,
       l.price,
       l.url,
       c.name                                                                          AS category,
       l.creation_date,
       l.id,
       l.description,
       l.user_id,
       UNIX_TIMESTAMP(l.completion_date) - UNIX_TIMESTAMP(now()) AS timestamp_to_clos_date,
       MAX(GREATEST(COALESCE(l.price, b.bid_amount), COALESCE(b.bid_amount, l.price))) AS current_price,
       MAX(GREATEST(COALESCE(l.price, b.bid_amount), COALESCE(b.bid_amount, l.price))) + l.bid_step AS min_bid
FROM lot l
         LEFT JOIN category c
                   ON l.category_id = c.id
LEFT JOIN bid b
                   ON l.id = b.lot_id
WHERE l.id = ?
GROUP BY l.id, l.name, l.url, l.price, l.creation_date, c.name, l.completion_date, l.bid_step, l.description, l.user_id';

    $stmt = db_get_prepare_stmt($link, $sql, [$lot_id]);
    return select($stmt, $link);
}

/** Функция для работы с подготовленным выражением с параметрами при SELECT-запросах
 * Получает подготовленное выражение с параметрами, исполняет его и фетчит результат, и возвращает его, либо умирает с показом ошибки.
 * @param mysqli_stmt $stmt
 * @return array|null
 */
function select(mysqli_stmt $stmt, mysqli $link): ?array
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
 * Приводит элементы массива $new_lot, отвечающие за дату завершения лота и путь к картинке лота, к нужному формату.
 * Проверяет успешность добавления лота в БД. Уточняет и возвращает id нового лота.
 * Если ничего не добавилось, функция показывает ошибку и умирает.
 * @param mysqli $link
 * @param array $new_lot
 * @param int $user_id
 * @return int|null
 */
function get_new_lot_id(mysqli $link, array $new_lot, int $user_id): ?int
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
    $new_id = insert($stmt, $link);
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
 * Получает подготовленное выражение с параметрами, исполняет его и фетчит результат, и возвращает id добавленного лота/пользователя, либо умирает с показом ошибки.
 * @param mysqli_stmt $stmt
 * @return int|null
 */
function insert(mysqli_stmt $stmt, mysqli $link): ?int
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

/**Функция проверки уникальности email
 * Получает ресурс соединения и введенный при регистрации email.
 * При наличии такого email у ранее зарегистрированных пользователей, функция возвращает false.
 * При уникальности email функция возвращает true.
 * @param mysqli $link
 * @param string $email
 * @return bool|null
 */
function check_unique_email(mysqli $link, string $email): ?bool
{
    $sql = 'SELECT u.id
FROM user u
WHERE u.email = ?';
    $stmt = db_get_prepare_stmt($link, $sql, [$email]);
    $current_user_id = select($stmt, $link);
    if (!isset($current_user_id[0])) {
        return true;
    } else {
        return false;
    }
}

/** Функция добавления нового пользователя
 * Получает ресурс соединения и массив с параметрами по добавляемому пользователю.
 * Приводит элементы массива $new_user, отвечающие за путь к картинке аватара, к нужному формату.
 * Выполняет запрос по добавлению пользователя в БД.
 * @param mysqli $link
 * @param array $new_user
 */
function add_new_user(mysqli $link, array $new_user)
{
    if (isset($new_user['path'])) {
        $new_user['path'] = "uploads/" . $new_user['path'];
    } else {
        $new_user['path'] = null;
    }

    $sql = 'INSERT INTO user
(email, password, avatar_path, name, contact)
VALUES (?, ?, ?, ?, ?)';
    $stmt = db_get_prepare_stmt($link, $sql, [
        $new_user['email'],
        $new_user['password'],
        $new_user['path'],
        $new_user['name'],
        $new_user['message']
    ]);
    insert($stmt, $link);
    return;
}

/** Функция проверяет наличие email пользователя и возвращает массив с данными пользователя или null
 * @param mysqli $link
 * @param string $email
 * @return array|null
 */
function get_user_data(mysqli $link, string $email): ?array
{
    $sql = 'SELECT u.id,
       u.password,
       u.email
FROM user u
WHERE u.email = ?';
    $stmt = db_get_prepare_stmt($link, $sql, [$email]);
    return select($stmt, $link);
}

/** Функция возвращает имя пользователя по его id
 * @param $link
 * @param int $user_id
 * @return array
 */
function get_username(mysqli $link, int $user_id): ?array
{
    $sql = 'SELECT name FROM user
WHERE id = ?';
    $stmt = db_get_prepare_stmt($link, $sql, [$user_id]);
    $user_name = select($stmt, $link);
    $user_name = $user_name[0] ?? null;
    return $user_name;
}

/** Функция вычисляет, сделал ли пользователь последнюю ставку по текущему лоту
 * Получает ресурс соединения, id пользователя, id лота.
 * На основании сопоставления с условиями показа возвращает true (показываем) или false (не показываем).
 * @param mysqli $link
 * @param int $user_id
 * @param int $lot_id
 * @return bool|null
 */

function check_last_bid_user(mysqli $link, int $user_id, int $lot_id): bool
{
    //Определяем пользователя, сделавшего максимальную ставку.
    $sql = 'SELECT b.lot_id,
       b.bid_amount,
       b.user_id
FROM bid b
WHERE b.lot_id = ?
ORDER BY b.bid_amount DESC LIMIT 1';
    $stmt = db_get_prepare_stmt($link, $sql, [$lot_id]);
    $max_bid_user = select($stmt, $link);
    $max_bid_user = $max_bid_user[0] ?? null;
    if ((int)$max_bid_user['user_id'] === $user_id) {
        return false;
    }
    return true;
}

function add_new_bid(mysqli $link, array $new_bid)
{
    $sql = 'INSERT INTO bid
(bid_amount, user_id, lot_id)
VALUES (?, ?, ?)';
    $stmt = db_get_prepare_stmt($link, $sql, [
        $new_bid['cost'],
        $new_bid['user_id'],
        $new_bid['lot_id']
    ]);
    insert($stmt, $link);
    return;
}

/** Функция формирует запросы к БД для получения данных для формирования таблицы ставок пользователя.
 * Принимает ресурс соединения и id пользователя.
 * Возвращает массив с данными по ставкам, либо null, если ставок не было.
 * @param mysqli $link
 * @param int $user_id
 * @return array|null
 */
function get_list_of_users_bids(mysqli $link, int $user_id): ?array
{
    $sql = 'SELECT l.name,
       l.url,
       c.name                                                                          AS category,
       UNIX_TIMESTAMP(l.completion_date) - UNIX_TIMESTAMP(now()) AS timestamp_to_clos_date,
       l.winner_id,
       b.user_id,
       b.bid_amount,
       b.lot_id,
       b.date,
       UNIX_TIMESTAMP(b.date)  AS timestamp_bid,
       TIME_FORMAT(b.date, "%H:%i") AS time_of_bid,
       DATE_FORMAT(b.date, "%d.%m.%y в %H:%i") AS date_time_of_bid,
       TIMEDIFF (l.completion_date, now()) AS time_to_clos_date,
       u.contact
FROM bid b
LEFT JOIN lot l
                   ON b.lot_id = l.id
LEFT JOIN category c
                   ON l.category_id = c.id
LEFT JOIN user u
                   ON l.winner_id = u.id
WHERE b.user_id = ?
ORDER BY b.date DESC';
    $stmt = db_get_prepare_stmt($link, $sql, [$user_id]);
    return select($stmt, $link);
}

/** Функция возвращает различные форматы записи времени и даты для столбца "Время ставки"
 * @param array $users_bids_option
 * @return string
 */
function get_format_time_of_bid(array $users_bids_option): string
{
    $hours_ago = (int)((time() - ((int)$users_bids_option["timestamp_bid"])) / 3600); //округленное до целого количество часов, прошедшее с момента ставки
    $minuts_ago = (int)((time() - (int)$users_bids_option["timestamp_bid"]) / 60) - $hours_ago * 60; //округленное до целого количество минут, прошедшше с момента ставки (за вычетом часов)
    /*для ставок, сделанных менее часа назад*/
    if ((int)$users_bids_option["timestamp_bid"] > strtotime("1 hour ago")) {
        $date_time_of_bid = $minuts_ago . get_noun_plural_form($minuts_ago,
                ' минута', ' минуты', ' минут') . ' назад';
        /* для ставок, сделанных более 1 часа, но менее 2 часов назад */
    } elseif ((int)$users_bids_option["timestamp_bid"] > strtotime("2 hour ago") && (int)$users_bids_option["timestamp_bid"] > strtotime("today midnight")) {
        $date_time_of_bid = 'Час назад';
        /* для ставок, сделанных более 2 часов назад, но сегодня  */
    } elseif
    ((int)$users_bids_option["timestamp_bid"] > strtotime("today midnight")) {
        $date_time_of_bid = $hours_ago . get_noun_plural_form($hours_ago,
                ' час ', ' часа ', ' часов ') . ' назад';
        /* для ставок, сделанных вчера */
    } elseif
    ((int)$users_bids_option["timestamp_bid"] > strtotime("yesterday midnight")) {
        $date_time_of_bid = "Вчера, в " . $users_bids_option["time_of_bid"];
        /* для остальных ставок */
    } else {
        $date_time_of_bid = $users_bids_option["date_time_of_bid"];
    }
    return $date_time_of_bid;
}

/** Функция формирует запросы к БД для получения данных для формирования таблицы истории ставок по лоту.
 * Принимает ресурс соединения и id лота.
 * Возвращает массив с данными по ставкам, либо null, если ставок не было.
 * @param mysqli $link
 * @param int $lot_id
 * @return array|null
 */
function get_list_of_lots_bids(mysqli $link, int $lot_id): ?array
{
    $sql = 'SELECT u.name,
       b.bid_amount,
       b.date,
       UNIX_TIMESTAMP(b.date)  AS timestamp_bid,
       TIME_FORMAT(b.date, "%H:%i") AS time_of_bid,
       DATE_FORMAT(b.date, "%d.%m.%y в %H:%i") AS date_time_of_bid
FROM bid b
LEFT JOIN user u
                   ON b.user_id = u.id
WHERE b.lot_id = ?
ORDER BY b.date DESC';
    $stmt = db_get_prepare_stmt($link, $sql, [$lot_id]);
    return select($stmt, $link);
}

/** Функция осуществляет запросы к БД для полнотекстового поиска с учетом поискового запроса $search.
 *  $page_items, $offset определяют количество возвращаемых лотов с учетом построения пагинации.
 * @param mysqli $link
 * @param string $search
 * @param $page_items
 * @param $offset
 * @return array|null
 */
function get_search_result(mysqli $link, string $search, $page_items, $offset): ?array
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
WHERE l.completion_date > now() AND MATCH(l.name, l.description) AGAINST(?)
ORDER BY l.creation_date DESC
LIMIT ? OFFSET ?';
    $stmt = db_get_prepare_stmt($link, $sql, [$search, $page_items, $offset]);
    $result = select($stmt, $link);
    if (isset($result[0])) {
        return $result;
    } else {
        return null;
    }
}

/** Функция для определения количества лотов, которые выводятся в результатах поиска по поисковому запросу $search.
 * @param mysqli $link
 * @param string $search
 * @return array|null
 */
function get_search_num(mysqli $link, string $search): ?array
{
    $sql = 'SELECT COUNT(l.id) AS result_num
FROM lot l
WHERE l.completion_date > now() AND MATCH(l.name, l.description) AGAINST(?)';
    $stmt = db_get_prepare_stmt($link, $sql, [$search]);
    return select($stmt, $link);
}

/** Функция определяет победившие ставки и id пользователей-победителей по завершившимся лотам
 * Принимает ресурс соединения.
 * Возвращает массив c id лотов, id-пользователей-победителей и id победивших ставок
 * @param mysqli $link
 * @return array|null
 */
function get_winners_id(mysqli $link): ?array
{
    $sql = 'SELECT  b.lot_id,
		b.user_id,
       b.id AS bid_id
FROM (SELECT  b.lot_id,
      MAX(b.id) AS winner_bid_id
      FROM lot l
      LEFT JOIN bid b 
      ON l.id = b.lot_id
      WHERE l.completion_date <= now() AND l.winner_id IS NULL AND b.lot_id IS NOT NULL
      GROUP BY b.lot_id) AS lot_and_winner_bid
LEFT JOIN bid b 
    ON lot_and_winner_bid.winner_bid_id = b.id';
    $stmt = db_get_prepare_stmt($link, $sql);
    return select($stmt, $link);
}

/** Функция добавляет winner_id в таблицу лота.
 * Принимает ресурс соединения, id лота, id пользователя-победителя в аукционе по лоту.
 * Формирует запрос на обноление winner_id в таблице лота и через функцию insert выполняет запрос и получает id обновленной строки.
 * @param $link
 * @param $lot_id
 * @param $user_id
 */
function add_winners(mysqli $link, int $lot_id, int $user_id)
{
    $sql = 'UPDATE lot
SET winner_id = ?
WHERE id = ?';
    $stmt = db_get_prepare_stmt($link, $sql, [$user_id, $lot_id]);
    insert($stmt, $link);
    return;
}

/** Функция получает из БД одномерный массив данных для вставки в шаблон письма победителю аукциона
 * @param mysqli $link
 * @param int $lot_id
 * @param int $user_id
 * @return array
 */
function get_data_for_email(mysqli $link, int $lot_id, int $user_id): array
{
    $sql = 'SELECT l.id AS lot_id,
       l.name AS lot_name,
       u.name AS user_name,
       u.email
FROM lot l
         LEFT JOIN user u
                   ON l.winner_id = u.id
WHERE l.id = ? AND u.id = ?';
    $stmt = db_get_prepare_stmt($link, $sql, [$lot_id, $user_id]);
    $email_data = select($stmt, $link);
    $email_data = $email_data[0] ?? null;
    return $email_data;
}

/** Функция для определения количества действующих лотов, которые выводятся в категории $category_id.
 * @param mysqli $link
 * @param string $search
 * @return array|null
 */
function get_lots_count(mysqli $link, int $category_id): ?array
{
    $sql = 'SELECT COUNT(l.id) AS result_num
FROM lot l
WHERE l.completion_date > now() AND category_id = ?';
    $stmt = db_get_prepare_stmt($link, $sql, [$category_id]);
    return select($stmt, $link);
}

/** Функция осуществляет запросы к БД для построения каталога лотов по категории $category_id.
 *  $page_items, $offset определяют количество возвращаемых лотов с учетом построения пагинации.
 * @param mysqli $link
 * @param string $search
 * @param $page_items
 * @param $offset
 * @return array|null
 */
function get_lots_by_category(mysqli $link, int $category_id, $page_items, $offset): ?array
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
WHERE l.completion_date > now() AND l.category_id = ?
ORDER BY l.creation_date DESC
LIMIT ? OFFSET ?';
    $stmt = db_get_prepare_stmt($link, $sql, [$category_id, $page_items, $offset]);
    $result = select($stmt, $link);
    if (isset($result[0])) {
        return $result;
    } else {
        return null;
    }
}

/** Функция для получения названия категории в строковом значении с id=$category_id.
 * При отсутствии категории с таким id, возвращает null
 * @param mysqli $link
 * @param string $search
 * @return array|null
 */
function get_category_name(mysqli $link, int $category_id): ?string
{
    $sql = 'SELECT name
FROM category
WHERE id = ?';
    $stmt = db_get_prepare_stmt($link, $sql, [$category_id]);
    $result = select($stmt, $link);
    $result = $result[0]['name'] ?? null;
    return $result;
}

/** Функция для проверки того, что дата завершения лота в формате ГГГГ-ММ-ДД больше текущей даты хотя бы на день
 * @param string $date
 * @return bool
 */
function is_date_after_today(string $date): bool
{
    $date = strtotime($date);
    if ($date >= strtotime("tomorrow midnight")) {
        return true;
    }
    return false;
}
