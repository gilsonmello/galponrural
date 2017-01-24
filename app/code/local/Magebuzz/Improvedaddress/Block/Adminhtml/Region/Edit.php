<?php
/*
* Copyright (c) 2015 www.magebuzz.com 
*/
class Magebuzz_Improvedaddress_Block_Adminhtml_Region_Edit extends Mage_Adminhtml_Block_Widget_Form_Container {
	public function __construct() {
		parent::__construct();						 
		$this->_objectId = 'id';
		$this->_blockGroup = 'improvedaddress';
		$this->_controller = 'adminhtml_region';
		
		$this->_updateButton('save', 'label', Mage::helper('improvedaddress')->__('Save Item'));
		$this->_updateButton('delete', 'label', Mage::helper('improvedaddress')->__('Delete Item'));

		$this->_addButton('saveandcontinue', array(
			'label'     => Mage::helper('adminhtml')->__('Save And Continue Edit'),
			'onclick'   => 'saveAndContinueEdit()',
			'class'     => 'save',
		), -100);

		$this->_formScripts[] = "
			function toggleEditor() {
				if (tinyMCE.getInstanceById('improvedaddress_content') == null) {
					tinyMCE.execCommand('mceAddControl', false, 'improvedaddress_content');
				} else {
					tinyMCE.execCommand('mceRemoveControl', false, 'improvedaddress_content');
				}
			}

			function saveAndContinueEdit(){
				editForm.submit($('edit_form').action+'back/edit/');
			}
		";
	}

	public function getHeaderText() {
		if( Mage::registry('improvedaddress_data') && Mage::registry('improvedaddress_data')->getId() ) {
			return Mage::helper('improvedaddress')->__("Edit Item '%s'", $this->htmlEscape(Mage::registry('improvedaddress_data')->getTitle()));
		} else {
			return Mage::helper('improvedaddress')->__('Add Item');
		}
	}
}