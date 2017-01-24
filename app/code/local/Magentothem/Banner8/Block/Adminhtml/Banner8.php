<?php
/*------------------------------------------------------------------------
# Websites: http://www.magentothem.com/
-------------------------------------------------------------------------*/ 
class Magentothem_Banner8_Block_Adminhtml_Banner8 extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
    $this->_controller = 'adminhtml_banner8';
    $this->_blockGroup = 'banner8';
    $this->_headerText = Mage::helper('banner8')->__('Item Manager');
    $this->_addButtonLabel = Mage::helper('banner8')->__('Add Item');
    parent::__construct();
  }
}