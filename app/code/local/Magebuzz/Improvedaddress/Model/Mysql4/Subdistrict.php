<?php
/*
* Copyright (c) 2015 www.magebuzz.com 
*/
class Magebuzz_Customaddress_Model_Mysql4_Subdistrict extends Mage_Core_Model_Mysql4_Abstract {
	public function _construct() {
		$this->_init('customaddress/subdistrict', 'subdistrict_id');
	}
}