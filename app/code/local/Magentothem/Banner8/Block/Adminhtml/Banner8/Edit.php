<?php
/*------------------------------------------------------------------------
# Websites: http://www.magentothem.com/
			http://www.plazathemes.com/
-------------------------------------------------------------------------*/ 
class Magentothem_Banner8_Block_Adminhtml_Banner8_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
                 
        $this->_objectId = 'id';
        $this->_blockGroup = 'banner8';
        $this->_controller = 'adminhtml_banner8';
        
        $this->_updateButton('save', 'label', Mage::helper('banner8')->__('Save Item'));
        $this->_updateButton('delete', 'label', Mage::helper('banner8')->__('Delete Item'));
		
        $this->_addButton('saveandcontinue', array(
            'label'     => Mage::helper('adminhtml')->__('Save And Continue Edit'),
            'onclick'   => 'saveAndContinueEdit()',
            'class'     => 'save',
        ), -100);

        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('banner8_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'banner8_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'banner8_content');
                }
            }

            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
    }

    public function getHeaderText()
    {
        if( Mage::registry('banner8_data') && Mage::registry('banner8_data')->getId() ) {
            return Mage::helper('banner8')->__("Edit Item '%s'", $this->htmlEscape(Mage::registry('banner8_data')->getTitle()));
        } else {
            return Mage::helper('banner8')->__('Add Item');
        }
    }
}