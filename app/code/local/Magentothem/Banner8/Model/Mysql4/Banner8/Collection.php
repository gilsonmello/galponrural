<?php
/*------------------------------------------------------------------------
# Websites: http://www.magentothem.com/
-------------------------------------------------------------------------*/ 
class Magentothem_Banner8_Model_Mysql4_Banner8_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('banner8/banner8');
    }
}