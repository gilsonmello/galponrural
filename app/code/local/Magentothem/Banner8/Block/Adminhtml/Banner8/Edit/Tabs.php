<?php
/*------------------------------------------------------------------------
# Websites: http://www.magentothem.com
-------------------------------------------------------------------------*/ 
class Magentothem_Banner8_Block_Adminhtml_Banner8_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

  public function __construct()
  {
      parent::__construct();
      $this->setId('banner8_tabs');
      $this->setDestElementId('edit_form');
      $this->setTitle(Mage::helper('banner8')->__('Item Information'));
  }

  protected function _beforeToHtml()
  {
      $this->addTab('form_section', array(
          'label'     => Mage::helper('banner8')->__('Item Information'),
          'title'     => Mage::helper('banner8')->__('Item Information'),
          'content'   => $this->getLayout()->createBlock('banner8/adminhtml_banner8_edit_tab_form')->toHtml(),
      ));
     
      return parent::_beforeToHtml();
  }
}