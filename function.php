<?php
declare(strict_types=1);

function format_cost(float $cost): string
{
    $result = "";
    $cost = ceil($cost);
    if ($cost >= 1000) {
        $result = number_format($cost, 0, "", " ");
    }   else {
        $result = $cost;
    }
    return $result . " <b class=\"rub\">р</b>";
}

function time_to_midnight()
{
    $result = "";
    $timestamp_to_midnight;
    $timestamp_to_midnight = strtotime("tomorrow midnight") - strtotime("now");
    if ($timestamp_to_midnight > 3600) {
        $result .= "\"> " . date("H:i", (int) ($timestamp_to_midnight - 3 * 3600));
    } else {
        $result .= " timer--finishing\"> " . date("H:i", (int) ($timestamp_to_midnight - 3 * 3600));
    }
    return($result);
}
