<?php

class Qualityunit_Liveagent_Model_Mysql4_Buttons extends Mage_Core_Model_Mysql4_Abstract
{
	public function _construct()
	{
		$this->_init('liveagent/liveagentbuttons', 'liveagentbutton_id');
	}
}