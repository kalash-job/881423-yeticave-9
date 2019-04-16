<?php
declare(strict_types=1);

function format_cost(int $cost): string
{
    $result = "";
    if ($cost >= 1000) {
        $result = number_format($cost, 0, "", " ");
    }   else {
        $result = $cost;
    }
    return $result . " <b class=\"rub\">р</b>";
}
