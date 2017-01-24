<?php
class Qualityunit_Liveagent_Helper_Account extends Mage_Core_Helper_Abstract {

  	public static function isOnline() {
  	    $isOnline = Mage::getSingleton('adminhtml/session')->getMyIsOnline();
  	    if ($isOnline != '') return true;

    		$connect = new Qualityunit_Liveagent_Helper_Connect();
    		$settings = new Qualityunit_Liveagent_Helper_Settings();
    		try {
      			$connect->ping($settings->getLiveAgentUrl(), $settings->getApiKey());
      			Mage::getSingleton('adminhtml/session')->setMyIsOnline('online');
      			Mage::log('Account is online!', Zend_Log::INFO);
      			return true;
    		} catch (Qualityunit_Liveagent_Exception_ConnectProblem $e) {
      			Mage::log('Account not online', Zend_Log::INFO);
      			return false;
    		}
  	}
}