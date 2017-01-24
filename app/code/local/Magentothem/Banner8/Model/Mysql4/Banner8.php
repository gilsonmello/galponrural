<?php
/*------------------------------------------------------------------------
# Websites: http://www.magentothem.com/
-------------------------------------------------------------------------*/ 
class Magentothem_Banner8_Model_Mysql4_Banner8 extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        $this->_init('banner8/banner8', 'banner8_id');
    }
}