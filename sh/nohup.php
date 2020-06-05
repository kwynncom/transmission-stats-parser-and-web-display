<?php

require_once('/opt/kwynn/kwcod.php');
require_once('/opt/kwynn/kwutils.php');

mynohup();

function mynohup() {
    $f = tempnam('/tmp', 'kwtrans_nohup_');
    $cmd = 'nohup php ' . __DIR__ . '/' . 'daekwtr.php ';
    $cmd .= " > $f 2>&1 &";
    shell_exec($cmd);
    usleep(100 * 1000);
    $v = trim(file_get_contents($f));
    if ($v) echo $v;
    else echo 'Probably OK' . "\n";
}
