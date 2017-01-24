<?php
require_once "Mage/Catalog/controllers/Product/CompareController.php";

class Magentothem_Ajaxcartsuper_Product_CompareController extends Mage_Catalog_Product_CompareController {
    /**
     * override Add product to copare list action
     */
    public function addAction() {
        if ($this->getRequest()->getParam('callback')) {
            $ajaxData = array();
            $productId = (int) $this->getRequest()->getParam('product');
            if ($productId && (Mage::getSingleton('log/visitor')->getId() || Mage::getSingleton('customer/session')->isLoggedIn())) {
                $product = Mage::getModel('catalog/product')
                        ->setStoreId(Mage::app()->getStore()->getId())
                        ->load($productId);

                if ($product->getId()) {
                    Mage::getSingleton('catalog/product_compare_list')->addProduct($product);
                    Mage::dispatchEvent('catalog_product_compare_add_product', array('product' => $product));
                }

                Mage::helper('catalog/product_compare')->calculate();
            }
            $this->loadLayout();
            $sidebarCompare = "";
            if($this->getLayout()->getBlock('catalog.compare.sidebar')) {
                $sidebarCompare = $this->getLayout()->getBlock('catalog.compare.sidebar')->toHtml();
            }
			
			$collection = Mage::helper('catalog/product_compare')->getItemCollection();
            $ajaxData['compare_count'] = count($collection);
            $ajaxData['status'] = 1;
            $ajaxData['sidebar_compare'] = $sidebarCompare;
            $ajaxData['type_sidebar'] = 'compare';
			if (Mage::getStoreConfig('ajaxcartsuper/ajaxcartsuper_config/show_confirm')) {
				$pimage = Mage::helper('catalog/image')->init($product, 'small_image')->resize(48,70);
				$ajaxData['product_info'] = Mage::helper('ajaxcartsuper/data')->productHtml($product->getName(),$product->getProductUrl(),$pimage); 
			}	
            $this->getResponse()->setBody($this->getRequest()->getParam('callback').'('.Mage::helper('core')->jsonEncode($ajaxData).')');
            return;
        } else {

            parent::addAction();
        }
    }
	
	 public function clearAction()
    {
        $items = Mage::getResourceModel('catalog/product_compare_item_collection');

        if (Mage::getSingleton('customer/session')->isLoggedIn()) {
            $items->setCustomerId(Mage::getSingleton('customer/session')->getCustomerId());
        } elseif ($this->_customerId) {
            $items->setCustomerId($this->_customerId);
        } else {
            $items->setVisitorId(Mage::getSingleton('log/visitor')->getId());
        }

        /** @var $session Mage_Catalog_Model_Session */
        $session = Mage::getSingleton('catalog/session');

        try {
            $items->clear();
            $session->addSuccess($this->__('The comparison list was cleared.'));
            Mage::helper('catalog/product_compare')->calculate();
        } catch (Mage_Core_Exception $e) {
            $session->addError($e->getMessage());
        } catch (Exception $e) {
            $session->addException($e, $this->__('An error occurred while clearing comparison list.'));
        }
		$url = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB); 
		$this->_redirectUrl($url);
    }
    
     /**
     * override Remove item from compare list
     */
    public function removeAction() {
        if ($this->getRequest()->getParam('callback')) {
              $ajaxData = array();
            if ($productId = (int) $this->getRequest()->getParam('product')) {
                $product = Mage::getModel('catalog/product')
                        ->setStoreId(Mage::app()->getStore()->getId())
                        ->load($productId);

                if ($product->getId()) {
                    /** @var $item Mage_Catalog_Model_Product_Compare_Item */
                    $item = Mage::getModel('catalog/product_compare_item');
                    if (Mage::getSingleton('customer/session')->isLoggedIn()) {
                        $item->addCustomerData(Mage::getSingleton('customer/session')->getCustomer());
                    } elseif ($this->_customerId) {
                        $item->addCustomerData(
                                Mage::getModel('customer/customer')->load($this->_customerId)
                        );
                    } else {
                        $item->addVisitorId(Mage::getSingleton('log/visitor')->getId());
                    }

                    $item->loadByProduct($product);

                    if ($item->getId()) {
                        $item->delete();
                        Mage::dispatchEvent('catalog_product_compare_remove_product', array('product' => $item));
                        Mage::helper('catalog/product_compare')->calculate();
                    }
                }
            }
            $this->loadLayout();
            $sidebarCompare = "";
            if ($this->getLayout()->getBlock('catalog.compare.sidebar')) {
                $sidebarCompare = $this->getLayout()->getBlock('catalog.compare.sidebar')->toHtml();
            }
            $ajaxData['status'] = 1;
            $ajaxData['sidebar_compare'] = $sidebarCompare;
            $ajaxData['type_sidebar'] = 'compare';
            $this->getResponse()->setBody($this->getRequest()->getParam('callback').'('.Mage::helper('core')->jsonEncode($ajaxData).')');
            return;
            
        } else {
            parent::removeAction();
        }
    }


}