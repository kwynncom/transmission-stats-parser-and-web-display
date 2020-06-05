<?php

require_once('/opt/kwynn/kwcod.php');
require_once('/opt/kwynn/creds.php');
require_once(__DIR__ . '/../out/' . 'outjsonBT.php');
require_once('testCond.php');

postit();

function postit() {
    
    global $KW_INIT_ANOB_TSTATS_v_2020;

    $cro = new kwynn_creds();
    $creds = $cro->getType('tstats_2020');
    
    $parr = $KW_INIT_ANOB_TSTATS_v_2020; unset($KW_INIT_ANOB_TSTATS_v_2020);
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
	    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
	    $res = curl_exec($ch);
	    // echo $res;
	} else {
	    file_put_contents('/tmp/fifo.txt', $json);
	}
    }

    return;
}