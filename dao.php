<?php

require_once('/opt/kwynn/kwutils.php');

class dao_tstats extends dao_generic {
    const db = 'torr2020';
    const datv = 1;
    function __construct() {
        parent::__construct(self::db);
        $this->scoll    = $this->client->selectCollection(self::db, 'stats');
	$this->lcoll    = $this->client->selectCollection(self::db, 'lastPost');
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
    
    public function getLastSent() { return $this->lcoll->findOne([], ['sort' => ['ts' => -1]]); }
    public function putLastSent($din) {
	$lsv = 1;
	$din['lsv'] = $lsv;
	$this->lcoll->upsert(['lsv' => $lsv], $din);
    }
} 