<?php

require_once('/opt/kwynn/kwutils.php');

class dao_tstats extends dao_generic {
    const db = 'torr2020';
    const datv = 1;
    function __construct() {
        parent::__construct(self::db);
        $this->scoll    = $this->client->selectCollection(self::db, 'stats');
    }
      
    public function put($dat) { 
	$now = time();
	$dat['ts'] = $now;
	$dat['r' ] = date('r', $now); unset($now);
	$dat['datv'] = self::datv;
	$this->scoll->insertOne($dat); 
    }
    
    public function get($limit = PHP_INT_MAX) {
	return $this->scoll->find([], ['sort' => ['ts' => -1], 'limit' => $limit, /* 'projection' => ['_id' => 0] */])->toArray();
    }
} 