<?php
class Magentothem_Producttabs_IndexController extends Mage_Core_Controller_Front_Action
{
    public function resultAction()
    {
    	if ($this->getRequest()->isAjax()) {

	        $this->loadLayout();    
	        $this->renderLayout();
	        return $this;
	    }
    }

}