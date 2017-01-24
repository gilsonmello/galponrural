<?php
/**
 * @copyright Copyright (c) 2007 Quality Unit s.r.o.
 * @author Juraj Simon
 * @package WpLiveAgentPlugin
 * @version 1.0.0
 *
 * Licensed under GPL2
 */

class Qualityunit_Liveagent_Helper_Settings {

	//internal settings
	const OWNER_AUTHTOKEN = 'la-settings_owner-authtoken';
	const ACCOUNT_STATUS = 'la-settings_accountstatus';

	//general settings
	const LA_URL_SETTING_NAME = 'la-url';
	const LA_OWNER_EMAIL_SETTING_NAME = 'la-owner-email';
	const LA_API_KEY = 'la-api-key';

	//buttons options
	const BUTTON_CODE = 'la-config-button-code';
	const BUTTON_ID = 'la-config-button-id';

	//additional widget options
	const ADDITIONAL_NAME = 'la-config-additional-name';
	const ADDITIONAL_EMAIL = 'la-config-additional-email';
	const ADDITIONAL_PHONE = 'la-config-additional-phone';
	const DISPLAY_IN_ADMIN = 'la-display-in-admin';

	const ACCOUNT_STATUS_SET = 'S';

	public function getOwnerAuthToken() {
		return $this->getOption(Qualityunit_Liveagent_Helper_Settings::OWNER_AUTHTOKEN);
	}

	public function getLiveAgentUrl() {
		$url = $this->getOption(Qualityunit_Liveagent_Helper_Settings::LA_URL_SETTING_NAME);
		if ($url == null) {
			return $url;
		}
		if (strpos($url, 'http://') === false && strpos($url, 'https://') === false) {
			return 'http://' . $url;
		}
		return $url;
	}

	public function getOwnerEmail() {
		return $this->getOption(Qualityunit_Liveagent_Helper_Settings::LA_OWNER_EMAIL_SETTING_NAME);
	}

	public function getApiKey() {
		return $this->getOption(Qualityunit_Liveagent_Helper_Settings::LA_API_KEY);
	}

	public function setOption($name, $value) {
		$setting = Mage::getModel('liveagent/settings')->load($name, 'name');
		$setting->setValue($value);
		$setting->setName($name);
		$setting->save();
	}

	public function getAllWidgets() {
		$connectHelper = new Qualityunit_Liveagent_Helper_Connect();
		try {
			$widgetsList = $connectHelper->getWidgets($this->getOption(Qualityunit_Liveagent_Helper_Settings::LA_URL_SETTING_NAME),
					$this->getOption(Qualityunit_Liveagent_Helper_Settings::LA_API_KEY));
			return $widgetsList;
		} catch (Qualityunit_Liveagent_Exception_Base $e) {
			throw new Qualityunit_Liveagent_Exception_ConnectFailed($e->getMessage());
		}
		Mage::log("Connect response received: " . print_r($response, true), Zend_log::DEBUG);
		Mage::log("Response OK", Zend_log::DEBUG);
	}

	private function getLogoURL() {
		return str_replace(array('http:','https:'), '', $this->getLiveAgentUrl()) . 'themes/install/_common_templates/img/default-contactwidget-logo.png';
	}

	public function getDefaultWidgetParams() {
		$attributes = array(
		array('section' => 'chat','name' => 'chat_action','value' => 'C'),
		array('section' => 'chat','name' => 'chat_action','value' => 'C'),
		array('section' => 'chat','name' => 'chat_agentmessage_bg_color','value' => 'FFFFFF'),
		array('section' => 'chat','name' => 'chat_agentmessage_color','value' => '000000'),
		array('section' => 'chat','name' => 'chat_border_bg_color','value' => '000000'),
		array('section' => 'chat','name' => 'chat_border_color','value' => 'FFFFFF'),
		array('section' => 'chat','name' => 'chat_button_bg_color','value' => '476B77'),
		array('section' => 'chat','name' => 'chat_button_color','value' => 'FFFFFF'),
		array('section' => 'chat','name' => 'chat_custom_css','value' => ''),
		array('section' => 'chat','name' => 'chat_design','value' => 'musho'),
		array('section' => 'chat','name' => 'chat_not_available_action','value' => 'F'),
		array('section' => 'chat','name' => 'chat_status_bg_color','value' => '98A8AD'),
		array('section' => 'chat','name' => 'chat_status_color','value' => '000000'),
		array('section' => 'chat','name' => 'chat_title','value' => 'Welcome'),
		array('section' => 'chat','name' => 'chat_type','value' => 'E'),
		array('section' => 'chat','name' => 'chat_visitormessage_bg_color','value' => 'FFFFFF'),
		array('section' => 'chat','name' => 'chat_visitormessage_color','value' => '909DA2'),
		array('section' => 'chat','name' => 'chat_welcome_message','value' => ''),
		array('section' => 'chat','name' => 'chat_window_height','value' => '450'),
		array('section' => 'chat','name' => 'chat_window_width','value' => '350'),
		array('section' => 'chat','name' => 'embedded_position','value' => 'BR'),
		array('section' => 'chat','name' => 'leaving_mess_status','value' => 'Y'),
		array('section' => 'chat','name' => 'window_position','value' => 'C'),
		array('section' => 'contactForm', 'name' => 'form_form_color', 'value' => '464646' ),
		array('section' => 'contactForm', 'name' => 'form_border_bg_color', 'value' => 'F6F6F6'),
		array('section' => 'contactForm', 'name' => 'kb_suggestions_kb_id', 'value' => 'kb_defa'),
		array('section' => 'contactForm', 'name' => 'form_content_bg_color', 'value' => 'F6F6F6'),
		array('section' => 'contactForm', 'name' => 'form_status_color', 'value' => '000000'),
		array('section' => 'contactForm', 'name' => 'form_custom_css', 'value' => ''),
		array('section' => 'contactForm', 'name' => 'form_logourl', 'value' => $this->getLogoURL()),
		array('section' => 'contactForm', 'name' => 'form_department_choose', 'value' => 'N'),
		array('section' => 'contactForm', 'name' => 'kb_suggestions_parent_entry_id', 'value' => '0'),
		array('section' => 'contactForm', 'name' => 'kb_suggestions_treepath', 'value' => '0'),
		array('section' => 'contactForm', 'name' => 'form_border_color', 'value' => '464646'),
		array('section' => 'contactForm', 'name' => 'form_confirm_message', 'value' => 'Thanks for your question. We\'ll send you an answer via email to {$conversationOwnerEmail}'),
		array('section' => 'contactForm', 'name' => 'contact_form_design', 'value' => 'modern'),
		array('section' => 'contactForm', 'name' => 'form_form_bg_color', 'value' => 'F6F6F6'),
		array('section' => 'contactForm', 'name' => 'show_kb_suggestions', 'value' => 'N'),
		array('section' => 'contactForm', 'name' => 'form_status_bg_color', 'value' => 'EAEAEA'),
		array('section' => 'contactForm', 'name' => 'form_button_bg_color', 'value' => 'FF7C22'),
		array('section' => 'contactForm', 'name' => 'form_title', 'value' => 'Welcome'),
		array('section' => 'contactForm', 'name' => 'form_content_color', 'value' => '464646'),
		array('section' => 'contactForm', 'name' => 'form_button_color', 'value' => 'FFFFFF'),
		array('section' => 'contactForm', 'name' => 'form_window_height','value' => '490'),
		array('section' => 'contactForm', 'name' => 'form_window_width','value' => '500'),
		array('section' => 'button','name' => 'button_type','value' => '7'),
		array('section' => 'button','name' => 'offline_button_bg_color','value' => '7E7E7E'),
		array('section' => 'button','name' => 'offline_button_border_color','value' => '000000'),
		array('section' => 'button','name' => 'offline_button_color','value' => 'FFFFFF'),
		array('section' => 'button','name' => 'offline_button_inner_border_color','value' => 'FFFFFF'),
		array('section' => 'button','name' => 'offline_button_position','value' => '25'),
		array('section' => 'button','name' => 'offline_button_side','value' => 'L'),
		array('section' => 'button','name' => 'offline_button_text','value' => 'Contact Us'),
		array('section' => 'button','name' => 'online_button_bg_color','value' => '191919'),
		array('section' => 'button','name' => 'online_button_border_color','value' => '000000'),
		array('section' => 'button','name' => 'online_button_color','value' => 'FFFFFF'),
		array('section' => 'button','name' => 'online_button_inner_border_color','value' => '6BDC00'),
		array('section' => 'button','name' => 'online_button_position','value' => '25'),
		array('section' => 'button','name' => 'online_button_side','value' => 'L'),
		array('section' => 'button','name' => 'online_button_text','value' => 'Live Chat'),
		array('section' => 'contactForm','name' => 'online_contact_form_design','value' => 'modern'),
		array('section' => 'contactForm','name' => 'online_form_border_bg_color','value' => 'F6F6F6'),
		array('section' => 'contactForm','name' => 'online_form_border_color','value' => '464646'),
		array('section' => 'contactForm','name' => 'online_form_button_bg_color','value' => 'FF7C22'),
		array('section' => 'contactForm','name' => 'online_form_button_color','value' => 'FFFFFF'),
		array('section' => 'contactForm','name' => 'online_form_confirm_message','value' => 'Please stand by, you will be redirected to operator shortly...'),
		array('section' => 'contactForm','name' => 'online_form_content_bg_color','value' => 'F6F6F6'),
		array('section' => 'contactForm','name' => 'online_form_content_color','value' => '464646'),
		array('section' => 'contactForm','name' => 'online_form_custom_css','value' => ''),
		array('section' => 'contactForm','name' => 'online_form_department_choose','value' => 'N'),
		array('section' => 'contactForm','name' => 'online_form_form_bg_color','value' => 'F6F6F6'),
		array('section' => 'contactForm','name' => 'online_form_form_color','value' => '464646'),
		array('section' => 'contactForm','name' => 'online_form_logourl','value' => $this->getLogoURL()),
		array('section' => 'contactForm','name' => 'online_form_status_bg_color','value' => 'EAEAEA'),
		array('section' => 'contactForm','name' => 'online_form_status_color','value' => '000000'),
		array('section' => 'contactForm','name' => 'online_form_title','value' => 'Welcome'),
		array('section' => 'contactForm','name' => 'online_form_window_height','value' => '490'),
		array('section' => 'contactForm','name' => 'online_form_window_width','value' => '500'),
		array('section' => 'contactForm','name' => 'online_kb_suggestions_kb_id','value' => 'kb_defa'),
		array('section' => 'contactForm','name' => 'online_kb_suggestions_parent_entry_id','value' => '0'),
		array('section' => 'contactForm','name' => 'online_kb_suggestions_treepath','value' => '0'),
		array('section' => 'contactForm','name' => 'online_show_kb_suggestions','value' => 'N'));

		$formFields = array(
			array('formid' => 'contactForm', 'name' => 'Name', 'rstatus' => 'M', 'code' => 'name', 'rtype' => 'T', 'availablevalues' => ''),
			array('formid' => 'contactForm', 'name' => 'Email', 'rstatus' => 'M', 'code' => 'email', 'rtype' => 'T', 'availablevalues' => ''),
			array('formid' => 'contactForm', 'name' => 'Message', 'rstatus' => 'M','code' => 'message', 'rtype' => 'M', 'availablevalues' => ''),
			array('formid' => 'chat', 'name' => 'Name', 'rstatus' => 'M', 'code' => 'name', 'rtype' => 'T', 'availablevalues' => ''),
			array('formid' => 'chat', 'name' => 'Email', 'rstatus' => 'M', 'code' => 'email', 'rtype' => 'T', 'availablevalues' => ''),
			array('formid' => 'chat', 'name' => 'Message', 'rstatus' => 'M', 'code' => 'message', 'rtype' => 'M', 'availablevalues' => '')
		);

		return array(
				'name' => 'Default button',
				'provide' => 'BFC',
				'departmentid' => 'default',
				'rtype' => 'C',
				'usecode' => 'B',
				'status' => 'A',
				'apikey' => $this->getApiKey(),
				'language' => 'en-US',
				'onlinecode' => '<div style="left: 0px;top: 25%; margin-top: -0px;z-index: 999997; position: fixed;"><!-- HorizontalBorderedTextButton --><div style="position:static; transform:rotate(-90deg); -ms-transform:rotate(-90deg); -webkit-transform: rotate(-90deg);-moz-transform: rotate(-90deg);-o-transform: rotate(-90deg); transform-origin: 0% 100%; -moz-transform-origin: 0% 0%; -webkit-transform-origin: 0% 0%; -o-transform-origin: 0% 0%;"><div style="cursor:pointer; box-shadow:0 0 3px #111111; -moz-box-shadow:0 0 3px #111111; -webkit-box-shadow:0 0 3px #111111; border-radius:0 0 5px 5px; -moz-border-radius:0 0 5px 5px; -webkit-border-radius:0 0 5px 5px; vertical-align:top; white-space:nowrap; margin-bottom:15px; font-size:20px; font-weight:bold; font-family:Arial,Verdana,Helvetica,sans-serif; cursor:pointer; padding:0px 3px 3px; position:absolute; top:0; right:0; border-top:0 !important; background-color:#6BDC00; border:1px solid #000000;"><div style="box-sizing:content-box; -moz-box-sizing:content-box; -webkit-box-sizing:content-box; -ms-box-sizing:content-box; font-size:20px; font-weight:bold; font-family:Arial,Verdana,Helvetica,sans-serif; position:static; line-height:18px; height:18px; padding:8px 23px; background-color:#191919; color:#FFFFFF;">Live Chat</div></div></div></div>',
				'onlinecode_ieold' => '<div style="left: 0px;top: 25%; margin-top: -0px;z-index: 999997; position: fixed;"><!-- HorizontalBorderedTextButton --><div style="position:static; filter:progid:DXImageTransform.Microsoft.BasicImage(rotation=3); transform-origin: 0% 100%; "><div style="cursor:pointer; box-shadow:0 0 3px #111111; border-radius:0 0 5px 5px; vertical-align:top; white-space:nowrap; margin-bottom:15px; font-size:20px; font-weight:bold; font-family:Arial,Verdana,Helvetica,sans-serif; cursor:pointer; padding:0px 3px 3px; *padding:3px 3px 3px 0; position:absolute; top:0; right:0; position:relative; filter:progid:DXImageTransform.Microsoft.BasicImage(rotation=3); border-top:0 !important; border-radius:0px; background-color:#6BDC00; border:1px solid #000000;"><div style="box-sizing:content-box; -moz-box-sizing:content-box; -webkit-box-sizing:content-box; -ms-box-sizing:content-box; font-size:20px; font-weight:bold; font-family:Arial,Verdana,Helvetica,sans-serif; position:static; line-height:18px; height:18px; padding:8px 23px; *filter:progid:DXImageTransform.Microsoft.BasicImage(rotation=3); background-color:#191919; color:#FFFFFF;">Live Chat</div></div></div></div>',
				'offlinecode' => '<div style="left: 0px;top: 25%; margin-top: -0px;z-index: 999997; position: fixed;"><!-- HorizontalBorderedTextButton --><div style="position:static; transform:rotate(-90deg); -ms-transform:rotate(-90deg); -webkit-transform: rotate(-90deg);-moz-transform: rotate(-90deg);-o-transform: rotate(-90deg); transform-origin: 0% 100%; -moz-transform-origin: 0% 0%; -webkit-transform-origin: 0% 0%; -o-transform-origin: 0% 0%;"><div style="cursor:pointer; box-shadow:0 0 3px #111111; -moz-box-shadow:0 0 3px #111111; -webkit-box-shadow:0 0 3px #111111; border-radius:0 0 5px 5px; -moz-border-radius:0 0 5px 5px; -webkit-border-radius:0 0 5px 5px; vertical-align:top; white-space:nowrap; margin-bottom:15px; font-size:20px; font-weight:bold; font-family:Arial,Verdana,Helvetica,sans-serif; cursor:pointer; padding:0px 3px 3px; position:absolute; top:0; right:0; border-top:0 !important; background-color:#FFFFFF; border:1px solid #000000;"><div style="box-sizing:content-box; -moz-box-sizing:content-box; -webkit-box-sizing:content-box; -ms-box-sizing:content-box; font-size:20px; font-weight:bold; font-family:Arial,Verdana,Helvetica,sans-serif; position:static; line-height:18px; height:18px; padding:8px 23px; background-color:#7E7E7E; color:#FFFFFF;">Contact Us</div></div></div></div>',
				'offlinecode_ieold' => '<div style="left: 0px;top: 25%; margin-top: -0px;z-index: 999997; position: fixed;"><!-- HorizontalBorderedTextButton --><div style="position:static; filter:progid:DXImageTransform.Microsoft.BasicImage(rotation=3); transform-origin: 0% 100%; "><div style="cursor:pointer; box-shadow:0 0 3px #111111; border-radius:0 0 5px 5px; vertical-align:top; white-space:nowrap; margin-bottom:15px; font-size:20px; font-weight:bold; font-family:Arial,Verdana,Helvetica,sans-serif; cursor:pointer; padding:0px 3px 3px; *padding:3px 3px 3px 0; position:absolute; top:0; right:0; position:relative; filter:progid:DXImageTransform.Microsoft.BasicImage(rotation=3); border-top:0 !important; border-radius:0px; background-color:#FFFFFF; border:1px solid #000000;"><div style="box-sizing:content-box; -moz-box-sizing:content-box; -webkit-box-sizing:content-box; -ms-box-sizing:content-box; font-size:20px; font-weight:bold; font-family:Arial,Verdana,Helvetica,sans-serif; position:static; line-height:18px; height:18px; padding:8px 23px; *filter:progid:DXImageTransform.Microsoft.BasicImage(rotation=3); background-color:#7E7E7E; color:#FFFFFF;">Contact Us</div></div></div></div>',
				'attributes' => json_encode($attributes),
				'form_fields' => json_encode($formFields)
		);
	}

    public function getSavedButtonCode() {
        $config = Mage::getSingleton('liveagent/config');
        if (!$config->isButtonEnabled()) {
            return '<!-- LiveAgent: la_error - button display is turned off for this store -->';
        }
    	if ($this->getOption(Qualityunit_Liveagent_Helper_Settings::BUTTON_CODE) != '') {
    		return $this->replacePlaceholders($this->getOption(Qualityunit_Liveagent_Helper_Settings::BUTTON_CODE));
    	}
    	return '<!-- LiveAgent: la_error - no button code set yet -->';
    }

	public function replacePlaceholders($htmlCode) {
		$customerSession = Mage::getSingleton('customer/session');
		if (!$customerSession->isLoggedIn()) {
			$htmlCode = str_replace('%%firstName%%', '', $htmlCode);
			$htmlCode = str_replace('%%lastName%%', '', $htmlCode);
			$htmlCode = str_replace('%%email%%', '', $htmlCode);
			$htmlCode = str_replace('%%phone%%', '', $htmlCode);
			$htmlCode = str_replace('%%order%%', '', $htmlCode);
			return $htmlCode;
		}

		$customer = $customerSession->getCustomer();

		if (($customer->getFirstname() != null) && ($customer->getFirstname() != '')) {
			$htmlCode = str_replace('%%firstName%%', "LiveAgent.addUserDetail('firstName', '" . $customer->getFirstname() . "');\n", $htmlCode);
		}
		else {
			$htmlCode = str_replace('%%firstName%%', '', $htmlCode);
		}

		if (($customer->getLastname() != null) && ($customer->getLastname() != '')) {
			$htmlCode = str_replace('%%lastName%%', "LiveAgent.addUserDetail('lastName', '" . $customer->getLastname() . "');\n", $htmlCode);
		}
		else {
			$htmlCode = str_replace('%%lastName%%', '', $htmlCode);
		}

		if (($customer->getEmail() != null) && ($customer->getEmail() != '')) {
			$htmlCode = str_replace('%%email%%', "LiveAgent.addUserDetail('email', '" . $customer->getEmail() . "');\n", $htmlCode);
		}
		else {
			$htmlCode = str_replace('%%email%%', '', $htmlCode);
		}

        if ($customer->getPrimaryBillingAddress() !== false) {
        	if (($customer->getPrimaryBillingAddress()->getTelephone() != null) && ($customer->getPrimaryBillingAddress()->getTelephone() != '')) {
        		$htmlCode = str_replace('%%phone%%', "LiveAgent.addUserDetail('phone', '" . $customer->getPrimaryBillingAddress()->getTelephone() . "');\n", $htmlCode);
        	}
        	else {
        		$htmlCode = str_replace('%%phone%%', '', $htmlCode);
        	}
        }
        else {
        		$htmlCode = str_replace('%%phone%%', '', $htmlCode);
        }
		return $htmlCode;
	}

	public function getSavedButtonId() {
		if ($this->getOption(Qualityunit_Liveagent_Helper_Settings::BUTTON_ID) != '') {
			return $this->getOption(Qualityunit_Liveagent_Helper_Settings::BUTTON_ID);
		}
		return '';
	}

	public function saveButtonCodeForButtonId($buttonid) {
		$url = $this->getLiveAgentUrl();
		$url = str_replace('https', '', $url);
		$url = str_replace('http', '', $url);
		if (substr($url, -1) != '/') {
			$url .= '/';
		}
		$this->setOption(self::BUTTON_CODE, "
				<script type=\"text/javascript\">
(function(d, src, c) { var t=d.scripts[d.scripts.length - 1],s=d.createElement('script');s.id='la_x2s6df8d';s.async=true;s.src=src;s.onload=s.onreadystatechange=function(){var rs=this.readyState;if(rs&&(rs!='complete')&&(rs!='loaded')){return;}c(this);};t.parentElement.insertBefore(s,t.nextSibling);})(document,
" . $url . "'scripts/track.js',
function(e){LiveAgent.createButton('" . $buttonid . "', e)});
</script>");
	}

	public function getOption($name) {
		$setting = Mage::getModel('liveagent/settings')->load($name, 'name');
		return $setting->getData('value');
	}

	public function getAccountStatus() {
		return $this->getOption(Qualityunit_Liveagent_Helper_Settings::ACCOUNT_STATUS);
	}
}