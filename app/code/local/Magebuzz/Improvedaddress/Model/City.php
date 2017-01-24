<?php

/*
 * Copyright (c) 2015 www.magebuzz.com 
 */

class Magebuzz_Improvedaddress_Model_City extends Mage_Core_Model_Abstract {

    public function _construct() {
        parent::_construct();
        $this->_init('improvedaddress/city');
    }

    public function toOptionArray($isMultiselect = false) {

//        print_r($this->getCityCollection());
//        die();

        if ($isMultiselect) {
            return $this->getCityCollection();
        } else {
            $options = $this->_toOptionArray('region_id', 'default_name', array('title' => 'default_name'));
            if (count($options) > 0) {
                array_unshift($options, array(
                    'title ' => null,
                    'value' => "",
                    'label' => Mage::helper('directory')->__('-- Please select --')
                ));
            }
            return $options;
        }
    }

    protected function getCityCollection() {

        $coreResource = Mage::getSingleton('core/resource');
        $connection = $coreResource->getConnection('core_read');

        $select = 'SELECT * FROM directory_region_city ORDER BY default_name ASC';

        $rows = $connection->fetchAll($select);
        $collection = array();

        for ($i = 0; $i < count($rows); $i++) {
            $collection[] = array(
                'title ' => null,
                'value' => $rows[$i]['city_id'],
                'label' => $rows[$i]['default_name']
            );
        }

        return $collection;
    }

}
