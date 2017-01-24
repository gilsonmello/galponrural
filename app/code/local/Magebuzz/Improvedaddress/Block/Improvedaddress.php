<?php
/*
* Copyright (c) 2015 www.magebuzz.com 
*/
class Magebuzz_Improvedaddress_Block_Improvedaddress extends Mage_Core_Block_Template {
	public function _prepareLayout() {
		return parent::_prepareLayout();
  }
    
	public function getImprovedaddress() { 
		if (!$this->hasData('improvedaddress')) {
			$this->setData('improvedaddress', Mage::registry('improvedaddress'));
		}
		return $this->getData('improvedaddress');		
	}
}