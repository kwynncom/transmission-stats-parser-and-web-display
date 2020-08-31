<?php

function filter20($all) {
    
    for($i=0; $i < 2; $i++) if (!isset($all[$i])) return $all;
    
    $fs = ['myrat', 'myup', 'sra'];
    
    foreach($fs as $f) if ($all[0][$f] - $all[1][$f] > 0.0012) return $all;
    
    if ($all[0]['ts'] - $all[1]['ts'] >= 300) return $all;
    
    return false;
}