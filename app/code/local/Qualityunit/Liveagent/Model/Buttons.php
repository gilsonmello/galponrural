<?php

class Qualityunit_Liveagent_Model_Buttons extends Mage_Core_Model_Abstract {
	public function _construct() {
		parent::_construct();
		$this->_init('liveagent/buttons');
	}
}