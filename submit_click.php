<?php

require "php_connect.php";

if (! isset($_GET['game_id']) || ! isset($_GET['i'])) {
    echo('error: argument not set');
    exit();
}

$arr = [
    'game_uid' => $_GET['game_id'],
    'location'  => $_GET['i'],
    'ip'  => $_SERVER['REMOTE_ADDR'],
];

dibi::query('INSERT INTO `clicks_v3` %v', $arr);
echo('OK');

?>