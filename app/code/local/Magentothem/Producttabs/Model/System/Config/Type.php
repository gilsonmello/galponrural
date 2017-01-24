<?php

class Magentothem_Producttabs_Model_System_Config_Type
{
    public function toOptionArray()
    {
        return array(
            array('value' => 'bestseller', 'label'=>Mage::helper('adminhtml')->__('Bestseller Products')),
            array('value' => 'featured', 'label'=>Mage::helper('adminhtml')->__('Featured Products')),
            array('value' => 'mostviewed', 'label'=>Mage::helper('adminhtml')->__('Most Viewed Products')),
            array('value' => 'new', 'label'=>Mage::helper('adminhtml')->__('New Products')),
            array('value' => 'random', 'label'=>Mage::helper('adminhtml')->__('Random Products')),
            array('value' => 'sale', 'label'=>Mage::helper('adminhtml')->__('Sale Products')),
            array('value' => 'promotion', 'label'=>Mage::helper('adminhtml')->__('Promotion')),
        );
    }
}