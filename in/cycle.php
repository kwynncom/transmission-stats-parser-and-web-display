<?php

require_once('/opt/kwynn/kwcod.php');
require_once(__DIR__ . '/' . 'get.php');
require_once(__DIR__ . '/'. '../' . 'dao.php');

function getTStats() {
    $o = new transmission_stats();
    $dat = $o->get();
    $dao = new dao_tstats();
    $dao->put($dat);
    require_once(__DIR__ . '/../post/post.php');
}
