<?php

class Qualityunit_Liveagent_Helper_Data extends Mage_Core_Helper_Abstract {
	public static function convertOldButton() {
		$settings = new Qualityunit_Liveagent_Helper_Settings();
		$bannerCode = $settings->getSavedButtonCode();
		if ($bannerCode != '') {
			return;
		}
		$collection = Mage::getModel('liveagent/buttons')->getCollection();
		$buttonId = null;
		foreach ($collection as $button) {
			if ($buttonId === null && $button->getOnlinestatus() == 'Y' && $button->getContenttype() == 'F') {
				$buttonId = $button->getButtonid();
			}
			$button->delete();
		}
		if ($buttonId == null) {
			return;
		}
		$settings->saveButtonCodeForButtonId($buttonId);
	}
}