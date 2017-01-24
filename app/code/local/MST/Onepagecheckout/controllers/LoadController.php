<?php
class MST_Onepagecheckout_LoadController extends Mage_Checkout_Controller_Action
{
    private $_current_layout = null;

    public function getOnepagecheckout()
    {
        return Mage::getSingleton('onepagecheckout/data');
    }
    
    public function getOnepage()
    {
        return Mage::getSingleton('checkout/type_onepage');
    }   
     
    protected function _getCart()
    {
        return Mage::getSingleton('checkout/cart');
    }
            
    protected function _getQuote()
    {
        return $this->_getCart()->getQuote();
    }
            
    protected function _filterPostData($data)
    {
        $data = $this->_filterDates($data, array('dob'));
        return $data;
    }
        
    protected function _getStoreConfig($xmlPath)
    {
        return trim(Mage::getStoreConfig($xmlPath, Mage::app()->getStore()->getId()));
    }
    
	public function getConfigYesno()
    {
        return $this->_getStoreConfig(self::XML_PATH_ENABLED);
    }
    
    public function preDispatch()
    {
        parent::preDispatch();
        $this->_preDispatchValidateCustomer();
        return $this;
    }

    protected function _ajaxRedirectResponse()
    {
        $this->getResponse()
            ->setHeader('HTTP/1.1', '403 Session Expired')
            ->setHeader('Login-Required', 'true')
            ->sendResponse();
        return $this;
    }

    protected function _expireAjax()
    {
        if (!$this->getOnepagecheckout()->getQuote()->hasItems()
            || $this->getOnepagecheckout()->getQuote()->getHasError()
            || $this->getOnepagecheckout()->getQuote()->getIsMultiShipping()) {
            $this->_ajaxRedirectResponse();
            return true;
        }
        $action = $this->getRequest()->getActionName();
        if (Mage::getSingleton('checkout/session')->getCartWasUpdated(true)
            && !in_array($action, array('load', 'progress'))) {
            $this->_ajaxRedirectResponse();
            return true;
        }

        return false;
    }
    
    protected function _getUpdatedLayout()
    {
        $this->_initLayoutMessages('checkout/session');
        if ($this->_current_layout === null)
        {
            $layout = $this->getLayout();
            $update = $layout->getUpdate();            
            $update->load('onepagecheckout_load_updateall');
            
            $layout->generateXml();
            $layout->generateBlocks();
            $this->_current_layout = $layout;
        }
        
        return $this->_current_layout;
    }
    
    protected function _getShippingMethodsHtml()
    {
    	$layout	= $this->_getUpdatedLayout();
        return $layout->getBlock('checkout_shipping_method')->toHtml();
    }

    protected function _getPaymentMethodsHtml()
    {
    	$layout	= $this->_getUpdatedLayout();
        return $layout->getBlock('checkout_payment_method')->toHtml();
    }
    
    protected function _getInfoHtml()
    {
    	$layout	= $this->_getUpdatedLayout();
        return $layout->getBlock('review_info')->toHtml();
    }
    
    public function update_shipping_review_indexAction()
    {
        $this->getOnepage()->getQuote()->collectTotals()->save();
        $response['info'] = $this->_getInfoHtml();
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response));
    }
    
    public function update_shipping_paymentAction()
    {
        $helper = Mage::helper('onepagecheckout');
        $shipping_method = $this->getRequest()->getPost('shipping_method');
        $old_shipping_method = $this->getOnepage()->getQuote()->getShippingAddress()->getShippingMethod();

        if($shipping_method != '' && $shipping_method != $old_shipping_method)  {
            $helper->saveShippingMethod($shipping_method);
        }
        $paymentMethod = $this->getRequest()->getPost('payment_method', false);
        $selectedMethod = $this->getOnepage()->getQuote()->getPayment()->getMethod();
        $store = $this->getOnepage()->getQuote() ? $this->getOnepage()->getQuote()->getStoreId() : null;
        $methods = $helper->getActiveStoreMethods($store, $this->getOnepage()->getQuote());
        if($paymentMethod && !empty($methods) && !in_array($paymentMethod, $methods)){
            $paymentMethod = false;
        }
        if(!$paymentMethod && $selectedMethod && in_array($selectedMethod, $methods)){
             $paymentMethod = $selectedMethod;
        }
        try {
            $payment = $this->getRequest()->getPost('payment', array());
            if(!empty($paymentMethod)){
                $payment['method'] = $paymentMethod;
            }
            $helper->savePayment($payment);
        }
        catch(Exception $e) {
        }
        $this->getOnepage()->getQuote()->collectTotals()->save();
        $response['info'] = $this->_getInfoHtml();
        $response['shipping_method'] = $this->_getShippingMethodsHtml();
        $response['payment_method'] = $this->_getPaymentMethodsHtml();
        $response['validate_cart'] = Mage::helper('checkout/cart')->getItemsCount();
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response));
    }
        
    public function updateallAction(){
        if ($this->_expireAjax() || !$this->getRequest()->isPost())return;
        if($this->getRequest()->getPost())
        {
            //start save data            
    		$validation_enabled	= Mage::helper('onepagecheckout')->isAddressVerificationEnabled();
            $quote = $this->getOnepagecheckout()->getQuote();
            $bill_data = $this->getRequest()->getPost('billing', array());
            $bill_data = $this->_filterPostData($bill_data);
            $bill_addr_id = $this->getRequest()->getPost('billing[address_id]', false);
            $ship_updated = false;
            $pm_applied	= false;
            $billing_address_changed	= false;
            if($this->getOnepagecheckout()->_checkChangedAddress($bill_data, 'Billing', $bill_addr_id, $validation_enabled))
        	$billing_address_changed	= true;
            $shipping_address_changed	= false;
            if ($billing_address_changed || $this->getRequest()->getPost('payment-method', false))
            {
                if (isset($bill_data['email']))
                {
                    $bill_data['email'] = trim($bill_data['email']);
                }
                $bill_result = $this->getOnepagecheckout()->saveBilling($bill_data, $bill_addr_id, false);
                if (!isset($bill_result['error']))
                {
                    $pmnt_data = $this->getRequest()->getPost('payment', array());
                    $this->getOnepagecheckout()->usePayment(isset($pmnt_data['method']) ? $pmnt_data['method'] : null);
                    $pm_applied	= true;
                    if (isset($bill_data['use_for_shipping']) && $bill_data['use_for_shipping'] == 1 && !$this->getOnepagecheckout()->getQuote()->isVirtual())
    				{
                        $ship_updated = true;
                        if($billing_address_changed)
                       	$shipping_address_changed = true;
                    }
                }
            }
            if ($this->getRequest()->getPost('payment-changed', false))
            {
            	if(!$pm_applied)
            	{
    				$pmnt_data = $this->getRequest()->getPost('payment', array());
    				$this->getOnepagecheckout()->usePayment(isset($pmnt_data['method']) ? $pmnt_data['method'] : null);
            	}
            }
            $ship_data = $this->getRequest()->getPost('shipping', array());
            $ship_addr_id = $this->getRequest()->getPost('shipping_address_id', false);
            $ship_method	= $this->getRequest()->getPost('shipping_method', false);
            if (!$ship_updated && !$this->getOnepagecheckout()->getQuote()->isVirtual())
            {
            	$real_shipping_address_changed	= false;
            	// check if buttom 'same as billing' was checked
            	if (isset($bill_data['use_for_shipping']) && $bill_data['use_for_shipping'] == 1)
            	{
            		$this->getOnepagecheckout()->saveBilling($bill_data, $bill_addr_id, false);
            		$ship_updated	= true;	
            		$shipping_address_changed	= true;
            	}
            	else
            	{
    	        	if ($this->getOnepagecheckout()->_checkChangedAddress($ship_data, 'Shipping', $ship_addr_id, $validation_enabled))
    	        	{
    	        		$shipping_address_changed = true;
    	        		$real_shipping_address_changed	= true;
    	        	}
    	            if (($real_shipping_address_changed || $ship_method) && !$ship_updated) 
    	            {
    	                $ship_result = $this->getOnepagecheckout()->saveShipping($ship_data, $ship_addr_id, false);
    	            }
            	}
            }
            $rates = Mage::getModel('sales/quote_address_rate')->getCollection()->setAddressFilter($this->getOnepagecheckout()->getQuote()->getShippingAddress()->getId())->toArray();
            $this->getOnepagecheckout()->useShipping($ship_method);
            $this->getOnepagecheckout()->getQuote()->collectTotals()->save();
            //end update and save data
            $response['info'] = $this->_getInfoHtml();
            $response['shipping_method'] = $this->_getShippingMethodsHtml();
            $response['payment_method'] = $this->_getPaymentMethodsHtml();
            $response['validate_cart'] = Mage::helper('checkout/cart')->getItemsCount();
            $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response));
        }
    }
            
    //function update product in cart    
    public function updateqtyproductcartAction()
    {
        if($this->getRequest()->getPost())
        {
            $id_product = $this->getRequest()->getPost('id_product');
            $product_id = $this->getRequest()->getPost('product_id');
            $action = $this->getRequest()->getPost('action');
            $qty = $this->getRequest()->getPost('qty');
            $cart = Mage::getSingleton('checkout/cart');
            switch($action){
                case 1:
                    $qty = $qty-1;
                break;
                case 2:
                    $qty = $qty+1;
                break;
                default:
                    $qty = $qty;
                break;
            }
            $cart = Mage::getSingleton('checkout/cart');
            $items = $cart->getItems(); 
            foreach ($items as $item){
                if($id_product == $item->getId()){
                    if($qty == 0){
                        $cart->getQuote()->removeItem($id_product)->save();
                        if($cart->save())
                        {
                            $response['result'] = 'true';  
                        }else{
                            $response['result'] = 'false';
                        }
                    }else{
                        if(Mage::getModel('cataloginventory/stock_item')->loadByProduct($product_id)->getManageStock() == 1)
                        {
                            if((int)Mage::getModel('cataloginventory/stock_item')->loadByProduct($product_id)->getQty()>=$qty){
                                $item->setQty($qty); 
                                if($cart->save())
                                {
                                    $response['result'] = 'false';  
                                }
                            }else{
                                $response['message'] = 'The requested quantity for "'.$item->getProduct()->getName().'" is not available.
The maximum quantity allowed for "'.$item->getProduct()->getName().'" is '.(int)Mage::getModel('cataloginventory/stock_item')->loadByProduct($product_id)->getQty().'.';
                            }
                        }else{
                            $item->setQty($qty); 
                            if($cart->save())
                            {
                                $response['result'] = 'false';  
                            }
                        } 
                    }
                }
            }   
            $response['info'] = $this->_getInfoHtml();
            $response['shipping_method'] = $this->_getShippingMethodsHtml();
            $response['payment_method'] = $this->_getPaymentMethodsHtml();
            $response['validate_cart'] = Mage::helper('checkout/cart')->getItemsCount();
            $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response));
        }
    }
            
    //function delete product in cart        
    public function deleteproductcartAction()
    {
        if($this->getRequest()->getPost())
        {
            $id_product = $this->getRequest()->getPost('id_product');
            Mage::getSingleton('checkout/cart')->getQuote()->removeItem($id_product)->save();
            if(Mage::getModel('checkout/cart')->save())
             {
                $response['result'] = 'true';  
             }else{
                $response['result'] = 'false';
             }
        }
        $response['info'] = $this->_getInfoHtml();
        $response['shipping_method'] = $this->_getShippingMethodsHtml();
        $response['payment_method'] = $this->_getPaymentMethodsHtml();
        $response['validate_cart'] = Mage::helper('checkout/cart')->getItemsCount();
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response));
    }
            
    //function update coupon
    public function customcouponPostAction()
    { 
    	$response = array();
    	$response['status'] = 'ERROR';
    	$couponCode = (string) $this->getRequest()->getParam('coupon_code');
    	if ($this->getRequest()->getParam('remove') == 1) {
    		$couponCode = '';
    	}
    	$oldCouponCode = $this->_getQuote()->getCouponCode();
    	if (!strlen($couponCode) && !strlen($oldCouponCode)) {
    		return;
    	}
    	try {
    		$this->_getQuote()->getShippingAddress()->setCollectShippingRates(true);
    		$this->_getQuote()->setCouponCode(strlen($couponCode) ? $couponCode : '')
    		->collectTotals()
    		->save();
    		if (strlen($couponCode)) {
    			if ($couponCode == $this->_getQuote()->getCouponCode()) {
                    $response['msg'] =  $this->__('Coupon code '.Mage::helper('core')->htmlEscape($couponCode).' was applied');
                    $response['status'] = 'SUCCESS';	
    			}
    			else {
    				$response['msg'] = $this->__('Coupon code '.Mage::helper('core')->htmlEscape($couponCode).' is not valid.');
    				$response['status'] = 'ERROR';
    			}
    		} else {
                $response['msg'] = $this->__('Coupon code was canceled.');
    			$response['status'] = 'SUCCESS';
    		}
    
    	} catch (Mage_Core_Exception $e) {
    	} catch (Exception $e) {
    		$response['msg'] = $this->__('Cannot apply the coupon code.');
    	}
        $response['info'] = $this->_getInfoHtml();
        $response['shipping_method'] = $this->_getShippingMethodsHtml();
        $response['payment_method'] = $this->_getPaymentMethodsHtml();
        $response['validate_cart'] = Mage::helper('checkout/cart')->getItemsCount();
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response));
    }
    
    public function loginAction()
    {
        $session = Mage::getSingleton('customer/session');
        $result = array('success' => false);
        if ($this->getRequest()->isPost())
        {
            $login_data = $this->getRequest()->getPost('login');
            if (empty($login_data['username']) || empty($login_data['password'])) {
            	$result['error'] = Mage::helper('onepagecheckout')->__('Login and password are required.');
            }
            else
            {
				try
				{
                    $session->login($login_data['username'], $login_data['password']);
                    $result['success'] = true;
                    $result['redirect'] = Mage::getUrl('*/*/index', array('_secure'=>true));
                }
                catch (Mage_Core_Exception $e)
                {
                    switch ($e->getCode()) {
                        case Mage_Customer_Model_Customer::EXCEPTION_EMAIL_NOT_CONFIRMED:
                            $message = Mage::helper('onepagecheckout')->__('Email is not confirmed. <a href="%s">Resend confirmation email.</a>', Mage::helper('customer')->getEmailConfirmationUrl($login_data['username']));
                            break;
                        default:
                            $message = $e->getMessage();
                    }
                    $result['error'] = $message;
                    $session->setUsername($login_data['username']);
                }
            }
        }
        Mage::getModel('customer/session')->setData('message',$result['error']);
        $this->_redirectUrl(Mage::getBaseUrl().'onepagecheckout');
    }
    
    public function forgotpasswordAction()
    {
        $session = Mage::getSingleton('customer/session');
        $email = $this->getRequest()->getPost('email');
        $result = array('success' => false);
        if (!$email)
        {
			$result['error'] = Mage::helper('onepagecheckout')->__('Please enter your email.');
        }
        else
        {
			if (!Zend_Validate::is($email, 'EmailAddress'))
			{
                $session->setForgottenEmail($email);
                $result['error'] = Mage::helper('onepagecheckout')->__('Invalid email address.');
            }
            else
            {
                $customer = Mage::getModel('customer/customer')->setWebsiteId(Mage::app()->getStore()->getWebsiteId())->loadByEmail($email);
                if(!$customer->getId())
                {
                	$session->setForgottenEmail($email);
                    $result['error'] = Mage::helper('onepagecheckout')->__('This email address was not found in our records.');
                }
                else
                {
                    try
                    {
						$new_pass = $customer->generatePassword();
                        $customer->changePassword($new_pass, false);
                        $customer->sendPasswordReminderEmail();
                        $result['success'] = true;
                        $result['message'] = Mage::helper('onepagecheckout')->__('A new password has been sent.');
                    }
                    catch (Exception $e)
                    {
                        $result['error'] = $e->getMessage();
                    }
                }
            }
        }
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
    }
}
?>