<?php
/*------------------------------------------------------------------------
# Websites: http://www.magentothem.com/
-------------------------------------------------------------------------*/ 
class Magentothem_Banner8_Model_Banner8 extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('banner8/banner8');
    }
}