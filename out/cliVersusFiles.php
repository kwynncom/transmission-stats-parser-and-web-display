<?php

class trans_file_analysis {
    public static function mod($r, &$saref, $rawsra) {
	
	$t = $saref;
	$upmb = $t['upmb'];
	$myr = $upmb / $t['totmb'];

	if (isset($r['cfi'])) {
	    $fir = $r['cfi']['tor']['r'];
	    $fsr = $r['cfi']['tot']['r'];
	    $fup = $r['cfi']['tor']['up'] / 1000000;
	} else $fir = $fsr = $fup = -1;
	
	$saref['myup' ] = self::get('myup' , $upmb  , $fup);
	$saref['myrat'] = self::get('myrat', $myr   , $fir );
	$saref['sra']   = self::get('sra'  , $rawsra, $fsr);
	
	return;
    }
    
    private static function get($f, $cv, $fv) {
	$v = max($cv, $fv);
	if ($cv > $fv) {
	    if ($f === 'myup' ) {
		if ($cv < 1000) $d = 1;
		else return $cv;
	    }
	    else	        $d = 3;
	}
	else if ($f === 'myup') $d = 3;
	else $d = 5;
	
	return sprintf('%0.' . $d . 'f', $v);
    }
    

}