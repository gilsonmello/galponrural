<?php

class Qualityunit_Liveagent_Model_Mysql4_Liveagent_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
	public function _construct()
	{
		parent::_construct();
		$this->_init('liveagent/liveagent');
	}
}