<?php
/*
* Copyright (c) 2015 www.magebuzz.com 
*/
class Magebuzz_Customaddress_Model_Mysql4_Subdistrict_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract {
	public function _construct() {
		parent::_construct();
		$this->_init('customaddress/subdistrict');
	}
	
	public function toOptionArray() {
		$options = $this->_toOptionArray('subdistrict_id', 'default_name', array('title' => 'default_name'));
		if (count($options) > 0) {
			array_unshift($options, array(
				'title '=> null,
				'value' => "",
				'label' => Mage::helper('customaddress')->__('-- Please select --')
			));
		}
		return $options;
	}
}