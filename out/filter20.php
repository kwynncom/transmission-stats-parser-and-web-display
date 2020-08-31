<?php

class filter20 {
    
    public static function filter($aa, $dao) { 
	$o = new self($aa, $dao);
	return $o->getDat();
	
    }

    private function __construct($aain, $dao) {
	$this->rdat = false;
	if (!$aain || !isset($aain[0])) { $this->rdat = false; return; }
	$l = $dao->getLastSent();
	if (!$l) {
	    $dao->putLastSent($aain[0]);
	    $this->rdat = $aain;
	    return;
	}

	$cmp[0] = $aain[0];
	$cmp[1] = $l;
	
	$res = self::filter20($cmp);
	if ($res) {
	    $dao->putLastSent($aain[0]);	    
	    $this->rdat = $aain;
	}
	else      $this->rdat = false;
    }
    
    private function getDat() { return $this->rdat; }
    
    private static function filter20($all) {

	for($i=0; $i < 2; $i++) if (!isset($all[$i])) return $all;

	$fs = ['myrat', 'myup', 'sra', 'ts'];

	foreach($fs as $f) {
	    $v0 = $all[0][$f];
	    $v1 = $all[1][$f];
	    $d = $v0 - $v1;
	    if ($f !== 'ts' && $d >   0.000009) return $all;
	    if ($f === 'ts' && $d > 300       ) return $all;
	}
	return false;
    }

}