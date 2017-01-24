<?php
class Magentothem_Imagerotator_Block_Imagerotator extends Mage_Core_Block_Template
{
	public function _prepareLayout()
    {
		return parent::_prepareLayout();
    }
    
     public function getImagerotator()     
     { 
        if (!$this->hasData('imagerotator')) {
            $this->setData('imagerotator', Mage::registry('imagerotator'));
        }
        return $this->getData('imagerotator');
        
    }
}