<?php

class Justworks_Bancard_Helper_Data extends Mage_Core_Helper_Abstract {

    function getPaymentGatewayUrl() {
        return Mage::getUrl('bancard/payment/gateway', array('_secure' => false));
    }

}
