<?php

require "php_connect.php";

if (! isset($_GET['game_id']) || ! isset($_GET['i'])) {
    echo('error: argument not set');
    exit();
}

dibi::query("INSERT INTO `clicks_v3` (`game_uid`, `location`, `ip`) VALUES ('".$_GET['game_id']."', '".$_GET['i']."', '".$_SERVER['REMOTE_ADDR']."');");
echo('OK');

?>