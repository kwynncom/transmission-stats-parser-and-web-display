<?php

require_once('/opt/kwynn/kwutils.php');

class dao_tstats_web extends dao_generic {
    const db = 'torrweb';
    const datv = 1;
    function __construct() {
        parent::__construct(self::db);
        $this->wcoll    = $this->client->selectCollection(self::db, 'webStats');
    }
      
    public function put($dat) { 
	$this->wcoll->deleteMany([]);
	$this->wcoll->insertOne($dat); 
    }
    
    public function get() {
	return $this->wcoll->findOne([]);
    }
} 