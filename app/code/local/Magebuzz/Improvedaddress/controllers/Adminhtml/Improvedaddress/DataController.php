<?php
class Magebuzz_Customaddress_Adminhtml_Customaddress_DataController extends Mage_Adminhtml_Controller_Action {  
  public function importAction() {
		// $filePath = getcwd() . 'app'.DS.'code'.DS.'local'.DS.'Magebuzz'.DS.'Customaddress'.DS.'sql'.DS.'test.sql';
	
    // //$dumpFile = $sample_data_path . "magento_sample_data_for_1.6.1.0.sql";
		// try {
			// $file = file_get_contents($filePath, true);
			// echo $file;die();
			// $adapter = $this->_writeConnection();
			// $adapter->query($file);
		// }
		// catch (Exception $e) {
			// echo $e->getMessage();
			// die('xxxeee');
		// }
		
		// echo $file;die('xxxx');
		Mage::getSingleton('core/session')->addError('Importing function is not working in this version. Please import thai_address.sql directly using phpmyadmin.');
		//Mage::getSingleton('core/session')->addSuccess('Successfully import Thai address data.');
		$this->_redirect('adminhtml/system_config/index', array('section' => 'customaddress'));
    return;
  }
	
	protected function _isAllowed() {
		return true;
	}
	
	protected function _writeConnection() {
		$resource = Mage::getSingleton('core/resource');
		return $resource->getConnection('core_write');
	}
}