<?php
require_once 'function.php';
require_once 'helpers.php';
date_default_timezone_set("Europe/Moscow");
$link = mysqli_connect("localhost", "root", "", "yeticave");
if ($link === false) {
    $error = mysqli_connect_error($link);
    print(include_template('error.php', ['error' => $error]));
    die();
}
mysqli_set_charset($link, "utf8");
session_start();
if (isset($_SESSION['user'])) {
    $user_session = get_username($link, $_SESSION['user']);
    $user_session['is_auth'] = 1;
} else {
    $user_session['is_auth'] = 0;
}