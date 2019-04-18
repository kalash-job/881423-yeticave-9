<?php
declare(strict_types=1);

function format_cost($cost): string
{
    $result = "";
    if (ceil($cost) >= 1000) {
        $result = number_format($cost, 0, "", " ");
    }   else {
        $result = $cost;
    }
    return $result . " <b class=\"rub\">р</b>";
}

/**
 * Подключает шаблон, передает туда данные и возвращает итоговый HTML контент
 * @param string $name Путь к файлу шаблона относительно папки templates
 * @param array $data Ассоциативный массив с данными для шаблона
 * @return string Итоговый HTML
 */
function include_template($name, array $data = []) {
    $name = 'templates/' . $name;
    $result = '';

    if (!is_readable($name)) {
        return $result;
    }

    ob_start();
    extract($data);
    require $name;

    $result = ob_get_clean();

    return $result;
}