<?php
/**
 * Magento
 * NOTICE OF LICENSE 
 * @package    ZealousWeb
 * @author     ZealousWeb
 * @copyright  Copyright (c) 2014 Zealousweb Technology. (http://www.zealousweb.com)
 */
 ?>
<?php
class ZealousWeb_GTranslator_IndexController extends Mage_Core_Controller_Front_Action
{
    public function IndexAction() 
    {      
	  $this->loadLayout();   
      $this->renderLayout(); 	  
    }
}