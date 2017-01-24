<?php

class Cdr_PureChat_Block_Jsheader extends Mage_Core_Block_Template
{
    protected $_widgetKey = false;
    
    
    public function getWidgetKey() {
        if($this->_widgetKey === false)
            $this->_widgetKey = Mage::getStoreConfig('purechat/widget/key');
        
        return $this->_widgetKey;
    }
    
    public function isActive() {
        return  
                Mage::getStoreConfigFlag('purechat/widget/active') && 
                $this->getWidgetKey();
    }
}
