<?php

class Qualityunit_Liveagent_Block_Buttoncode extends Qualityunit_Liveagent_Block_Base {

	public function _prepareLayout() {
		$settings = new Qualityunit_Liveagent_Helper_Settings();
		parent::_prepareLayout();

		$this->assignVariable('dialogCaption', Mage::helper('adminhtml')->__('Set a chat button for your store'));
		$this->assignVariable('integrationSectionLabel', Mage::helper('adminhtml')->__('Actual button code'));
		$this->assignVariable('buttonCodeLabel', Mage::helper('adminhtml')->__('Button code'));
		$this->assignVariable('contactHelp', Mage::helper('adminhtml')->__('Do you need any help with this plugin? Feel free to'));
		$this->assignVariable('contactLink', Mage::helper('adminhtml')->__('contact us'));
		$this->assignVariable('accountSectionLabel', Mage::helper('adminhtml')->__('Your LiveAgent account'));
		$this->assignVariable('accountUrlLabel', Mage::helper('adminhtml')->__('Account URL'));
		$this->assignVariable('loginLabel', Mage::helper('adminhtml')->__('Login to your account'));
		$this->assignVariable('ChangeLabel', Mage::helper('adminhtml')->__('Connect to a different account'));
		$this->assignVariable('buttonCodeHelp', Mage::helper('adminhtml')->__('This is the chat button code which will be automatically placed to your Magento site'));
		$this->assignVariable('configOptionsHelp', Mage::helper('adminhtml')->__('If customer is logged in, you can automatically add these to chat.'));
		$this->assignVariable('configOptionsTitle', Mage::helper('adminhtml')->__('Additional options'));
		$this->assignVariable('customer', Mage::helper('adminhtml')->__('customer'));
		$this->assignVariable('name', Mage::helper('adminhtml')->__('name'));
		$this->assignVariable('email', Mage::helper('adminhtml')->__('email'));
		$this->assignVariable('phone', Mage::helper('adminhtml')->__('phone'));
		$this->assignVariable('widgetsSectionLabel', Mage::helper('adminhtml')->__('Your chat buttons'));
		$this->assignVariable('addMoreButtons', Mage::helper('adminhtml')->__('Add more buttons'));
		$this->assignVariable('LaSignupFormDesc', Mage::helper('adminhtml')->__('This will redirect you to your LiveAgent account'));
		$this->assignVariable('displayInAdminPanel', Mage::helper('adminhtml')->__('Display button in admin panel'));
		$this->assignVariable('saveWidgetCodeHelp', Mage::helper('adminhtml')->__('The default widget code has just changed. You have to save it to apply the changes to the site chat widget.'));
		$this->assignVariable('saveWidgetCode', Mage::helper('adminhtml')->__('Save widget code'));
		$this->assignVariable('resetText', Mage::helper('adminhtml')->__('Reset settings and start over'));

		$this->assignVariable('formKey', Mage::getSingleton('core/session')->getFormKey());
		$this->assignVariable('saveUrlAction', $this->getUrl('*/*/post'));

		$code = htmlentities($settings->getOption(Qualityunit_Liveagent_Helper_Settings::BUTTON_CODE));

		$this->assignVariable('buttonId', $settings->getSavedButtonId());
		$this->assignVariable('la-config-button-code', $this->getTextArea('la-config-button-code', $code, 10 ,200, ' textarea'));

		$this->assignVariable('saveButtonCodeFlag', Qualityunit_Liveagent_Block_AccountConnect::SAVE_BUTTON_CODE_ACTION_FLAG);
		$this->assignVariable('la-url', $settings->getLiveAgentUrl());
		$this->assignVariable('loginUrl', $this->getLoginUrl($settings));
		$this->assignVariable('ChangeUrl', $this->getUrl('*/*/index', array('key' => $this->getRequest()->get('key'), 'action' => Qualityunit_Liveagent_Block_AccountConnect::CONNECT_ACCOUNT_ACTION_FLAG)));
		$this->assignVariable('resetUrl', $this->getUrl('*/*/index', array('key' => $this->getRequest()->get('key'), 'action' => Qualityunit_Liveagent_Block_AccountConnect::RESET_SETTINGS_ACTION_FLAG)));

		$this->prepareWidgetsBox($settings);

		$this->assignVariable('inUse', Mage::helper('adminhtml')->__('In use'));
		$this->assignVariable('errorOccurred', Mage::helper('adminhtml')->__('An error occurred: '));
		$this->assignVariable('useThisButton', Mage::helper('adminhtml')->__('Use this button'));

		// display in admin
		if ($settings->getOption(Qualityunit_Liveagent_Helper_Settings::DISPLAY_IN_ADMIN) != '') {
			$this->assignVariable('displayInAdminChecked', ' checked');
		} else {
		 	$this->assignVariable('displayInAdminChecked', '');
		}

		// additional configs
		if ($settings->getOption(Qualityunit_Liveagent_Helper_Settings::ADDITIONAL_NAME) != '') {
			$this->assignVariable('configOptionNameChecked', ' checked');
		} else {
			$this->assignVariable('configOptionNameChecked', '');
		}
		if ($settings->getOption(Qualityunit_Liveagent_Helper_Settings::ADDITIONAL_EMAIL) != '') {
			$this->assignVariable('configOptionEmailChecked', ' checked');
		} else {
			$this->assignVariable('configOptionEmailChecked', '');
		}
		if ($settings->getOption(Qualityunit_Liveagent_Helper_Settings::ADDITIONAL_PHONE) != '') {
			$this->assignVariable('configOptionPhoneChecked', ' checked');
		} else {
			$this->assignVariable('configOptionPhoneChecked', '');
		}
	}

	private function prepareWidgetsBox(Qualityunit_Liveagent_Helper_Settings $settings) {
		try {
			$widgetsArray = $settings->getAllWidgets();
			$widgetsHTML = '';
			$saved = false;

			if (empty($widgetsArray)) {
			    $connectHelper = new Qualityunit_Liveagent_Helper_Connect();
			    $settings = new Qualityunit_Liveagent_Helper_Settings();
			    try {
			        $widgetsArray = $connectHelper->createWidget($settings->getLiveAgentUrl(), $settings->getDefaultWidgetParams());
			    } catch (Qualityunit_Liveagent_Exception_Base $e) {
			        $this->assignVariable('widgetsHTML', '<div class="widgetsContainer">{errorOccurred}' . $e->getMessage() . "</div>\n");
			    }
			}

			foreach ($widgetsArray as $widget) {
				$result = '<div class="widgetTitle">' . $widget->name . ':</div>';
				$result .= '<div class="widgetDisplay"><iframe frameborder="0" id="iFramePreview' . $widget->contactwidgetid . '" width="100%" height="100%" style="background-color: white"><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd"><html xmlns="http://www.w3.org/1999/xhtml"><head><title>' . $widget->name . '</title></head><body></body></html></iframe></div>';
				$result .= '<textarea id="iFrame' . $widget->contactwidgetid . '" class="widgetCodeInvisible">' . $widget->onlinecode . '</textarea>';
				$result .= '<textarea id="' . $widget->contactwidgetid . '" class="widgetCodeInvisible">' . htmlspecialchars_decode($widget->integration_code) . '</textarea>';
				$selected = '';
				$useButton = false;

				if (($settings->getSavedButtonCode() == null) || ($settings->getSavedButtonCode() == '')) {
					if (!$saved) {
						$settings->setOption(Qualityunit_Liveagent_Helper_Settings::BUTTON_CODE, htmlspecialchars_decode($widget->integration_code));
						$settings->setOption(Qualityunit_Liveagent_Helper_Settings::BUTTON_ID, $widget->contactwidgetid);
						$saved = true;
						$selected = ' selected';
					}
					else {
					   $useButton = true;
					}
				} else {
					if ($settings->getSavedButtonId() == $widget->contactwidgetid) {
						$selected = ' selected';
					}
					else {
						$useButton = true;
					}
				}

				if ($useButton) {
					$result .= '<div onclick="jQuery(function($) {$.fn.setButton(\'' . $widget->contactwidgetid . '\')})" class="widgetSetButton">{useThisButton}</div>';
				}
				else {
				    $result .= '<span class="inUseLabel">{inUse}</span>';
				}

				$widgetsHTML .= '<div class="widgetBox ' . $selected . '">' . $result . "</div>\n";
				$widgetsHTML .= '<script type="text/javascript">
					jQuery(function($) {
						$.fn.getIframePreviewCode("' . $widget->contactwidgetid . '");
					});
					</script>';
			}

			$this->assignVariable('widgetsHTML', '<div class="widgetsContainer">' . $widgetsHTML . '</div>');
		} catch (Qualityunit_Liveagent_Exception_ConnectFailed $e) {
			$this->assignVariable('widgetsHTML', '<div class="widgetsContainer">{errorOccurred}' . $e->getMessage() . "</div>\n");
		}
	}

	private function getLoginUrl(Qualityunit_Liveagent_Helper_Settings $settings) {
		return $settings->getLiveAgentUrl() . 'agent/index.php?AuthToken=' . $settings->getOwnerAuthToken();
	}

	protected function getTemplateString() {
		return '
		<form id="configForm" name="edit_form" action="{saveUrlAction}" method="post">
		<input name="form_key" type="hidden" value="{formKey}" />
		<input name="action" type="hidden" value="{saveButtonCodeFlag}"/>
		<div class="content-header">
			<table cellspacing="0">
				<tbody><tr>
					<td style="width:50%;"><h3 class="icon-head head-promo-catalog">{dialogCaption}</h3></td>
					<td class="form-buttons">{contactHelp}&nbsp;<a href="http://support.qualityunit.com/submit_ticket" target="_blank">{contactLink}</a>.</td>
					</tr>
				</tbody>
			</table>
		</div>
		<div class="entry-edit">
			<div class="entry-edit-head"><h4>{accountSectionLabel}</h4></div>
			<fieldset>
				<table cellspacing="0" class="form-list">
					<tbody>
						<tr id="row_la_url">
							<td class="label" style="vertical-align: middle"><label for="row_la_url">{accountUrlLabel}:</label></td>
							<td class="value" style="vertical-align: middle; text-align: center">
								<strong>{la-url}</strong><br />
								<a href="{ChangeUrl}">&gt;&gt;&gt; {ChangeLabel}</a>
							</td>
							<td class="scope-label">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
							<td class="value" style="text-align: center">
								<div id="connectButtonmain" class="ImLeButtonMain1 buttonBgColor buttonBorderColor createButton" tabindex="0">
									<span onclick="window.open(\'{loginUrl}\', \'_blank\')" class="buttonText">{loginLabel}</span>
								</div>
							</td>
							<td class="scope-label"></td><td></td>
						</tr>
					</tbody>
				</table>
		        <p id="resetLink">{resetText}?</p>
                <input type="hidden" id="resetUrl" value="{resetUrl}" />
			</fieldset>
		</div>
		<div class="entry-edit">
			<div class="entry-edit-head"><h4>{widgetsSectionLabel}</h4></div>
			<fieldset>
				{widgetsHTML}
				<div class="displayInAdminCheckbox">
					<input onclick="jQuery(function($) {$(\'#configForm\').submit()})" type="checkbox" id="displayInAdmin" value="1" name="displayInAdmin" {displayInAdminChecked}> <label for="la-config-button-options"><strong>{displayInAdminPanel}</strong></label>
		        </div>
				<div class="formFooter">
					<div id="connectButtonmain" class="ImLeButtonMain1 buttonBgColor buttonBorderColor createButton" tabindex="0">
						<span onclick="window.open(\'{loginUrl}\', \'_blank\')" class="buttonText">{addMoreButtons}</span>
					</div>
					<span class="LaSignupFormDesc1">{LaSignupFormDesc} {la-url}</span>
				</div>
			</fieldset>
		</div>
		<div class="entry-edit">
			<div class="entry-edit-head"><h4>{integrationSectionLabel}</h4></div>
			<fieldset>
				<table cellspacing="0" class="form-list" style="width: 100%">
					<tbody>
						<tr id="la-config-button-code">
							<td class="label"><label for="la-config-button-code">{buttonCodeLabel}:</label></td>
							<td class="value">{la-config-button-code}
							  <input type="hidden" value="{buttonId}" name="buttonId" id="buttonId">
							<p class="note"><span>{buttonCodeHelp}</span></p>
							</td>
							<td class="scope-label">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
							<td class="value configOptions">
							<label for="la-config-button-options"><strong>{configOptionsTitle}:</strong></label><br />
							<input type="checkbox" id="configOptionName" value="1" name="configOptionName" {configOptionNameChecked}> {customer} {name}<br />
							<input type="checkbox" id="configOptionEmail" value="2" name="configOptionEmail" {configOptionEmailChecked}> {customer} {email}<br />
							<input type="checkbox" id="configOptionPhone" value="3" name="configOptionPhone" {configOptionPhoneChecked}> {customer} {phone}<br />
							<p class="note" style="margin-top:1em"><span>{configOptionsHelp}</span></p>
							</td>
						</tr>
						<tr>
							<td colspan=4>
								<div class="formFooter SaveWidgetCode">
									<div id="connectButtonmain" class="ImLeButtonMain1 buttonBgColor buttonBorderColor createButton" tabindex="0">
										<span onclick="jQuery(function($) {$(\'#configForm\').submit()})" class="buttonText">{saveWidgetCode}</span>
									</div>
									<span class="LaSignupFormDesc1">{saveWidgetCodeHelp}</span>
								</div>
							</td>
						</tr>
					</tbody>
				</table>
			</fieldset>
		</div>
		</form>';
	}
}