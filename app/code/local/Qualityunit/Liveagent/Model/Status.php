<?php

class Qualityunit_Liveagent_Model_Status extends Varien_Object {
	const STATUS_ENABLED	= 'Y';
	const STATUS_DISABLED	= 'N';

	static public function getOptionArray() {
		return array(
				self::STATUS_ENABLED    => Mage::helper('liveagent')->__('Enabled'),
				self::STATUS_DISABLED   => Mage::helper('liveagent')->__('Disabled')
		);
	}
}