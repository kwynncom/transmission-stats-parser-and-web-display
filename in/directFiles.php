<?php

require_once('/opt/kwynn/kwcod.php');
require_once('/opt/kwynn/kwutils.php');

class trans_direct_files {
    
    public static function get() {
	$base = '/home/' . get_current_user() . '/.config/transmission/';
	$cn   = get_called_class();
	
	$tret = [];
	$fs = ['resume/' => "$cn::torrent", 'stats.json' => "$cn::total"];
	foreach($fs as $f => $fu) {
	    $path = $base . $f;
	    kwas(file_exists($path), 'path does not exist');
	    $tret[] = $fu($path);
	}
	
	$ret['total'  ] = $tret[1];
	$ret['torrent'] = $tret[0];
	
	return $ret;
    }
    
    private static function torrent($base) {
	$g = glob($base . '*'); kwas(isset($g[0]), 'no resume file found'); unset($base);
	$file = $g[0]; unset($g);
	$t = file_get_contents($file); kwas($t, 'file read filed - torrent');
	$ts = filemtime($file); unset($file);
	
	$fs = ['uploaded' => 'up', 'downloaded' => 'dn'];
	
	$vs = [];
	foreach ($fs as $f => $alias) {
	    preg_match('/' . $f . 'i(\d+)e/', $t, $m); kwas(isset($m[1]), 'bad up');
	    $b = intval($m[1]); unset($m);
	    $om = log($b, 10);
	    $vs[$alias] = $b; unset($om, $b, $f, $alias);
	} unset($t, $fs);
	
	$r  = $vs['up'] / $vs['dn'];
	$up = $vs['up']; unset($vs);
	
	return get_defined_vars();
    }
    
    private static function total($path) {
	$t = file_get_contents($path); kwas($t, 'file read failed');
	$ts = filemtime($path); unset($path);
	
	$a = json_decode($t, 1); unset($t);
	$r = $a['uploaded-bytes'] / $a['downloaded-bytes']; unset($a);
	return get_defined_vars();
	
    }
}

trans_direct_files::get();

