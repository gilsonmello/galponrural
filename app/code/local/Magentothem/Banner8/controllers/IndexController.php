<?php
/*------------------------------------------------------------------------
# Websites: http://www.magentothem.com/
-------------------------------------------------------------------------*/ 
class Magentothem_Banner8_IndexController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
    		
		$this->loadLayout();     
		$this->renderLayout();
    }
}