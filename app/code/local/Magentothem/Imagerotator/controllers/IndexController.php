<?php
class Magentothem_Imagerotator_IndexController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
    	
    	/*
    	 * Load an object by id 
    	 * Request looking like:
    	 * http://site.com/imagerotator?id=15 
    	 *  or
    	 * http://site.com/imagerotator/id/15 	
    	 */
    	/* 
		$imagerotator_id = $this->getRequest()->getParam('id');

  		if($imagerotator_id != null && $imagerotator_id != '')	{
			$imagerotator = Mage::getModel('imagerotator/imagerotator')->load($imagerotator_id)->getData();
		} else {
			$imagerotator = null;
		}	
		*/
		
		 /*
    	 * If no param we load a the last created item
    	 */ 
    	/*
    	if($imagerotator == null) {
			$resource = Mage::getSingleton('core/resource');
			$read= $resource->getConnection('core_read');
			$imagerotatorTable = $resource->getTableName('imagerotator');
			
			$select = $read->select()
			   ->from($imagerotatorTable,array('imagerotator_id','title','content','status'))
			   ->where('status',1)
			   ->order('created_time DESC') ;
			   
			$imagerotator = $read->fetchRow($select);
		}
		Mage::register('imagerotator', $imagerotator);
		*/

			
		$this->loadLayout();     
		$this->renderLayout();
    }
}