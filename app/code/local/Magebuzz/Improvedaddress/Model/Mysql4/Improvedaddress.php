<?php
/*
* Copyright (c) 2015 www.magebuzz.com 
*/
class Magebuzz_Improvedaddress_Model_Mysql4_Improvedaddress extends Mage_Core_Model_Mysql4_Abstract {
	public function _construct() {    
		// Note that the improvedaddress_id refers to the key field in your database table.
		$this->_init('improvedaddress/improvedaddress', 'improvedaddress_id');
	}
}