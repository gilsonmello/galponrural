<?php
class Qualityunit_Liveagent_Model_Config extends Mage_Core_Model_Config_Base {
    var $addToStore;

    public function __construct() {
        $this->url = Mage::getStoreConfig('liveagent_config/widgetdisplay/addtostore');
    }

    public function isButtonEnabled() {
        if (Mage::getStoreConfigFlag('liveagent_config/widgetdisplay/addtostore')) {
            return true;
        }
        return false;
    }
}