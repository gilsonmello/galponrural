<?php

class Cdr_PureChat_Block_Button extends Mage_Core_Block_Template
{
    protected $_configPath = null;
    
    public function isActive() {
        return  
                Mage::getStoreConfigFlag('purechat/widget/active') && 
                Mage::getStoreConfigFlag('purechat/widget/key') &&
                Mage::getStoreConfigFlag($this->_configPath);
    }
    
    public function setButton($btn) {
        $this->_configPath = 'purechat/widget/'.$btn;
    }
}