<?php

require_once('/opt/kwynn/kwcod.php');
require_once(__DIR__ . '/../dao.php');
require_once('cliVersusFiles.php');
require_once('filter20.php');

define('KW_TSTATS_IGNORE_RAT',  0.98);
define('KW_TSTATS_IGNORE_HR_D',    3);

function getTSOutput() {
    
    $dao = new dao_tstats();
    $rawall = $dao->get(); 
    $all = tstats_ht_filter($rawall);
    $all = filter20::filter($all, $dao); unset($dao);
    if (!$all) return false;
    $all = htf2($all);
    $all['lmago'] = getlmago($rawall); unset($rawall);
    
    return $all;
}

function getlmago($din) {
    if (isset ($din[0]['tra_lmago']))
	return $din[0]['tra_lmago'];
    else return '';
}

function popperm2(&$vin, $cin) {
    if (isset($vin['perm'])) return;
    $fs = ['totszh', 'totmb', 'fname']; // must match below for totmb
    $vin['permheaders'] = $fs;

    foreach($fs as $f) $vin['perm'][] = $cin[$f];
    $vin['finfo']['totmb'] = 'hide';
}

function getmyrat($n, $d) {
    $ret = round($n / $d, 3);
    return $ret;
}

function htf2($in) {
    $ret['headers'] = ['myr', 'MB', 'sra', 'r4', 'r6', 'asof', 's4','l4', 's6','l6', 'seedt', 'ts'];
    $ret['finfo']['ts'] = 'hide';
    $ret['v'] = [];
        
    foreach($in as $r) {
	popperm2($ret, $r);
	$t = [];
	$t[] = $r['myrat'];
	$t[] = $r['myup'];
	$t[] = $r['sra'];
	poprat($t, $r, 'rats');
	$t[] = $r['asof'];
	poprat($t, $r, 'inputs');
	$t[] = $r['seedtime'];
	$t[] = $r['ts'];
	$ret['v'][] = $t;
    }
   
    return $ret;
}

function poprat(&$t, $r, $type) {
    
    $fs = [4,6];
    
    foreach($fs as $f) {
	$l = $f . 'l';
	$s = $f . 's';
	$rat = $f . 'r';
	if ($type === 'inputs') {
	    $t[] = $r[$s];
	    $t[] = $r[$l];
	}
	if ($type === 'rats') {
	    if (isset($r[$l]) && $r[$l] > 0) $t[] = intval(round($r[$s] / $r[$l]));
	    else				 $t[] = '-';
	}
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

function byteswu($n, $u) {
    if ($u === 'GB') $m = 1000;
    else             $m = 1;
    $pf = $n * $m;
    $pi = intval(round($pf));
    
    if (abs($pf - $pi ) < 0.09) return $pi;
    return $pf;
    
    
}

function popperm(&$rin, $tor) {
    
    $rin['fname']   = $tor['Name']['r'];
    
    $th = $tor['Total size']['r'];
    $th = trim(preg_replace('/\s\(.+/', '', $th));
    preg_match('/(\d+\.\d+) (\w+)/', $th, $m);
    $rin['totszh'] = $th;

    $mb = byteswu($m[1], $m[2]);
    $rin['totmb'] = $mb;
}

function ifExists(...$args) {
    
    $t = $args[0];
    
    for($i=2; $i < count($args); $i++) {
	if (!isset($t[$args[$i]])) return $args[1];
	$t =       $t[$args[$i]];
    }
    
    return $t;
    
}

function tstats_ht_filter($rin) {
    $ret = [];
    
    $rcnt = count($rin);
    
    foreach($rin as $r) {
	$tor = $r['tor'];
	if (!isset($tor['Uploaded']['v'])) continue;

	$t = [];

	$up   = $tor['Uploaded']['v'];
	
	preg_match('/(\d+\.\d+) (\w+)/', $up, $m); unset($up);
	$mb = byteswu($m[1], $m[2]); unset($m);
	$t['upmb'] = $mb; unset($mb);
	$t['rat'] = floatval($tor['Ratio']['v']);
	
	poptra($t, $r['tra']);
	
	popperm($t, $tor);
	
	$seed = $tor['Seeding Time']['v'];
	$seed = preg_replace('/\s+\(\d+ seconds\)\s*/', '', $seed);
	$seed = seed_filt($seed);
	$t['seedtime'] = $seed;

	$t['asof'] = date('D n/j h:iA (s', $r['ts']) . 's)';  // Wed 6/3 09:50PM (00s)	
	
	$t['ts']   = $r['ts'];
	
	$rawsra = calcSRA($r);
	
	$t['sra'] = sprintf('%0.3f', $rawsra);
	trans_file_analysis::mod($r, $t, $rawsra);
	
	$ret[] = $t;
    }
    
    filterClose($ret);
    
    return $ret;

}

function calcSRA($r) {
    $auto = ifExists($r, '', 'srv', 'Ratio'     , 'v');
    $up   = ifExists($r, '', 'srv', 'Uploaded'  , 'v');
    $dn   = ifExists($r, '', 'srv', 'Downloaded', 'v');
    
    if (!$up || !$dn) return $auto;
    
    $re = '/^\d+\.?\d*/';
    
    preg_match($re, $up, $upm);
    preg_match($re, $dn, $dnm);
    
    if (!$upm || !$dnm) return $auto;
    
    $dnf = floatval($dnm[0]);
    if (!$dnf) return $auto;
    
    $rat = $upm[0] / $dnf;
    
    return $rat;
}

function cutme($din) {
    
    static $lpts = false;
    static $lpmb = false;

    $mb = $din['upmb'];
    $ts = $din['ts'  ];
    
    if ($lpts === false) {
	$lpts = $ts;
	$lpmb = $mb;
	return false;
    }
    
    $sd  = abs($ts - $lpts);
    $hrd = $sd / 3600;
    
    if ($lpmb > 0) $mbr = $mb / $lpmb;
    else           $mbr = -1;
        
    if ($mbr > KW_TSTATS_IGNORE_RAT && $hrd  < KW_TSTATS_IGNORE_HR_D) return true;
    
    $lpts = $ts;
    $lpmb = $mb;
    
    return false;
}

function filterClose(&$vin) {
    
    static $cfn =  5; // check first n
    static $stn =  1; 
    
    $cnt = count($vin);
    if ($cnt < $cfn) return;
    if ($cnt < $stn) return;
    
    $un = [];
    for ($i=$cfn; $i < $cnt - $stn; $i++) if (cutme($vin[$i])) $un[$i] = 1;
    
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
