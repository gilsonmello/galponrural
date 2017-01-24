<?php

class MST_Onepagecheckout_Model_Mysql4_License extends Mage_Core_Model_Mysql4_Abstract
{

    public function _construct()
    {
        // Note that the news_id refers to the key field in your database table.
        $this->_init('onepagecheckout/license', 'license_id');
    }

}