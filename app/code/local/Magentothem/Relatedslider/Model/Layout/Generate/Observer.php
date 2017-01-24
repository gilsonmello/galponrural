<?php
class Magentothem_Relatedslider_Model_Layout_Generate_Observer {
    /*
     * Get head block
     */
    private function __getHeadBlock() {
		$enabled = Mage::getStoreConfig('relatedslider/relatedslider_config/enabled');
		return Mage::getSingleton('core/layout')->getBlock('magentothem_relatedslider_head');
    }
}