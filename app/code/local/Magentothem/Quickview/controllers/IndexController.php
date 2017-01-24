<?php

class Magentothem_Quickview_IndexController extends Mage_Core_Controller_Front_Action
{

    protected function initProduct($productId) {

        $product = null;

        if($productId) {
            $product = Mage::getModel('catalog/product')
                ->setStoreId(Mage::app()->getStore()->getId())
                ->load($productId);

            Mage::register('current_product', $product);
            Mage::register('product', $product);

        }

        return $product;
    }

    protected function initProductLayout($product) {
        $update = $this->getLayout()->getUpdate();
        $update->addHandle('default');

        $this->addActionLayoutHandles();

        $update->addHandle('QUICK_PRODUCT_TYPE_'.$product->getTypeId());
        $update->addHandle('PRODUCT_DEFAULT_'.$product->getId());


        $this->loadLayoutUpdates();

        $update->addUpdate($product->getCustomLayoutUpdate());

        $this->generateLayoutXml();
        $this->generateLayoutBlocks();
        $this->_isLayoutLoaded = true;

        if ($root = $this->getLayout()->getBlock('root')) {
            $root->addBodyClass('product-' . $product->getUrlKey());
        }

        return $this;
    }

    public function viewAction() {

        $path = $this->getRequest()->getParam('path');

        //Get object by url rewrite
        if($path) {
            $oRewrite = Mage::getModel('core/url_rewrite')
                ->setStoreId(Mage::app()->getStore()->getId())
                ->loadByRequestPath($path);

            $productId = $oRewrite->getProductId();

            $product = $this->initProduct($productId);

            if($product) {
                
               
                
                Mage::dispatchEvent('catalog_controller_product_view', array('product'=>$product));

                if ($this->getRequest()->getParam('options')) {
                    $notice = $product->getTypeInstance(true)->getSpecifyOptionMessage();
                    Mage::getSingleton('catalog/session')->addNotice($notice);
                }

                Mage::getSingleton('catalog/session')->setLastViewedProductId($product->getId());
                Mage::getModel('catalog/design')->applyDesign($product, Mage_Catalog_Model_Design::APPLY_FOR_PRODUCT);

                $this->initProductLayout($product);
                $this->_initLayoutMessages('catalog/session');
                $this->_initLayoutMessages('tag/session');
                $this->_initLayoutMessages('checkout/session');
                
//                 return print_r($productId);
                
                $this->renderLayout();
            } else {
                $this->_forward('noRoute');
                return;
            }

        } else {
            $this->_forward('noRoute');
            return;
        }

    }
}