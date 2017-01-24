<?php

class Qualityunit_Liveagent_Block_Signup extends Qualityunit_Liveagent_Block_Base {

	const FULL_NAME_FIELD = 'la-full-name';

	public function _prepareLayout() {
		parent::_prepareLayout();
		$this->assignVariable(self::FULL_NAME_FIELD, $this->getOwnerName());
		$this->assignVariable(Qualityunit_Liveagent_Helper_Settings::LA_OWNER_EMAIL_SETTING_NAME, $this->getOwnerEmail());
		$this->assignVariable(Qualityunit_Liveagent_Helper_Settings::LA_URL_SETTING_NAME, $this->getdomainOnly());
		$this->assignVariable('skipUrlAction', $this->getUrl('*/*/index', array('key' => $this->getRequest()->get('key'), 'action' => Qualityunit_Liveagent_Block_AccountConnect::CONNECT_ACCOUNT_ACTION_FLAG)));
		$this->assignVariable('registerUrlAction', $this->getUrl('*/*/post'));
		$this->assignVariable('formKey', Mage::getSingleton('core/session')->getFormKey());

		$this->assignVariable('dialogCaption', Mage::helper('adminhtml')->__('Helpdesk & Live Chat Software. Get started') . '!');
		$this->assignVariable('sslSecure', Mage::helper('adminhtml')->__('SSL secure signup'));
		$this->assignVariable('submitCaption', Mage::helper('adminhtml')->__('Create LiveAgent account'));
		$this->assignVariable('fullNameLabel', Mage::helper('adminhtml')->__('Full name'));
		$this->assignVariable('emailLabel', Mage::helper('adminhtml')->__('Email'));
		$this->assignVariable('emailPlaceholder', Mage::helper('adminhtml')->__('Your login information will be sent here'));
		$this->assignVariable('accountNameLabel', Mage::helper('adminhtml')->__('Your account name'));
		$this->assignVariable('domainPlaceholder', Mage::helper('adminhtml')->__('.ladesk.com'));
		$this->assignVariable('IAgree', Mage::helper('adminhtml')->__('By creating an account I agree with the'));
		$this->assignVariable('TnC', Mage::helper('adminhtml')->__('Terms & Conditions'));
		$this->assignVariable('skipLink', Mage::helper('adminhtml')->__('Skip this step, I already have an account'));
		$this->assignVariable('buildingLA', Mage::helper('adminhtml')->__('Building Your LiveAgent'));
		$this->assignVariable('buildingLALong', Mage::helper('adminhtml')->__('Your account is being created. Your login information will be sent to your email address.'));
		$this->assignVariable('whatsNew', Mage::helper('adminhtml')->__('Meanwhile, you can check what\'s new on'));
		$this->assignVariable('loading', Mage::helper('adminhtml')->__('Loading'));
		$this->assignVariable('blog', Mage::helper('adminhtml')->__('Our blog'));
		$this->assignVariable('howItWorks', Mage::helper('adminhtml')->__('See how it works'));

		$this->assignVariable('settingsSection', Mage::helper('adminhtml')->__('Create a new account'));
		$this->assignVariable('pluginHelpText', Mage::helper('adminhtml')->__('We want you to enjoy the full functionality of LiveAgent with 14 days free account. It does not limit the number of agents and you have the option to activate the most of available features.'));
		$this->assignVariable('pluginHelpText2', Mage::helper('adminhtml')->__('Fully equipped helpdesk and live chat software. Get a competitive advantage with top-notch customer service.'));
	}

	private function getdomainOnly() {
		$fullDomain = @$_SERVER['SERVER_NAME'];

		while (preg_match('/^([A-Za-z0-9-_]+\.)+([A-Za-z]{2,6})$/', $fullDomain)) {
			$domainSuffix = preg_replace('/^([A-Za-z0-9-_]+\.)+([A-Za-z]{2,6})$/', '$2', $fullDomain);
			$fullDomain = str_replace('.' . $domainSuffix, '', $fullDomain);
		}
		$domain = str_replace('www.', '', $fullDomain);
		$domain = str_replace('.', '-', $domain);

		if (trim($domain) == 'localhost') {
			return '';
		}
		if (preg_match('/^[a-zA-Z0-9\-]+$/', $domain) === false) {
			return '';
		}
		return $domain;
	}

	private function getOwnerName() {
		try {
			$user = Mage::getSingleton('admin/session')->getUser();
			return $user->getFirstname() . ' ' . $user->getLastname();
		} catch (Exception $e) {
			Mage::log($e->getMessage(), Zend_Log::ERR);
			return '';
		}
	}

	private function getOwnerEmail() {
		try {
			$user = Mage::getSingleton('admin/session')->getUser();
			return $user->getEmail();
		} catch (Exception $e) {
			Mage::log($e->getMessage(), Zend_Log::ERR);
			return '';
		}
	}

	protected function getTemplateString() {
			return '<div id="signup" class="nlHostedForm mainBox">
				<h1>{dialogCaption}</h1>
				<p class="secure">{sslSecure}</p>
				<div class="LaSignupForm">
					<input id="variation" value="3513230f" type="hidden">
					<input id="plan" value="14-day trial" type="hidden">
					<input id="continue" value="{registerUrlAction}" type="hidden">
					<input id="form_key" type="hidden" value="{formKey}">
					<div class="nameField">
						<div class="nameLabel">{fullNameLabel}</div>
						<div id="nameFieldmain" class="g-FormField2 FieldNotSet">
							<div id="nameFieldinputPanel" class="g-FormField2-InputPanel"></div>
							<div id="nameFieldTextContainer" class="TextBoxContainer TextBoxContainer-mandatory">
								<input id="nameFieldinnerWidget" class="Placeholdem TextBox TextBox-mandatory" name="Full name" value="{la-full-name}" placeholder="{fullNameLabel}" autocomplete="off" type="text">
							</div>
							<div id="nameFielddescription" class="g-FormField2-Description"></div>
							<div id="nameFieldmessage" class="g-FormField2-Message"></div>
							<div class="clear"></div>
						</div>
					</div>
					<div class="mailField">
						<div class="emailLabel">{emailLabel} <span></span></div>
						<div id="mailFieldmain" class="g-FormField2 FieldNotSet">
							<div id="mailFieldinputPanel" class="g-FormField2-InputPanel"></div>
							<div id="mailFieldTextContainer" class="TextBoxContainer TextBoxContainer-mandatory">
								<input id="mailFieldinnerWidget" class="Placeholdem TextBox TextBox-mandatory" name="Email" value="{la-owner-email}" placeholder="{emailPlaceholder}" autocomplete="off" type="text">
							</div>
							<div id="mailFielddescription" class="g-FormField2-Description"></div>
							<div id="mailFieldmessage" class="g-FormField2-Message"></div>
							<div class="clear"></div>
						</div>
					</div>
					<div class="domainField">
						<div class="domainLabel">{accountNameLabel} <span></span></div>
						<div id="domainFieldmain" class="g-FormField2 FieldNotSet">
							<div id="domainFieldinputPanel" class="g-FormField2-InputPanel"></div>
							<div id="domainFieldTextContainer" class="TextBoxContainer TextBoxContainer-mandatory"><label class="domain" for="domainFieldinnerWidget"><label style="visibility: hidden;"></label><label></label></label>
								<input id="domainFieldinnerWidget" maxlength="30" data-ghost-text="{domainPlaceholder}" class="TextBox TextBox-mandatory" name="Domain" value="{la-url}" placeholder="accountname{domainPlaceholder}" autocomplete="off" type="text">
								<div class="gwt-Label DomainFieldPlaceHolder" style="display: none;">{domainPlaceholder}</div>
							</div>
							<div id="domainFielddescription" class="g-FormField2-Description"></div>
							<div id="domainFieldmessage" class="g-FormField2-Message"></div>
							<div class="clear"></div>
						</div>
					</div>
					<div id="createButtonmain" class="ImLeButtonMain buttonBgColor buttonBorderColor createButton" tabindex="0">
						<span id="createButtontextSpan" class="buttonText">{submitCaption}</span>
						<div id="createButtoniconDiv" class="buttonIcon"></div>
					</div>
					<div class="skipMain">
						<a href="#" onclick="skipCreate()" class="scalable">&gt; {skipLink}</a>
					</div>
					<div class="LaSignupFormDesc">
						<span align="center">
						{IAgree}
						<a href="//www.qualityunit.com/liveagent/pricing/hosted/terms-and-conditions" target="_blank">{TnC}</a>
						</span>
					</div>
				</div>
			</div>

			<div id="loader" class="nlHostedForm mainBox invisible">
				<h1>{buildingLA}</h1>
				<p>{buildingLALong}</p>
			<div class="loading-bar">
				<div class="progress-bar"><div class="progress-stripes">////////////////////////</div></div>
				</div>
				<div class="loading-info">
					<span class="percentage">0%</span> / <span class="loader-label">{loading}...</span>
				</div>
			</div>

			<div id="completed" class="nlHostedForm invisible">
				<h1>{buildingLA}</h1>
				<p>{buildingLALong}</p>
				<p>{whatsNew}</p>
				<a href="blog/" target="_self" class="ImLeButtonMain"><span>{blog}</span></a>
			</div>

			<div class="rightPanelVideo">
                <div>
                    <h2>{settingsSection}</h2>
                    <p>{pluginHelpText}</p>
                </div>
    			<div>
					<h2>{howItWorks}</h2>
					<p>{pluginHelpText2}</p>
    			</div>
				<iframe src="https://player.vimeo.com/video/34832602?title=0&byline=0&portrait=0" width="500" height="281" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>
			</div>

			<script type="text/javascript">
				var skipCreate = function() {
					setLocation(\'{skipUrlAction}\');
					return false;
				};
			</script>';
	}
}