<?php

function seededShuffle($arr, $seed) {
    mt_srand($seed);
    $order = array_map(create_function('$val', 'return mt_rand();'), range(1, count($arr)));
    array_multisort($order, $arr);
    return $arr;
}

function generatePlan($size_x, $size_y, $seed) {
    $red = $size_x + $size_y - 1 - ($seed % 2);
    $blue = $size_x + $size_y - 1 - (1 - $seed % 2);
    $black = 1;

    $cells = [];

    for ($i = 0; $i < $red; $i++) { array_push($cells, 1); }
    for ($i = 0; $i < $blue; $i++) { array_push($cells, 2); }
    for ($i = 0; $i < $black; $i++) { array_push($cells, 4); }
    for ($i = 0; $i < ($size_x * $size_y) - $red - $blue - $black; $i++) { array_push($cells, 3); }

    return (seededShuffle($cells, $seed));
}

?>