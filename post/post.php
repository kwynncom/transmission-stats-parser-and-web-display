<?php

require_once('/opt/kwynn/kwcod.php');
require_once('/opt/kwynn/creds.php');
require_once(__DIR__ . '/../out/outjsonBT.php');
require_once('testCond.php');

function postit() {
    
    $cro = new kwynn_creds();
    $creds = $cro->getType('tstats_2020');
    
    $parr = getTSOutput(); 
    $parr['key'] = $creds['send_key'];
    
    $urls[]   = $creds['localhost'];
    $urls[]   = 'https://kwynn.com/t/20/06/tstats/';
    
    $json = json_encode($parr);
    
    foreach($urls as $url) {
    
	$url .= 'post/receive.php';
	if (!tstatspcTest()) { 
	    $ch = curl_init();		
	    curl_setopt($ch, CURLOPT_URL, $url);		
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
	    curl_setopt($ch, CURLOPT_POST, TRUE);		
	    curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
	    $res = curl_exec($ch);
	    curl_close($ch);
	} else {
	    file_put_contents('/tmp/fifo.txt', $json);
	}
    }

    return;
}