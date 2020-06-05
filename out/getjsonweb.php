<?php

require_once(__DIR__ . '/../post/dao.php');

$KW_INIT_JSON_TSTATS_v_2020 = '';

getjsonweb();
function getjsonweb() {
    
    global $KW_INIT_JSON_TSTATS_v_2020;
    
    $dao = new dao_tstats_web();
    $dat = $dao->get();
    
    $KW_INIT_JSON_TSTATS_v_2020 = json_encode($dat);
    
}