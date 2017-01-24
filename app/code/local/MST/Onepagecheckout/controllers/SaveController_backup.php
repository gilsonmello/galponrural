<?php
class MST_Onepagecheckout_SaveController extends Mage_Checkout_Controller_Action
{

    public function getOnepagecheckout()
    {
        return Mage::getSingleton('onepagecheckout/data');
    }
    
    public function getOnepage()
    {
        return Mage::getSingleton('checkout/type_onepage');
    }   
     
    protected function _filterPostData($data)
    {
        $data = $this->_filterDates($data, array('dob'));
        return $data;
    }
    
    //-------------start save when success checkout order-------------------
    public function removeHistoryComment()
    {
        Mage::getSingleton('customer/session')->setOrderCustomerComment(null);
    }
        
    public function emptyCart()
    {
		if (Mage::helper('onepagecheckout')->isOnepageCheckoutEnabled())
		{
			$cartHelper = Mage::helper('checkout/cart');
			$items = $cartHelper->getCart()->getItems();
			foreach ($items as $item) {
				$itemId = $item->getItemId();
				$cartHelper->getCart()->removeItem($itemId)->save();
			}
		}
    }
        
    protected function _saveOrderPurchase()
    {
    	$result = array();
        try 
        {
            $pmnt_data = $this->getRequest()->getPost('payment', array());
            $result = $this->getOnepagecheckout()->savePayment($pmnt_data);

            $redirectUrl = $this->getOnepagecheckout()->getQuote()->getPayment()->getCheckoutRedirectUrl();
            if ($redirectUrl)
            {
                $result['redirect'] = $redirectUrl;
            }
        }
        catch (Mage_Payment_Exception $e)
        {
            if ($e->getFields()) {
                $result['fields'] = $e->getFields();
            }
            $result['error'] = $e->getMessage();
        }
        catch (Mage_Core_Exception $e)
        {
            $result['error'] = $e->getMessage();
        }
        catch (Exception $e)
        {
            Mage::logException($e);
            $result['error'] = Mage::helper('onepagecheckout')->__('Unable to set Payment Method.');
        }
        return $result;
    }
            
    protected function _subscribeNews()
    {
        if ($this->getRequest()->isPost() && $this->getRequest()->getPost('newsletter'))
        {
            $customerSession = Mage::getSingleton('customer/session');
            if($customerSession->isLoggedIn())
            	$email = $customerSession->getCustomer()->getEmail();
            else
            {
            	$bill_data = $this->getRequest()->getPost('billing');
            	$email = $bill_data['email'];
            }
            try {
                if (!$customerSession->isLoggedIn() && Mage::getStoreConfig(Mage_Newsletter_Model_Subscriber::XML_PATH_ALLOW_GUEST_SUBSCRIBE_FLAG) != 1)
                    Mage::throwException(Mage::helper('onepagecheckout')->__('Sorry, subscription for guests is not allowed. Please <a href="%s">register</a>.', Mage::getUrl('customer/account/create/')));
                $ownerId = Mage::getModel('customer/customer')->setWebsiteId(Mage::app()->getStore()->getWebsiteId())->loadByEmail($email)->getId();
                if ($ownerId !== null && $ownerId != $customerSession->getId())
                    Mage::throwException(Mage::helper('onepagecheckout')->__('Sorry, you are trying to subscribe email assigned to another user.'));
                $status = Mage::getModel('newsletter/subscriber')->subscribe($email);
            }
            catch (Mage_Core_Exception $e) {
            }
            catch (Exception $e) {
            }
        }
    }
    
    public function saveOrderAction()
    {
        $validation_enabled	= Mage::helper('onepagecheckout')->isAddressVerificationEnabled();	
        $result = array();
        try {
            $bill_data = $this->_filterPostData($this->getRequest()->getPost('billing', array()));
            $bill_addr_id = $this->getRequest()->getPost('billing_address_id', false);
	        // need for verification
        	$ship_updated = false;
        	$shipping_address_changed	= false;
        	// get prev shipping data.
			$prev_ship = $this->getOnepagecheckout()->getQuote()->getShippingAddress();
			$prev_same_as_bill = $prev_ship->getSameAsBilling();

	        $billing_address_changed	= false;
	        if($this->getOnepagecheckout()->_checkChangedAddress($bill_data, 'Billing', $bill_addr_id, $validation_enabled))
	        {
	        	$billing_address_changed	= true;
	        	$this->getOnepagecheckout()->getCheckout()->setBillingWasValidated(false);
	        }
			$result = $this->getOnepagecheckout()->saveBilling($bill_data,$bill_addr_id,true,true);
            if ($result)
            {
            	$result['error_messages'] = $result['message'];
            	$result['error'] = true;
                $result['success'] = false;
                if($result['success']){
                }else{
                    Mage::getModel('customer/session')->setData('message',$result['error_messages']);
                    $this->_redirectUrl(Mage::getBaseUrl().'onepagecheckout');
                }
            }
            // need for address validation
			if (isset($bill_data['use_for_shipping']) && $bill_data['use_for_shipping'] == 1 && !$this->getOnepagecheckout()->getQuote()->isVirtual())
			{
				$ship_updated = true;
				if($billing_address_changed)
                    $shipping_address_changed = true;
				$this->getOnepagecheckout()->getCheckout()->setShippingWasValidated(true);
			}    
            if ((!$bill_data['use_for_shipping'] || !isset($bill_data['use_for_shipping'])) && !$this->getOnepagecheckout()->getQuote()->isVirtual())
            {
		        $ship_data		= $this->_filterPostData($this->getRequest()->getPost('shipping', array()));
		        $ship_addr_id	= $this->getRequest()->getPost('shipping_address_id', false);

		        if (!$ship_updated)
		        {
		        	if ($this->getOnepagecheckout()->_checkChangedAddress($ship_data, 'Shipping', $ship_addr_id, $validation_enabled))
		        	{
		        		$shipping_address_changed = true;
		        		$this->getOnepagecheckout()->getCheckout()->setShippingWasValidated(false);
		        	}
		        	else
		        	{
		        		// check if 'use for shipping' has been changed
		        		if($prev_same_as_bill == 1)
		        		{
			        		$shipping_address_changed = true;
			        		$this->getOnepagecheckout()->getCheckout()->setShippingWasValidated(false);
		        		}
		        	}
		        }
				$result = $this->getOnepagecheckout()->saveShipping($ship_data,$ship_addr_id, true, true);
                if ($result)
                {
                	$result['error_messages'] = $result['message'];
                	$result['error'] = true;
                    $result['success'] = false;
                    if($result['success']){
                    }else{
                        Mage::getModel('customer/session')->setData('message',$result['error_messages']);
                        $this->_redirectUrl(Mage::getBaseUrl().'onepagecheckout');
                    }
                }
            }
                                    
            $result = $this->_saveOrderPurchase();
            if($result && !isset($result['redirect']))
            {
                $result['error_messages'] = $result['error'];
            }else{
                $result['success'] = true;
            }
            if(!isset($result['error']))
            {
                Mage::dispatchEvent('checkout_controller_onepage_save_shipping_method', array('request'=>$this->getRequest(), 'quote'=>$this->getOnepagecheckout()->getQuote()));
                $this->_subscribeNews();
            }
            Mage::getSingleton('customer/session')->setOrderCustomerComment($this->getRequest()->getPost('order-comment'));
			$this->getOnepagecheckout()->getQuote()->setCustomerNote($this->getRequest()->getPost('order-comment'));
            //if have not redirect checkout paypal and have not error-> checkout normal
            if (!isset($result['redirect']) && !isset($result['error']))
            {
                //active shipping method
                $ship_method	= $this->getRequest()->getPost('shipping_method', false);
                $this->getOnepagecheckout()->useShipping($ship_method);
                $this->getOnepagecheckout()->getQuote()->collectTotals()->save();
                //end active shipping method
            	$pmnt_data = $this->getRequest()->getPost('payment', false);
                if ($pmnt_data)$this->getOnepagecheckout()->getQuote()->getPayment()->importData($pmnt_data);
                $this->getOnepagecheckout()->saveOrder();
                $redirectUrl = $this->getOnepagecheckout()->getCheckout()->getRedirectUrl();
                $result['success'] = true;
                $result['error']   = false;
                $result['order_created'] = true;
            }
        }
        catch (Mage_Core_Exception $e)
        {
            Mage::logException($e);
            Mage::helper('checkout')->sendPaymentFailedEmail($this->getOnepagecheckout()->getQuote(), $e->getMessage());
            $result['error_messages'] = $e->getMessage();
            $result['error'] = true;
            $result['success'] = false;
            $goto_section = $this->getOnepagecheckout()->getCheckout()->getGotoSection();
            if ($goto_section)
            {
            	$this->getOnepagecheckout()->getCheckout()->setGotoSection(null);
                $result['goto_section'] = $goto_section;
            }
            $update_section = $this->getOnepagecheckout()->getCheckout()->getUpdateSection();
            if ($update_section)
            {
                $this->getOnepagecheckout()->getCheckout()->setUpdateSection(null);
            }
            $this->getOnepagecheckout()->getQuote()->save();
        } 
        catch (Exception $e)
        {
            Mage::logException($e);
            Mage::helper('checkout')->sendPaymentFailedEmail($this->getOnepagecheckout()->getQuote(), $e->getMessage());
            $result['error_messages'] = Mage::helper('onepagecheckout')->__('There was an error processing your order. Please contact support or try again later.');
            $result['error']    = true;
            $result['success']  = false;
            $this->getOnepagecheckout()->getQuote()->save();
        }
        if (isset($redirectUrl)) {
            $redirect_checkout = $redirectUrl;
        }else{
            $redirect_checkout = $result['redirect'];
        }   
        if($result['success']){
            if($redirect_checkout){
                $url = $redirect_checkout;
            }else{
                $url = Mage::getBaseUrl().'checkout/onepage/success';
            }
            $this->_redirectUrl($url);
            //$this->removeHistoryComment();
            //$this->emptyCart();
        }else{
            Mage::getModel('customer/session')->setData('message',$result['error_messages']);
            $this->_redirectUrl(Mage::getBaseUrl().'onepagecheckout');
        }
    }
    
}
?>