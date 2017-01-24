<?php
class MST_Onepagecheckout_Model_Terms extends Mage_Core_Model_Abstract
{
	public function toOptionArray()
    {
        return array(
            array('value'=>'1', 'label'=>Mage::helper('onepagecheckout')->__('Popup')),
            array('value'=>'2', 'label'=>Mage::helper('onepagecheckout')->__('Terms and conditions page')),
        );
    } 
} ?>