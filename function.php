<?php
declare(strict_types=1);

function format_cost(int $cost): string
{
    if ($cost >= 1000) {
        $cost = number_format($cost, 0, "", " ");
    }
    return $cost . " " . "<b class=\"rub\">р</b>";
}
