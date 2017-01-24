<?php

class Magentothem_Imagerotator_Model_Mysql4_Imagerotator extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        // Note that the imagerotator_id refers to the key field in your database table.
        $this->_init('imagerotator/imagerotator', 'imagerotator_id');
    }
}