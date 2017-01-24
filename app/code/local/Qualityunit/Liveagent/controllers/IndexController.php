<?php
class Qualityunit_Liveagent_IndexController extends Mage_Core_Controller_Front_Action {
	public function indexAction() {
		Qualityunit_Liveagent_Helper_Data::convertOldButton();
		$this->loadLayout();
		$this->renderLayout();
	}
}