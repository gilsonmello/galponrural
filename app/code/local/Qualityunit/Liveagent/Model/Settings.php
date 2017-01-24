<?php

class Qualityunit_Liveagent_Model_Settings extends Mage_Core_Model_Abstract {
	public function _construct() {
		parent::_construct();
		$this->_init('liveagent/settings');
	}

	public function getChatsOverview() {
		$settings = new Qualityunit_Liveagent_Helper_Settings();
		if ($settings->getApiKey() == '' || $settings->getLiveAgentUrl() == '') {
			return array('error' => 'Your LiveAgent module is not configured yet. When the configuration is set correctly, you will see some basic reports here.');
		}
		$connect = new Qualityunit_Liveagent_Helper_Connect();
		try {
			$result = $connect->getOverview($settings->getLiveAgentUrl(), $settings->getApiKey());
			$overviews = array('error' => '',
				'visitors' => $result->visitors,
				'chats' => $result->chats,
				'queue' => $result->queue,
				'agents' => $result->agents
			);
		} catch (Qualityunit_Liveagent_Exception_ConnectFailed $e) {
			$overviews = array('error' => 'Error occurred: ' . $e->getMessage());
		}
		return $overviews;
	}
}