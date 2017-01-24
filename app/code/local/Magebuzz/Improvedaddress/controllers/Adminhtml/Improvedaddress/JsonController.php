<?php
class Magebuzz_Improvedaddress_Adminhtml_Improvedaddress_JsonController extends Mage_Adminhtml_Controller_Action
{
  public function cityAction() {
    
    $arrCity = array();
    $regionId = $this->getRequest()->getParam('parent');
    $arrCities = Mage::getModel('improvedaddress/city')
      ->getCollection()
      ->addFieldToFilter('region_id', $regionId)
      ->load()
      ->toOptionArray();
    if (!empty($arrCities)) {
      foreach ($arrCities as $city) {
        $arrCity[] = $city;
      }
    }
    $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($arrCity));
    
  }  
  
  // use in order edit address
  public function cityOrderAction(){
    $this->getResponse()->setHeader('Content-type', 'application/json');
    $arrCity = array();
    $regionId = $this->getRequest()->getParam('parent');
    $arrCities = Mage::getModel('improvedaddress/city')
      ->getCollection()
      ->addRegionFilter($regionId)
      ->load()
      ->toOptionArray();
    if (!empty($arrCities)) {
      foreach ($arrCities as $city) {
        $arrCity[] = $city;
      }
    }
    $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($arrCity));
    
  }
  
	protected function _isAllowed() {
		return true;
	}
}