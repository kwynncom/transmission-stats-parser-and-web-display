<?php

require_once('/opt/kwynn/kwcod.php');
require_once(__DIR__ . '/../dao.php');

define('KW_TSTATS_IGNORE_MB_D', 10);
define('KW_TSTATS_IGNORE_HR_D',  3);

function getTSOutput() {
    
    $dao = new dao_tstats();
    $rawall = $dao->get(); unset($dao);
    $all = tstats_ht_filter($rawall); unset($rawall);
    $all = htf2($all);
    
    return $all;

}

function htf2($in) {
    $ret['headers'] = ['rat', 'MB', 'asof', 's4','l4', 'r4', 's6','l6', 'r6', 'seedt', 'hide'];
    $ret['v'] = [];
    
    foreach($in as $r) {
	$t = [];
	$t[] = $r['rat'];
	$t[] = $r['upmb'];
	$t[] = $r['asof'];
	poprat($t, $r);
	$t[] = $r['seedtime'];
	$t[] = $r['ts'];
	$ret['v'][] = $t;
    }
   
    return $ret;
}

function poprat(&$t, $r) {
    
    $fs = [4,6];
    
    foreach($fs as $f) {
	$l = $f . 'l';
	$s = $f . 's';
	$rat = $f . 'r';
	$t[] = $r[$s];
	$t[] = $r[$l];
	if (isset($r[$l]) && $r[$l] > 0) $t[] = intval(round($r[$s] / $r[$l]));
	else				 $t[] = '-';
    }
    
    return;
}

function poptra(&$t, $r) {
    $x = 2;
    $t['4s'] = $r['ipv4']['seeders'];
    $t['4l'] = $r['ipv4']['leechers'];
    $t['6s'] = $r['ipv6']['seeders'];
    $t['6l'] = $r['ipv6']['leechers'];
}

function tstats_ht_filter($rin) {
    $ret = [];
    
    $rcnt = count($rin);
    
    foreach($rin as $r) {
	$tor = $r['tor'];
	$t = [];
	$up   = $tor['Uploaded']['v'];
	$up   = floatval(preg_replace('/[^\d\.]/', '', $up));
	$t['upmb'] = $up;
	$t['rat'] = floatval($tor['Ratio']['v']);
	
	poptra($t, $r['tra']);
	
	$seed = $tor['Seeding Time']['v'];
	$seed = preg_replace('/\s+\(\d+ seconds\)\s*/', '', $seed);
	$seed = seed_filt($seed);
	$t['seedtime'] = $seed;

	// $t['asof'] = date('D M d h:i A', $r['ts']); // Wed Jun 03 06:08 PM
	// $t['asof'] = date('D n/j h:iA', $r['ts']);  // Fri 6/5 08:21PM	
	$t['asof'] = date('D n/j h:iA (s', $r['ts']) . 's)';  // Wed 6/3 09:50PM (00s)	
	
	$t['ts']   = $r['ts'];
	
	$ret[] = $t;
    }
    
    filterClose($ret);
    
    return $ret;

}

function cutme($ain, $bin, $cin) {
    
    $amb = $ain['upmb'];
    $bmb = $bin['upmb'];
    $as  = $ain['ts'];
    $bs  = $cin['ts'];
    
    $sd  = abs($as - $bs);
    $hrd = $sd / 3600;
    $mbd = abs($amb - $bmb);
        
    if ($mbd < KW_TSTATS_IGNORE_MB_D && $hrd  < KW_TSTATS_IGNORE_HR_D) return true;
    return false;
}

function filterClose(&$vin) {
    $cnt = count($vin);
    if ($cnt <= 2) return;
    
    $un = [];
    for ($i=2; $i < $cnt - 2; $i++) if (cutme($vin[$i],  $vin[$i-1], $vin[$i+1])) $un[$i] = 1;
    
    if (count($un) === 0) return;
    foreach($un as $i => $ignore) unset($vin[$i]);
    $vin = array_values($vin); // reorder indexes
    // filterClose($vin);
}

function seed_filt($s) {
    $s = str_replace(' days' , 'd', $s);
    $s = str_replace(' hours', 'h', $s);    
    $s = str_replace(', ', ' ', $s);  
    return $s;
}
