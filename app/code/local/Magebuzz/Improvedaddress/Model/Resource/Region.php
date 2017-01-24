<?php
class Magebuzz_Improvedaddress_Model_Resource_Region extends Mage_Core_Model_Resource_Db_Abstract {
  protected function _construct() {
		$this->_init('improvedaddress/region', 'region_id');
	}
}