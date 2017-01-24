<?php
class MST_Onepagecheckout_Helper_Data extends Mage_Core_Helper_Abstract
{
    protected $_agree = null;

    public function isOnepageCheckoutEnabled()
    {
        return (bool)Mage::getStoreConfig('onepagecheckout/general/enabled');
    }
    
    public function isGuestCheckoutAllowed()
    {
        return Mage::getStoreConfig('onepagecheckout/general/guest_checkout');
    }

    public function isShippingAddressAllowed()
    {
    	return Mage::getStoreConfig('onepagecheckout/general/shipping_address');
    }
    
    public function isSubscribeNewAllowed()
    {
        if (!Mage::getStoreConfig('onepagecheckout/general/newsletter_checkbox'))
            return false;

        $cust_sess = Mage::getSingleton('customer/session');
        if (!$cust_sess->isLoggedIn() && !Mage::getStoreConfig('newsletter/subscription/allow_guest_subscribe'))
            return false;

		$subscribed	= $this->getIsSubscribed();
		if($subscribed)
			return false;
		else
			return true;
    }
    
    public function getIsSubscribed()
    {
        $cust_sess = Mage::getSingleton('customer/session');
        if (!$cust_sess->isLoggedIn())
            return false;

        return Mage::getModel('newsletter/subscriber')->getCollection()
            										->useOnlySubscribed()
            										->addStoreFilter(Mage::app()->getStore()->getId())
            										->addFieldToFilter('subscriber_email', $cust_sess->getCustomer()->getEmail())
            										->getAllIds();
    }
    
    public function getMagentoVersion()
    {
		$ver_info = Mage::getVersionInfo();
		$mag_version	= "{$ver_info['major']}.{$ver_info['minor']}.{$ver_info['revision']}.{$ver_info['patch']}";
		
		return $mag_version;
    }
    
    public function isAddressVerificationEnabled()
    {
    	return false;
    }

    //function set customer comment
    public function setCustomerCommentDelivery($observer)
    {
        try{
            $order = $observer->getEvent()->getOrder();
            $order_id = $order->getData('increment_id') ;
            $orderComment = $this->_getRequest()->getPost('suvery');
            if(trim($orderComment)!= 'other'){
                $orderComment = trim($orderComment);
            }else{
                $orderComment = trim($this->_getRequest()->getPost('suvery_other'));
            }
            //save suvery to database
            $resource = Mage::getSingleton('core/resource');
            $writeConnection = $resource->getConnection('core_write');
            $query = "INSERT INTO ".Mage::getConfig()->getTablePrefix()."onepagecheckout_suvery VALUES ('','".$order_id."','".$orderComment."','','')";
            $writeConnection->query($query);
            //save delivery to datbase
            $delivery_date = $this->_getRequest()->getPost('delivery_date');
            $delivery_time = $this->_getRequest()->getPost('delivery_time');
            $resource = Mage::getSingleton('core/resource');
            $writeConnection = $resource->getConnection('core_write');
            $query = "INSERT INTO ".Mage::getConfig()->getTablePrefix()."onepagecheckout_delivery VALUES ('','".$order_id."','".$delivery_date."','".$delivery_time."','','')";
            $writeConnection->query($query);
        }catch(exception $e){
            
        }
    }
    
    //function update shipping payment 
    public function getOnepage()
    {
        return Mage::getSingleton('checkout/type_onepage');
    }
    
    public function savePayment($data)
    {
        if (empty($data)) {
            return array('error' => -1, 'message' => Mage::helper('checkout')->__('Invalid data'));
        }
        if ($this->getOnepage()->getQuote()->isVirtual()) {
            $this->getOnepage()->getQuote()->getBillingAddress()->setPaymentMethod(isset($data['method']) ? $data['method'] : null);
        } else {
            $this->getOnepage()->getQuote()->getShippingAddress()->setPaymentMethod(isset($data['method']) ? $data['method'] : null);
        }

        $payment = $this->getOnepage()->getQuote()->getPayment();
        $payment->importData($data);

        $this->getOnepage()->getQuote()->save();

        return array();
    }

    public function saveShippingMethod($shippingMethod)
    {
        if (empty($shippingMethod)) {
            $res = array(
                'error' => -1,
                'message' => Mage::helper('checkout')->__('Invalid shipping method.')
            );
            return $res;
        }
        $rate = $this->getOnepage()->getQuote()->getShippingAddress()->getShippingRateByCode($shippingMethod);
        if (!$rate) {
            $res = array(
                'error' => -1,
                'message' => Mage::helper('checkout')->__('Invalid shipping method.')
            );
            return $res;
        }
        $this->getOnepage()->getQuote()->getShippingAddress()->setShippingMethod($shippingMethod);

        return array();
    }
    
    public function getActiveStoreMethods($store = null, $quote = null){

        $store = $quote ? $quote->getStoreId() : null;
        $methods = array();
        $methods = Mage::helper('payment')->getStoreMethods($store, $quote);

        foreach ($methods as $key => $method) {
                if ($this->_canUseMethod($method, $quote)) {
                    unset($methods[$key]);
                    $methods[$key] = $method->getCode();
                } else {
                    unset($methods[$key]);
                }
            }
        $this->methods = $methods;
        return $methods;

    }
    
    protected function _canUseMethod($method, $quote)
    {
        if (!$method->canUseForCountry($quote->getBillingAddress()->getCountry())) {
            return false;
        }

        if(method_exists($method, 'canUseForCurrency')){
            if (!$method->canUseForCurrency(Mage::app()->getStore()->getBaseCurrencyCode())) {
                return false;
            }
        }

        /**
         * Checking for min/max order total for assigned payment method
         */
        $total = $quote->getBaseGrandTotal();
        $minTotal = $method->getConfigData('min_order_total');
        $maxTotal = $method->getConfigData('max_order_total');

        if((!empty($minTotal) && ($total < $minTotal)) || (!empty($maxTotal) && ($total > $maxTotal))) {
            return false;
        }
        return true;
    }
    public function getDateFieldsOrder()
	{
		$text = trim(Mage::getStoreConfig('onepagecheckout/delivery/format'));
		$text = str_replace(
					array(',', 'd', 'm', 'y'),
					array('/', '%d', '%m', '%Y'),
					$text);
		return $text;
	}
	
	/* edit by David */
	function get_content_id($file,$id){
		$h1tags = preg_match_all("/(<div id=\"{$id}\">)(.*?)(<\/div>)/ismU",$file,$patterns);
		$res = array();
		array_push($res,$patterns[2]);
		array_push($res,count($patterns[2]));
		return $res;
	}
	
	function get_div($file,$id){
    $h1tags = preg_match_all("/(<div.*>)(\w.*)(<\/div>)/ismU",$file,$patterns);
    $res = array();
    array_push($res,$patterns[2]);
    array_push($res,count($patterns[2]));
    return $res;
	}
	
	function get_domain($url)   {   
		$dev = 'dev';
		if ( !preg_match("/^http/", $url) )
			$url = 'http://' . $url;
		if ( $url[strlen($url)-1] != '/' )
			$url .= '/';
		$pieces = parse_url($url);
		$domain = isset($pieces['host']) ? $pieces['host'] : ''; 
		if ( preg_match('/(?P<domain>[a-z0-9][a-z0-9\-]{1,63}\.[a-z\.]{2,6})$/i', $domain, $regs) ) { 
			$res = preg_replace('/^www\./', '', $regs['domain'] );
			return $res;
		}   
		return $dev;
	}
	/* end */
	
	
}