<?php
class Magebuzz_Improvedaddress_Model_Observer {	
	public function addressSaveBefore(Varien_Event_Observer $observer) {
		$address = $observer->getEvent()->getCustomerAddress();
		$cityId = Mage::app()->getRequest()->getParam('city_id');
		$city = Mage::app()->getRequest()->getParam('city');						
		if ($cityId && $cityId != 0){
			$city = Mage::getModel('improvedaddress/city')->load($cityId)->getDefaultName();
		}
		$address->setData('city', $city);
	}
	
	public function addAdditionalDataToAddress(Varien_Event_Observer $observer) {
		$address = $observer->getAddress();
		$regionId = $address->getRegionId();
			
		if ($regionId = $address->getRegionId()) {
			if ($cityId = $address->getCityId()) {
				$json = Mage::helper('improvedaddress')->getCityJson();
				$data = json_decode($json, true);
				if(isset($data[$regionId][$cityId])) {
					$address->setCity($data[$regionId][$cityId]['name']);
				}
			}
		}

	}
}