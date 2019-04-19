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

function time_to_midnight($timestamp_to_midnight)
{
    $minut;
    $hour;
    $minut = intdiv($timestamp_to_midnight, 60) % 60;
    if ($minut < 10) {
        $minut = "0" . (string)$minut;
    }
    $hour = intdiv($timestamp_to_midnight, 3600) % 24;
    if ($hour < 10) {
        $hour = "0" . (string)$hour;
    }
    return($hour . ":" . $minut);
}

function second_to_midnight($timestamp_of_end)
{
    $timestamp_to_midnight;
    $timestamp_to_midnight = $timestamp_of_end - strtotime("now");
    return($timestamp_to_midnight);
}

function color_hour_to_midnight($timestamp_to_midnight)
{
    $result = "";
    if ($timestamp_to_midnight <= 3600) {
        $result .= " timer--finishing";
    }
    return($result);
}