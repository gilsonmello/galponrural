<?php

// app/code/local/Envato/Custompaymentmethod/Block/Form/Custompaymentmethod.php
class Justworks_Bancard_Block_Form_Bancard extends Mage_Payment_Block_Form {

    protected function _construct() {
        parent::_construct();
        $this->setTemplate('bancard/form/bancard.phtml');
    }

}
