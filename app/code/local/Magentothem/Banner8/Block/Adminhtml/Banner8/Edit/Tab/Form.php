<?php
/*------------------------------------------------------------------------
# Websites: http://www.magentothem.com/
			http://www.plazathemes.com/
-------------------------------------------------------------------------*/ 
class Magentothem_Banner8_Block_Adminhtml_Banner8_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
      $form = new Varien_Data_Form();
      $this->setForm($form);
      $fieldset = $form->addFieldset('banner8_form', array('legend'=>Mage::helper('banner8')->__('Item information')));
     
      $fieldset->addField('title', 'text', array(
          'label'     => Mage::helper('banner8')->__('Title'),
          'required'  => false,
          'name'      => 'title',
      ));
	  
	  $fieldset->addField('link', 'text', array(
          'label'     => Mage::helper('banner8')->__('Link'),
          'required'  => false,
          'name'      => 'link',
      ));

      $fieldset->addField('image', 'file', array(
          'label'     => Mage::helper('banner8')->__('Image'),
          'required'  => false,
          'name'      => 'image',
	  ));
	  
	  $fieldset->addField('order', 'text', array(
          'label'     => Mage::helper('banner8')->__('Order'),
          'required'  => false,
          'name'      => 'order',
      ));


       
        if (!Mage::app()->isSingleStoreMode()) {
            $fieldset->addField('store_id', 'multiselect', array(
                'name' => 'store_id[]',
                'label' => $this->__('Store View'),
                'required' => TRUE,
                'values' => Mage::getSingleton('adminhtml/system_store')->getStoreValuesForForm(FALSE, TRUE),
            ));
        }
		
      $fieldset->addField('status', 'select', array(
          'label'     => Mage::helper('banner8')->__('Status'),
          'name'      => 'status',
          'values'    => array(
              array(
                  'value'     => 1,
                  'label'     => Mage::helper('banner8')->__('Enabled'),
              ),

              array(
                  'value'     => 2,
                  'label'     => Mage::helper('banner8')->__('Disabled'),
              ),
          ),
      ));
     
      $fieldset->addField('description', 'editor', array(
          'name'      => 'description',
          'label'     => Mage::helper('banner8')->__('Description'),
          'title'     => Mage::helper('banner8')->__('Description'),
          'style'     => 'width:700px; height:500px;',
          'wysiwyg'   => false,
          'required'  => false,
      ));
     
      if ( Mage::getSingleton('adminhtml/session')->getBanner8Data() )
      {
          $form->setValues(Mage::getSingleton('adminhtml/session')->getBanner8Data());
          Mage::getSingleton('adminhtml/session')->setBanner8Data(null);
      } elseif ( Mage::registry('banner8_data') ) {
          $form->setValues(Mage::registry('banner8_data')->getData());
      }
      return parent::_prepareForm();
  }
}