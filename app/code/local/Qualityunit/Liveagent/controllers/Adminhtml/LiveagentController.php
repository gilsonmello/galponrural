<?php

class Qualityunit_Liveagent_Adminhtml_LiveagentController extends Mage_Adminhtml_Controller_Action {

	const LIVEAGENT_PLUGIN_NAME = 'liveagent';
	const ACTION_CREATE = 'c';

	/**
	 * @var Qualityunit_Liveagent_Helper_Settings
	 **/
	private $settings = null;

	protected function _construct() {
		parent::_construct();
		$this->settings = new Qualityunit_Liveagent_Helper_Settings();
	}

	private function initLayout() {
		$this->loadLayout()
		->_setActiveMenu('liveagent/account')
		->_addBreadcrumb(Mage::helper('adminhtml')->__('Account'), Mage::helper('adminhtml')->__('Settings'));
	}

	private function doAfterPost($params = array()) {
		$this->_redirect('*/*', $params);
	}

	private function doRegistration($post) {
		try {
			$this->saveAccountDetails(
					$post[Qualityunit_Liveagent_Helper_Settings::LA_OWNER_EMAIL_SETTING_NAME],
					$post[Qualityunit_Liveagent_Helper_Settings::LA_URL_SETTING_NAME],
					$post['apiKey'],
					$post['AuthToken']
			);
		} catch (Qualityunit_Liveagent_Exception_SignupFailed $e) {
			$this->signupFailed($e);
			return;
		}

		// create a button in the new account
		$this->createDefaultWidget();

		// open account dialog section
		$this->renderAccountDialog();
		$this->renderLayout();
		$this->doAfterPost();
	}

	private function createDefaultWidget(){
		$connectHelper = new Qualityunit_Liveagent_Helper_Connect();
		$settings = new Qualityunit_Liveagent_Helper_Settings();
		try {
			$connectHelper->createWidget($settings->getLiveAgentUrl(), $settings->getDefaultWidgetParams());
			return;
		} catch (Qualityunit_Liveagent_Exception_Base $e) {
			throw new Qualityunit_Liveagent_Exception_ConnectFailed($e->getMessage());
		}
	}

	private function doConnectAccount($post) {
		if (!$this->checkAccountSettings($post)) { // check posted values
			$this->doAfterPost($this->getAccountSettingsPost($post));
			return;
		}
		try {
			$this->sendConnectRequest(
					$post[Qualityunit_Liveagent_Helper_Settings::LA_URL_SETTING_NAME],
					$post[Qualityunit_Liveagent_Helper_Settings::LA_OWNER_EMAIL_SETTING_NAME],
					$post[Qualityunit_Liveagent_Helper_Settings::LA_API_KEY]
			);
			$this->_redirect('*/*');
		} catch (Qualityunit_Liveagent_Exception_ConnectFailed $e) {
			$this->connectFailed($e);
			return;
		}
		return;
	}

	private function doSaveButtonCode($post) {
		$this->settings->setOption(Qualityunit_Liveagent_Helper_Settings::BUTTON_CODE,
			html_entity_decode($post[Qualityunit_Liveagent_Helper_Settings::BUTTON_CODE]));
		$this->settings->setOption(Qualityunit_Liveagent_Helper_Settings::BUTTON_ID, $post['buttonId']);
		Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Button code was saved successfully'));

		// save additionals ...
		if (isset($post['displayInAdmin'])) {
			$this->settings->setOption(Qualityunit_Liveagent_Helper_Settings::DISPLAY_IN_ADMIN, $post['displayInAdmin']);
		} else {
			$this->settings->setOption(Qualityunit_Liveagent_Helper_Settings::DISPLAY_IN_ADMIN, '');
		}
		if (isset($post['configOptionName'])) {
			$this->settings->setOption(Qualityunit_Liveagent_Helper_Settings::ADDITIONAL_NAME, $post['configOptionName']);
		} else {
			$this->settings->setOption(Qualityunit_Liveagent_Helper_Settings::ADDITIONAL_NAME, '');
		}
		if (isset($post['configOptionEmail'])) {
			$this->settings->setOption(Qualityunit_Liveagent_Helper_Settings::ADDITIONAL_EMAIL, $post['configOptionEmail']);
		} else {
			$this->settings->setOption(Qualityunit_Liveagent_Helper_Settings::ADDITIONAL_EMAIL, '');
		}
		if (isset($post['configOptionPhone'])) {
			$this->settings->setOption(Qualityunit_Liveagent_Helper_Settings::ADDITIONAL_PHONE, $post['configOptionPhone']);
		} else {
			$this->settings->setOption(Qualityunit_Liveagent_Helper_Settings::ADDITIONAL_PHONE, '');
		}
		$this->doAfterPost();
	}

	private function doPostAction() {
		$post = $this->getRequest()->getPost();
		if (!array_key_exists('action', $post)) {
			$this->doAfterPost();
			return;
		}
		switch ($post['action']) {
			case 'r':
				  $this->doRegistration($post);
				  break;
			case Qualityunit_Liveagent_Block_AccountConnect::CONNECT_ACCOUNT_ACTION_FLAG:
				  $this->doConnectAccount($post);
				  break;
			case Qualityunit_Liveagent_Block_AccountConnect::SAVE_BUTTON_CODE_ACTION_FLAG:
				  $this->doSaveButtonCode($post);
				  break;
		}
		return;
	}

	private function getAccountSettingsChangeRevertLink() {
		return '<a href="'.$this->getUrl('*/*').'">' . Mage::helper('adminhtml')->__('Undo this change') . '</a>';
	}

	private function getAccountSettingsPost($post) {
		return array(
				Qualityunit_Liveagent_Helper_Settings::LA_URL_SETTING_NAME => ' ' . trim(base64_encode($post[Qualityunit_Liveagent_Helper_Settings::LA_URL_SETTING_NAME])),
				Qualityunit_Liveagent_Helper_Settings::LA_OWNER_EMAIL_SETTING_NAME => ' ' . trim(base64_encode($post[Qualityunit_Liveagent_Helper_Settings::LA_OWNER_EMAIL_SETTING_NAME])),
				Qualityunit_Liveagent_Helper_Settings::LA_API_KEY => ' ' . trim(base64_encode($post[Qualityunit_Liveagent_Helper_Settings::LA_API_KEY])),
				'action' => Qualityunit_Liveagent_Block_AccountConnect::CONNECT_ACCOUNT_ACTION_FLAG
			);
	}

	private function signupFailed($e) {
		Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
		$this->doAfterPost();
	}

	private function connectFailed($e) {
		Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
		$this->doAfterPost(array('action' => Qualityunit_Liveagent_Block_AccountConnect::CONNECT_ACCOUNT_ACTION_FLAG));
	}

	public function postAction() {
		$this->initLayout();
		try {
			$this->doPostAction();
		} catch (Exception $e) {
			Mage::log($e->getMessage(), Zend_log::ERR);
			Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
			$this->_redirect('*/*');
			$this->renderLayout();
		}
	}

	private function checkAccountSettings($post) {
		if (trim($post[Qualityunit_Liveagent_Helper_Settings::LA_OWNER_EMAIL_SETTING_NAME]) == null) {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Email can not be empty.') . ' ' . $this->getAccountSettingsChangeRevertLink());
			return false;
		}
		if (trim($post[Qualityunit_Liveagent_Helper_Settings::LA_API_KEY]) == null) {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('API key can not be empty.') . ' ' . $this->getAccountSettingsChangeRevertLink());
			return false;
		}
		if (trim($post[Qualityunit_Liveagent_Helper_Settings::LA_URL_SETTING_NAME]) == null) {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('URL can not be empty.') . ' ' . $this->getAccountSettingsChangeRevertLink());
			return false;
		}
		if (strpos($post[Qualityunit_Liveagent_Helper_Settings::LA_OWNER_EMAIL_SETTING_NAME], '@') === false) {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Must be a valid email.') . ' ' . $this->getAccountSettingsChangeRevertLink());
			return false;
		}
		return true;
	}

	public function indexAction() {
		$this->initLayout();
		try {
			$this->doIndexAction();
		} catch (Exception $e) {
			Mage::log($e->getMessage(), Zend_log::ERR);
			Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
			$this->renderLayout();
		}
	}

	public function doIndexAction() {
		if ($this->getRequest()->getParam('action')=="") {
			$this->renderAccountDialog();
			$this->renderLayout();
			return;
		}
		if ($this->getRequest()->getParam('action')==Qualityunit_Liveagent_Block_AccountConnect::CONNECT_ACCOUNT_ACTION_FLAG) {
 			$block = new Qualityunit_Liveagent_Block_AccountConnect();
 		}
 		if ($this->getRequest()->getParam('action')==Qualityunit_Liveagent_Block_AccountConnect::RESET_SETTINGS_ACTION_FLAG) {
 		    $this->resetSettings();
 		 	$this->doAfterPost();
 		 	return;
 		}
		$this->_addContent($this->getLayout()->createBlock($block));
		$this->renderLayout();
	}

	private function renderAccountDialog() {
		if ($this->isSetStatus()) {
			if (Qualityunit_Liveagent_Helper_Account::isOnline()) {
				 $block = new Qualityunit_Liveagent_Block_Buttoncode();
			} else {
				Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Unable to connect LiveAgent at') . ' ' . $this->settings->getLiveAgentUrl());
				$block = new Qualityunit_Liveagent_Block_AccountConnect();
			}
		} else {
			$block = new Qualityunit_Liveagent_Block_Signup();
		}
		$this->_addContent($this->getLayout()->createBlock($block));
	}

	private function isSetStatus() {
		return $this->settings->getAccountStatus() == Qualityunit_Liveagent_Helper_Settings::ACCOUNT_STATUS_SET;
	}

	private function sendConnectRequest($url, $email, $apikey) {
		$connectHelper = new Qualityunit_Liveagent_Helper_Connect();
		try {
      if ((strpos($url, 'http:') !== false) && (strpos($url, '.ladesk.com') !== false)) {
        $url = str_replace('http:', 'https:', $url);
      }
			$response = $connectHelper->connectWithLA($url, $email, $apikey);
			$this->saveAccountDetails($email, $url, $apikey, $response->authtoken);
		} catch (Qualityunit_Liveagent_Exception_Base $e) {
			throw new Qualityunit_Liveagent_Exception_ConnectFailed($e->getMessage());
		}
		Mage::log("Connect response recieved: " . print_r($response, true), Zend_log::DEBUG);
		Mage::log("Response OK", Zend_log::DEBUG);
	}

	private function resetSettings() {
	    $this->settings->setOption(Qualityunit_Liveagent_Helper_Settings::LA_URL_SETTING_NAME, '');
	    $this->settings->setOption(Qualityunit_Liveagent_Helper_Settings::LA_OWNER_EMAIL_SETTING_NAME, '');
	    $this->settings->setOption(Qualityunit_Liveagent_Helper_Settings::LA_API_KEY, '');
	    $this->settings->setOption(Qualityunit_Liveagent_Helper_Settings::OWNER_AUTHTOKEN, '');
	    $this->settings->setOption(Qualityunit_Liveagent_Helper_Settings::ACCOUNT_STATUS, '');
	}

	private function saveAccountDetails($email, $domain, $apiKey, $authToken) {
		if ((strpos($domain, 'http:') === false) && (strpos($domain, 'https:') === false)) {
			$this->settings->setOption(Qualityunit_Liveagent_Helper_Settings::LA_URL_SETTING_NAME, 'https://' . $domain . '.ladesk.com/');
		} else {
			if (substr($domain, -1) != '/') {
				$domain .= '/';
			}
			$this->settings->setOption(Qualityunit_Liveagent_Helper_Settings::LA_URL_SETTING_NAME, $domain);
		}

		$this->settings->setOption(Qualityunit_Liveagent_Helper_Settings::LA_OWNER_EMAIL_SETTING_NAME, $email);
		$this->settings->setOption(Qualityunit_Liveagent_Helper_Settings::LA_API_KEY, $apiKey);
		$this->settings->setOption(Qualityunit_Liveagent_Helper_Settings::OWNER_AUTHTOKEN, $authToken);
		$this->settings->setOption(Qualityunit_Liveagent_Helper_Settings::ACCOUNT_STATUS, Qualityunit_Liveagent_Helper_Settings::ACCOUNT_STATUS_SET);
	}
}