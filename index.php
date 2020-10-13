<?php
mb_internal_encoding("UTF-8");

require "dibi/dibi.php";
require "php_connect.php";
include 'generator.php';

error_reporting(E_ALL & ~E_NOTICE);

$game_data;
$seed = rand(1000, 9999);

if (isset($_GET['game_id'])) {
    $game_data = dibi::fetch('SELECT * FROM `games_v3` WHERE uid = %s', $_GET['game_id']);
}

if (! isset($_GET['game_id']) || !$game_data) {
    $size_x = 5;
    $size_y = 5;
    $uid = uniqid();
    $uid = substr($uid, strlen($uid) - 5, 5);
    $cells = generatePlan($size_x, $size_y, $seed);
    $words_raw = file_get_contents('http://slova.cetba.eu/generate.php?number='.($size_x*$size_y));
    die();

    if(dibi::fetch("SELECT * FROM `games_v3` WHERE uid = %s", $uid)) {
        header("Location: ./");
    }

    $arr = [
        'words' => $words_raw,
        'hint'  => implode("", $cells),
        'width'  => $size_x,
        'height'  => $size_y,
        'uid'  => $uid,
    ];

    dibi::query("INSERT INTO `games_v3` %v", $arr);

    header("Location: ./?game_id=".$uid);
    exit();
}

$game_data = dibi::fetch("SELECT * FROM `games_v3` WHERE uid = %s", $_GET['game_id']);
$size_x = $game_data['width'];
$size_y = $game_data['height'];
$cells = str_split($game_data['hint']);
$words = explode(' | ', $game_data['words']);

?>

<!DOCTYPE html>
<html>
    <head>
        <title>Krycí jména</title>
        <link rel="stylesheet" href="style.css?abc" />
        <meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
        <link href="https://fonts.googleapis.com/css?family=Open+Sans:400,700" rel="stylesheet">
        <script src="script.js"></script>
        <script>
            init('<? echo($_GET['game_id']."',".json_encode($cells)); ?>)
        </script>
    </head>
    <body>
        <div style="display: none"><endora></div>
        <?php
            echo ('<div class="wrapper">');
            for ($y = 0; $y < $size_y; $y++) {
                for ($x = 0; $x < $size_x; $x++) {
                    $i = $y * $size_x + $x;

                    $top =  (($y + 0.5) / $size_y) * 100;
                    $left = (($x + 0.5) / $size_x) * 100;

                    echo ('<div class="card_wrapper" style="top: '.$top.'%; left: '.$left.'%"><div class="card" onClick="select('.$i.', true)" id="card_'.$i.'"><span class="normal">'.$words[$i].'</span>');
                    echo ('<div class="upside-down">'.$words[$i].'</div>');                    
                    echo ('</div></div>');
                }
            }
            echo ('</div>');
            echo ('<div class="remaining remaining-red" id="remaining-red">?</div>');
            echo ('<div class="remaining remaining-blue" id="remaining-blue">?</div>');

            $reds = 0;
            $blues = 0;
            foreach ($cells as $w) {
                if ($w == 1) {
                    $reds++;
                }
                if ($w == 2) {
                    $blues++;
                }
            }
            $red_starts = $reds > $blues;

            echo ('<div class="seed_bg" id="blindfold"><div class="seed"><div class="seed_title">váš kód hry je</div>'.$_GET['game_id'].'<div class="seed_title">(začínají '.($red_starts ? "červení" : "modří").')</div>
            <div class="button" onClick="startGame(0)">Jsem agent<span class="subtitle">Hádám zadaná slova</span></div><div class="button" onClick="startGame(1)">Jsem spymaster<span class="subtitle">Zadávám slova</span></div>
            </div>
            <div class="footer"><a href="http://server.9e.cz/v2">Nebo se můžete vrátit ke staré verzi s odděleným návodem pro spymastera.</a></div>
            </div>');
        ?>
    </body>
</html>