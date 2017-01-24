<?php
/*
* Copyright (c) 2015 www.magebuzz.com 
*/
class Magebuzz_Improvedaddress_Block_Adminhtml_Region extends Mage_Adminhtml_Block_Widget_Grid_Container {
  public function __construct() {
    $this->_controller = 'adminhtml_region';
    $this->_blockGroup = 'improvedaddress';
    $this->_headerText = Mage::helper('improvedaddress')->__('Manage Region/States');
    $this->_addButtonLabel = Mage::helper('improvedaddress')->__('Add Item');
    parent::__construct();
  }
}