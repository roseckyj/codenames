<?php

require "php_connect.php";

if (! isset($_GET['game_id'])) {
    echo('error: argument not set');
    exit();
}

$result = dibi::query("SELECT * FROM `clicks_v3` WHERE game_uid = '".$_GET['game_id']."'");


$r = array();

foreach ($result as $n => $row) {
    array_push($r, $row['location']);
}

sort($r);
$r = array_unique($r);
$r = array_values($r);

echo(json_encode($r));
  
?>