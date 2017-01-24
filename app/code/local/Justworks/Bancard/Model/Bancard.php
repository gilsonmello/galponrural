<?php

// app/code/local/Envato/Custompaymentmethod/Model/Paymentmethod.php
class Justworks_Bancard_Model_Bancard extends Mage_Payment_Model_Method_Abstract {

    protected $_code = 'bancard';
//    protected $_formBlockType = 'bancard/form_bancard';
//    protected $_infoBlockType = 'bancard/info_bancard';

    public function assignData($data) {
//        $info = $this->getInfoInstance();
//
//        if ($data->getCustomFieldOne()) {
//            $info->setCustomFieldOne($data->getCustomFieldOne());
//        }
//
//        if ($data->getCustomFieldTwo()) {
//            $info->setCustomFieldTwo($data->getCustomFieldTwo());
//        }

        return $this;
    }

    public function validate() {
        parent::validate();
//        $info = $this->getInfoInstance();
//
//        if (!$info->getCustomFieldOne()) {
//            $errorCode = 'invalid_data';
//            $errorMsg = $this->_getHelper()->__("CustomFieldOne is a required field.\n");
//        }
//
//        if (!$info->getCustomFieldTwo()) {
//            $errorCode = 'invalid_data';
//            $errorMsg .= $this->_getHelper()->__('CustomFieldTwo is a required field.');
//        }
//
//        if ($errorMsg) {
//            Mage::throwException($errorMsg);
//        }

        return $this;
    }

    public function getOrderPlaceRedirectUrl() {
        return Mage::getUrl('bancard/payment/singleBuy', array('_secure' => false));
    }

}
