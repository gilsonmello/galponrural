<?php
class MST_Onepagecheckout_Helper_Url extends Mage_Checkout_Helper_Url
{
    public function getCheckoutUrl()
    {
   	   if(Mage::helper('onepagecheckout')->isOnepageCheckoutEnabled())
        	return $this->_getUrl('onepagecheckout', array('_secure'=>true));
        else
        	return parent::getCheckoutUrl();
    }
}
