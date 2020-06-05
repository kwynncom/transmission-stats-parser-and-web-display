<?php

require_once('/opt/kwynn/kwutils.php');
require_once('/opt/kwynn/creds.php');
require_once('testCond.php');
require_once(__DIR__ . '/dao.php');

doit_GetTStats();

function doit_GetTStats() {

    if (!tstatspcTest()) $file = 'php://input';
    else	         $file = '/tmp/fifo.txt';
    $json = file_get_contents($file);
    
    kwas(isset($json) && $json && is_string($json), 'invalid input');
    $dat = json_decode($json, 1);
    kwas(isset($dat['key']) && $dat['key'], 'invalid input 2');
    $key = $dat['key'];
    unset($dat['key']); // FOR SECURITY!  We don't want this saved!  
    
    kwas(is_string($key) && strlen(trim($key)) > 15, 'invalid input 3');

    $co = new kwynn_creds();
    $cr = $co->getType('tstats_2020');
    
    $hash = hash('sha256', $key);
    kwas($hash && is_string($hash) && strlen(trim($hash)) === 64, 'invalid hash');
    
    $cmpHash = $cr['receive_key'];
    
    kwas($hash === $cmpHash , 'invalid input 4');
    putTSDat($dat);
    
}

function putTSDat($dat) {
    $dao = new dao_tstats_web();
    $dao->put($dat);
}