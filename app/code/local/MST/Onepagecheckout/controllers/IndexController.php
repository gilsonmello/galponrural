<?php
class MST_Onepagecheckout_IndexController extends Mage_Checkout_Controller_Action
{
    public function getOnepagecheckout()
    {
        return Mage::getSingleton('onepagecheckout/data');
    }
    
    public function indexAction()
    {
        // clear verification results from prevous checkout
        $this->getOnepagecheckout()->getCheckout()->setShippingWasValidated(false);
		$this->getOnepagecheckout()->getCheckout()->setBillingWasValidated(false);
		$this->getOnepagecheckout()->getCheckout()->setBillingValidationResults(false);
		$this->getOnepagecheckout()->getCheckout()->setShippingValidationResults(false);
        if (!Mage::helper('onepagecheckout')->isOnepageCheckoutEnabled())
        {
            Mage::getSingleton('checkout/session')->addError(Mage::helper('onepagecheckout')->__('The one page checkout is disabled.'));
            $this->_redirect('checkout/cart');
            return;
        }
        $quote = $this->getOnepagecheckout()->getQuote();
        if (!$quote->hasItems() || $quote->getHasError()) {
            $this->_redirect('checkout/cart');
            return;
        }
        if (!$quote->validateMinimumAmount()) {
            $error = Mage::getStoreConfig('sales/minimum_order/error_message');
            Mage::getSingleton('checkout/session')->addError($error);
            $this->_redirect('checkout/cart');
            return;
        }
        $totalItemsInCart = Mage::helper('checkout/cart')->getItemsCount();
        $validate_module = Mage::getStoreConfig('onepagecheckout/general/enabled', Mage::app()->getStore()->getId());
        if($totalItemsInCart == 0 || !$validate_module){
             $this->_redirectUrl(Mage::getBaseUrl().'checkout/cart/');
        }
        Mage::getSingleton('checkout/session')->setCartWasUpdated(false);
        Mage::getSingleton('customer/session')->setBeforeAuthUrl(Mage::getUrl('*/*/*', array('_secure'=>true)));
        $this->getOnepagecheckout()->initDefaultData()->initCheckout();
        $this->loadLayout();
        $this->_initLayoutMessages('customer/session');
        $title	= Mage::getStoreConfig('onepagecheckout/general/title');
        $this->getLayout()->getBlock('head')->setTitle($title);
        $this->renderLayout();
    }    
}
?>