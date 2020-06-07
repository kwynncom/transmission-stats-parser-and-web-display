<?php

require_once('/opt/kwynn/kwcod.php');
require_once('/opt/kwynn/kwutils.php');
require_once('/opt/kwynn/creds.php');

class transmission_stats {
    
    public function __construct() {
	$this->alldat = [];
	$this->alldat['tor'] = [];
	$this->alldat['tra'] = [];
	$this->alldat['errmsg'] = '';
	$this->alldat['tra_lmago'] = '';
	$this->p1();
    }
    
    public function get() { return $this->alldat; }

    private function runCmd($sw = '') {
	$creds = new kwynn_creds();
	$credo = $creds->getType('transmission_bittorrent_remote_user');
	$cred  = $credo['cred'];
	$cmd  = 'transmission-remote -n ';
	$cmd .= " '$cred' ";
	$cmd .= ' -t 1 -i';
	$cmd .= $sw;
	$cmd .= ' 2>&1 ';
	$cret = shell_exec($cmd);
	$fret = $this->cmdErrCk($cret);
	return $fret;
    }
    
    private function cmdErrCk($res) {
	if (strpos($res, "Couldn't connect to server") !== false) {
	    $this->alldat['errmsg'] = 'Transmission (BitTorrent client) is down';
	    return null;
	}
	
	return $res;
    }

    private function toGet() {
	$perm = ['Name', 'Total size', 'Date added', 'Downloading Time'];
	$curr = ['State','Uploaded','Ratio', 'Peers', 'Seeding Time'];
	$ret['perm'] = $perm;
	$ret['curr'] = $curr;
	return $ret;
    }

    private function p1() {
	$this->rawdatto = $this->runCmd();
	if (!$this->rawdatto) return;
	$fa  = $this->toGet();
	
	array_walk_recursive($fa, [$this, 'parse']); unset($fa, $this->rawdatto);
	
	$rawtd = $this->runCmd('t');
	$trdat = $this->ptr($rawtd); unset($rawtd);
	
	$this->alldat['tor'] = $this->tordat; unset($this->tordat);
	$this->alldat['tra'] = $trdat; unset($trdat);
	
	return;
	
    }
    
    private function ptr($raw) {

	//     Tracker had 731 seeders and 18 leechers 7 minutes (442 seconds) ago
	//     Tracker had  731 seeders and   18   leechers 7 minutes (442 seconds) ago
	$p = '/Tracker had (\d+) seeders and (\d+) leechers [^\(]+\((\d+) seconds\) ago/';
	preg_match_all($p, $raw, $matches); unset($p, $raw);

	$i = 0;
	$now = time();
	
	$ks1 = ['ipv4', 'ipv6'];
	$ks2 = ['seeders', 'leechers', 's_ago'];
	$ret = [];
	
	foreach($ks1 as $k) {
	    
	    for ($j=1; $j <= 3; $j++) if (!isset($matches[$j][$i])) {
		if ($j <= 2) $jv = 0;
		else $jv = -1;
		$matches[$j][$i] = $jv; unset($jv);
	    } unset($j);
	    
	    $ret[$k]['seeders' ] = intval($matches[1][$i]);
	    $ret[$k]['leechers'] = intval($matches[2][$i]);
	    $mago = intval($matches[3][$i]);
	    $ret[$k]['s_ago'] = $mago;
	    if (!$this->alldat['tra_lmago']) 
		 $this->alldat['tra_lmago'] = intval(round($mago / 60));
	    	    
	    $i++;
	    unset($k);
	} unset($ks1, $now, $i, $matches);
	
	return $ret;
    }
    
    private function parse($key) {
	$r = "/$key:\s*(.+)\n/";
	preg_match($r, $this->rawdatto, $matches);
	if (!isset($matches[1])) {
	    return;
	}
	if (!trim($matches[1])) {
	    return;
	}
	$this->tordat[$key]['r'] = trim($matches[0]);
	$this->tordat[$key]['v'] = trim($matches[1]);

	$t = $matches[1];
	
	return;
		
    }
}