<?php
/*
* Copyright (c) 2015 www.magebuzz.com 
*/
class Magebuzz_Improvedaddress_Model_Status extends Varien_Object {
	const STATUS_ENABLED	= 1;
	const STATUS_DISABLED	= 2;

	static public function getOptionArray() {
		return array(
			self::STATUS_ENABLED    => Mage::helper('improvedaddress')->__('Enabled'),
			self::STATUS_DISABLED   => Mage::helper('improvedaddress')->__('Disabled')
		);
	}
}