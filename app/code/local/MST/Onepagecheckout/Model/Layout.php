<?php
class MST_Onepagecheckout_Model_Layout extends Mage_Core_Model_Abstract
{
	public function toOptionArray()
    {
        return array(
            array('value'=>'1', 'label'=>Mage::helper('onepagecheckout')->__('Default')),
            array('value'=>'0', 'label'=>Mage::helper('onepagecheckout')->__('Three Column Layout')),
        );
    } 
} ?>