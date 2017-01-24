<?php
class MST_Onepagecheckout_Block_System_Config_Form_Field_Suveryfields extends Mage_Adminhtml_Block_System_Config_Form_Field_Array_Abstract
{
    public function __construct()
    {
        $this->addColumn('value', array(
            'label' => Mage::helper('onepagecheckout')->__('Label'),
            'style' => 'width:250px',
        ));
        $this->_addAfter = false;
        $this->_addButtonLabel = Mage::helper('onepagecheckout')->__('Add label');
        parent::__construct();
    }
}