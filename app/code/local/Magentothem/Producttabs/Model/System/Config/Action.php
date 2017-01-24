<?php
class Magentothem_Producttabs_Model_System_Config_Action
{
    public function toOptionArray()
    {
        return array(
            array('value'=>'0', 'label'=>Mage::helper('adminhtml')->__('None')),
            array('value'=>'cart', 'label'=>Mage::helper('adminhtml')->__('Add to Cart')),
            array('value'=>'compare', 'label'=>Mage::helper('adminhtml')->__('Add to Compare')),
            array('value'=>'wishlist', 'label'=>Mage::helper('adminhtml')->__('Wishlist')),
            array('value'=>'cart,compare', 'label'=>Mage::helper('adminhtml')->__('Cart with Compare')),
            array('value'=>'cart,wishlist', 'label'=>Mage::helper('adminhtml')->__('Cart with Wishlist')),
            array('value'=>'compare,wishlist', 'label'=>Mage::helper('adminhtml')->__('Compare with Wishlist')),
            array('value'=>'compare,cart,wishlist', 'label'=>Mage::helper('adminhtml')->__('Display all')),
        );
    }

}