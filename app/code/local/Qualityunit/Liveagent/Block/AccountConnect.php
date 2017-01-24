<?php
/**
 *   @copyright Copyright (c) 2015 Quality Unit s.r.o.
 *   @author Martin Devecka, Martin Pullmann
 *   @package MgLiveAgentPlugin
 *   @version 1.0.0
 *
 *   Licensed under GPL2
 */
class Qualityunit_Liveagent_Block_AccountConnect extends Qualityunit_Liveagent_Block_Base {

	const CONNECT_ACCOUNT_ACTION_FLAG = 'ca';
	const SAVE_BUTTON_CODE_ACTION_FLAG = 'sb';
	const RESET_SETTINGS_ACTION_FLAG = 'rs';

	private $settings = null;

	public function _prepareLayout() {

	    $this->settings = new Qualityunit_Liveagent_Helper_Settings();
		parent::_prepareLayout();

		$this->assignVariable('dialogCaption', Mage::helper('adminhtml')->__('LiveAgent Connect - Free live chat and helpdesk plugin for Magento'));
		$this->assignVariable('connectCaption', Mage::helper('adminhtml')->__('Connect'));
		$this->assignVariable('settingsSection', Mage::helper('adminhtml')->__('Connect to your account'));
		$this->assignVariable('contactLink', Mage::helper('adminhtml')->__('contact us'));
		$this->assignVariable('contactHelp', Mage::helper('adminhtml')->__('Do you need any help with this plugin? Feel free to'));
		$this->assignVariable('formKey', Mage::getSingleton('core/session')->getFormKey());
		$this->assignVariable('saveUrlAction', $this->getUrl('*/*/post'));
		$this->assignVariable('connectUrlAction', $this->getUrl('*/*/index', array('key' => $this->getRequest()->get('key'), 'action' => self::CONNECT_ACCOUNT_ACTION_FLAG)));
		$this->assignVariable('urlLabel', Mage::helper('adminhtml')->__('Url') . ':');
		$this->assignVariable('urlHelp', Mage::helper('adminhtml')->__('Url where your LiveAgent installation is located'));
		$this->assignVariable('laOwnerEmailHelp', Mage::helper('adminhtml')->__('Username which you use to login to your Live Agent'));
		$this->assignVariable('nameLabel', Mage::helper('adminhtml')->__('Username') . ':');
		$this->assignVariable('sslSecure', Mage::helper('adminhtml')->__('SSL secure signup'));
		$this->assignVariable('accountUrl', Mage::helper('adminhtml')->__('URL') . ':');
		$this->assignVariable('accountEmail', Mage::helper('adminhtml')->__('Email') . ':');
		$this->assignVariable('accountAPIKey', Mage::helper('adminhtml')->__('API Key') . ':');

		$this->assignVariable(Qualityunit_Liveagent_Helper_Settings::LA_URL_SETTING_NAME, $this->settings->getLiveAgentUrl());
		$this->assignVariable(Qualityunit_Liveagent_Helper_Settings::LA_OWNER_EMAIL_SETTING_NAME, $this->settings->getOwnerEmail());
		$this->assignVariable(Qualityunit_Liveagent_Helper_Settings::LA_API_KEY, $this->settings->getApiKey());
		$this->assignVariable('saveActionSettingsFlag', self::CONNECT_ACCOUNT_ACTION_FLAG);
		$this->assignVariable('urlName', Qualityunit_Liveagent_Helper_Settings::LA_URL_SETTING_NAME);
		$this->assignVariable('apiKeyName', Qualityunit_Liveagent_Helper_Settings::LA_API_KEY);
		$this->assignVariable('ownerEmailName', Qualityunit_Liveagent_Helper_Settings::LA_OWNER_EMAIL_SETTING_NAME);
	}

	protected function getTemplateString(){
        return '<form id="configForm" name="edit_form" action="{saveUrlAction}" method="post">
	            <input name="form_key" type="hidden" value="{formKey}" />
	            <input name="action" type="hidden" value="{saveActionSettingsFlag}"/>
	            <div id="signup" class="nlHostedForm mainBox visible">
				<h1 style="margin-bottom: 0;">{settingsSection}</h1>
				<p class="secure">{sslSecure}</p>

				<div class="LaSignupForm">
					<div class="nameField">
						<div class="nameLabel">{accountUrl}</div>
						<div id="urlFieldmain" class="g-FormField2 FieldNotSet">
							<div id="urlFieldinputPanel" class="g-FormField2-InputPanel"></div>
							<div id="urlFieldTextContainer" class="TextBoxContainer TextBoxContainer-mandatory">
								<input id="urlFieldinnerWidget" class="Placeholdem TextBox TextBox-mandatory" name="{urlName}" value="{la-url}" placeholder="https://account.ladesk.com" autocomplete="off" type="text">
							</div>
							<div id="urlFielddescription" class="g-FormField2-Description"></div>
							<div id="urlFieldmessage" class="g-FormField2-Message"></div>
							<div class="clear"></div>
						</div>
					</div>
					<div class="mailField">
						<div class="emailLabel">{accountEmail}<span></span></div>
						<div id="mailFieldmain1" class="g-FormField2 FieldNotSet">
							<div id="mailFieldinputPanel" class="g-FormField2-InputPanel"></div>
							<div id="mailFieldTextContainer" class="TextBoxContainer TextBoxContainer-mandatory">
								<input id="mailFieldinnerWidget" class="Placeholdem TextBox TextBox-mandatory" name="{ownerEmailName}" value="{la-owner-email}" placeholder="Your login information will be sent here" autocomplete="off" type="text">
							</div>
							<div id="mailFielddescription" class="g-FormField2-Description"></div>
							<div id="mailFieldmessage" class="g-FormField2-Message"></div>
							<div class="clear"></div>
						</div>
					</div>
					<div class="domainField">
						<div class="domainLabel">{accountAPIKey}<span></span></div>
						<div id="apiFieldmain" class="g-FormField2 FieldNotSet">
							<div id="domainFieldinputPanel" class="g-FormField2-InputPanel"></div>
							<div id="domainFieldTextContainer" class="TextBoxContainer TextBoxContainer-mandatory"><label class="domain" for="domainFieldinnerWidget"><label style="visibility: hidden;"></label><label></label></label>
								<input id="ApiFieldinnerWidget" maxlength="32" class="TextBox TextBox-mandatory" name="{apiKeyName}" value="{la-api-key}" autocomplete="off" type="text">
							</div>
							<div id="domainFielddescription" class="g-FormField2-Description"></div>
							<div id="domainFieldmessage" class="g-FormField2-Message"></div>
							<div class="clear"></div>
						</div>
					</div>
					<div id="connectButtonmain" class="ImLeButtonMain buttonBgColor buttonBorderColor createButton" tabindex="0">
						<span id="connectButtontextSpan" onclick="configForm.submit()" class="buttonText" >{connectCaption}</span>
						<div id="connectButtoniconDiv" class="buttonIcon"></div>
					</div>
					<div class="LaSignupFormDesc">{contactHelp}&nbsp;<a href="http://support.qualityunit.com/submit_ticket" target="_blank">{contactLink}</a>.</div>
				</div>
			</div>';
	}
}