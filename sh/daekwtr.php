<?php

require_once('/opt/kwynn/kwcod.php');
require_once(__DIR__ . '/../in/' . 'get.php');
require_once(__DIR__ . '/../'    . 'dao.php');
require_once(__DIR__ . '/../in/' . 'cycle.php');

doit();

function doit() {
    $o = new kwProcMutex('tstats_kwynn_2020_0603.txt');
    $o->loop('getTStats');
}

class kwProcMutex {
    
    const pidfd = '/tmp/';

    public function loop($funcin) {

	if (PHP_SAPI !== 'cli') die('cli only');

	$sleep = 20;
	
	do {
	    $funcin();
	    sleep($sleep);
	} while(1);	
    }
    
    public function __construct($name) {
	$this->pidfn = self::pidfd . $name;
	register_shutdown_function([$this, 'onshutdown']); // must be a public function, or at least not private
	$this->p1();
    }

    public function onshutdown() { // must do checks because die() will call this
	$fn = $this->pidfn;
	if (!file_exists($fn)) return;
	$pid = trim(file_get_contents($fn));
	if ($pid == getmypid()) unlink($fn);
    }
    
    private function p1() {
	
	$fn = $this->pidfn;

	if (file_exists($fn)) $this->resolveExisting();
	$fp = fopen($fn, 'w');
	if (!flock($fp, LOCK_EX)) die('could not get lock' . "\n");
	fwrite($fp, getmypid());
	fflush($fp);
	flock($fp, LOCK_UN);
	fclose($fp);
    }
    
    private function resolveExisting() {
	$pid = trim(file_get_contents($this->pidfn));
	if (!$pid || !is_numeric($pid) || !preg_match('/^\d+$/', $pid)) return;
	$r = trim(shell_exec("kill -15 $pid 2>&1 "));
	if (strpos($r, 'No such process') !== false) return;
	if ($r) die('kill failed with ' . $r);
    }
}
