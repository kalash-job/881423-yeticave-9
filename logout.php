<?php
declare(strict_types=1);
require_once 'init.php';
unset($_SESSION['user']);
header("Location: /index.php");