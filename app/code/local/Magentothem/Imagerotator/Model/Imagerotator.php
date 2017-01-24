<?php

class Magentothem_Imagerotator_Model_Imagerotator extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('imagerotator/imagerotator');
    }
}