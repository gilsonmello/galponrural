<?php
class MST_OnepageCheckout_Block_Frontend_Checkout_Link extends Mage_Core_Block_Template
{
    public function EnableOnepagecheckout()
    {
        return $this->helper('onepagecheckout')->isOnepageCheckoutEnabled();
    }

    public function checkEnable()
    {
        return Mage::getSingleton('checkout/session')->getQuote()->validateMinimumAmount();
    }

    public function getOnepageCheckoutUrl()
    {
    	$url	= $this->getUrl('onepagecheckout', array('_secure' => true));
        return $url;
    }
}
