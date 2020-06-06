<?php

require_once(__DIR__ . '/../post/dao.php');

function getjsonweb() {
    
    $dao = new dao_tstats_web();
    $dat = $dao->get();
    
    return json_encode($dat);
    
}