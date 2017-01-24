<?php
/*
* Copyright (c) 2015 www.magebuzz.com 
*/
class Magebuzz_Improvedaddress_Block_Adminhtml_Region_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs {
  public function __construct() {
		parent::__construct();
		$this->setId('improvedaddress_tabs');
		$this->setDestElementId('edit_form');
		$this->setTitle(Mage::helper('improvedaddress')->__('Item Information'));
  }

  protected function _beforeToHtml() {
		$this->addTab('form_section', array(
			'label'     => Mage::helper('improvedaddress')->__('Region Information'),
			'title'     => Mage::helper('improvedaddress')->__('Region Information'),
			'content'   => $this->getLayout()->createBlock('improvedaddress/adminhtml_region_edit_tab_form')->toHtml(),
		));
	 
		return parent::_beforeToHtml();
  }
}