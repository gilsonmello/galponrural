<?php

class Magentothem_Imagerotator_Model_Mysql4_Imagerotator_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('imagerotator/imagerotator');
    }
}