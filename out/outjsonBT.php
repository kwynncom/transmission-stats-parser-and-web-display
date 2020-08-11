<?php

require_once('/opt/kwynn/kwcod.php');
require_once(__DIR__ . '/../dao.php');

define('KW_TSTATS_IGNORE_MB_D', 10);
define('KW_TSTATS_IGNORE_HR_D',  3);

function getTSOutput() {
    
    $dao = new dao_tstats();
    $rawall = $dao->get(); unset($dao);
    $all = tstats_ht_filter($rawall); 
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
    $ret['headers'] = ['myr', 'rat', 'MB', 'asof', 's4','l4', 'r4', 's6','l6', 'r6', 'seedt', 'ts', 'sra'];
    $ret['finfo']['ts'] = 'hide';
    $ret['v'] = [];
        
    foreach($in as $r) {
	popperm2($ret, $r);
	$t = [];
	$t[] = getmyrat($r['upmb'], $ret['perm'][1]); // must match above
	$t[] = $r['rat'];
	$t[] = $r['upmb'];
	$t[] = $r['asof'];
	poprat($t, $r);
	$t[] = $r['seedtime'];
	$t[] = $r['ts'];
	$t[] = $r['sra'];
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
    

    $x  = 2;
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
	$up   = floatval(preg_replace('/[^\d\.]/', '', $up));
	$t['upmb'] = $up;
	$t['rat'] = floatval($tor['Ratio']['v']);
	
	poptra($t, $r['tra']);
	
	popperm($t, $tor);
	
	$seed = $tor['Seeding Time']['v'];
	$seed = preg_replace('/\s+\(\d+ seconds\)\s*/', '', $seed);
	$seed = seed_filt($seed);
	$t['seedtime'] = $seed;

	$t['asof'] = date('D n/j h:iA (s', $r['ts']) . 's)';  // Wed 6/3 09:50PM (00s)	
	
	$t['ts']   = $r['ts'];
	
	$t['sra'] = calcSRA($r);
	
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
    
    $rrs = sprintf('%0.3f', $rat);
    
    return $rrs;
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
    
    static $cfn = 15; // check first n
    
    $cnt = count($vin);
    if ($cnt < $cfn) return;
    
    $un = [];
    for ($i=$cfn; $i < $cnt - 2; $i++) if (cutme($vin[$i],  $vin[$i-1], $vin[$i+1])) $un[$i] = 1;
    
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
