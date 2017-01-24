<?php
class MST_Onepagecheckout_Model_Registration extends Mage_Core_Model_Abstract
{
	public function toOptionArray()
    {
        return array(
            array('value'=>'1', 'label'=>Mage::helper('onepagecheckout')->__('Require registration/login')),
            array('value'=>'2', 'label'=>Mage::helper('onepagecheckout')->__('Disable registration/login')),
            array('value'=>'3', 'label'=>Mage::helper('onepagecheckout')->__('Allow guest and logged in users')),
        );
    } 
} ?>