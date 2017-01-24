<?php

/*
 * Copyright (c) 2015 www.magebuzz.com 
 */

class Magebuzz_Improvedaddress_Block_Adminhtml_City_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form {

    protected $_regionOptions = null;

    protected function _prepareForm() {
        $form = new Varien_Data_Form();
        $this->setForm($form);
        $countries = Mage::getSingleton('directory/country')->getCollection()
                        ->loadData()->toOptionArray(false);

        $regions = array();

        $fieldSet = $form->addFieldset('improvedaddress_form', array('legend' => Mage::helper('improvedaddress')->__('City Information')));

        $data = FALSE;

        if (Mage::getSingleton('adminhtml/session')->getCityData()) {
            $data = Mage::getSingleton('adminhtml/session')->getCityData();
        } elseif (Mage::registry('city_data')->getData()) {
            $data = Mage::registry('city_data')->getData();
        }

        if ($data) {
            $countryId = $data['country_id'];
            $regions = $this->getRegions($countryId);
        }

        $regionUrl = Mage::getUrl('*/*/reloadRegion');
        $fieldSet->addField('country_id', 'select', array(
            'label' => Mage::helper('improvedaddress')->__('Country'),
            'name' => 'country_id',
            'required' => true,
            'values' => $countries,
            'onchange' => "reloadRegion(this.value, '$regionUrl')",
                )
        );

        $fieldSet->addField('region_id', 'select', array(
            'label' => Mage::helper('improvedaddress')->__('Region'),
            'name' => 'region_id',
            'required' => true,
            'values' => $regions,
                //'onchange' => "alert('haa')"
                )
        );

        $fieldSet->addField('code', 'text', array(
            'label' => Mage::helper('improvedaddress')->__('Code'),
            'class' => 'required-entry',
            'required' => true,
            'name' => 'code',
                )
        );
        $fieldSet->addField('default_name', 'text', array(
            'label' => Mage::helper('improvedaddress')->__('Default Name'),
            'class' => 'required-entry',
            'required' => true,
            'name' => 'default_name',
                )
        );

        $fieldSet->addField('distance', 'text', array(
            'label' => Mage::helper('improvedaddress')->__('Distance'),
            'class' => 'required-entry',
            'required' => true,
            'name' => 'distance',
                )
        );
        
        $fieldSet->addField('dias_entrega', 'text', array(
            'label' => Mage::helper('improvedaddress')->__('Estimate delivery date (days)'),
            'class' => 'required-entry',
            'required' => true,
            'name' => 'dias_entrega',
                )
        );
        
        $fieldSet->addField('dias_entrega_motoboy', 'text', array(
            'label' => Mage::helper('improvedaddress')->__('Estimate delivery date motoboy (days)'),
            'class' => 'required-entry',
            'required' => true,
            'name' => 'dias_entrega_motoboy',
                )
        );
        
        $fieldSet->addField('observacoes', 'text', array(
            'label' => Mage::helper('improvedaddress')->__('Comments'),
            'class' => '',
            'required' => false,
            'name' => 'observacoes',
                )
        );
        
        $fieldSet->addField('preco_fixo', 'text', array(
            'label' => Mage::helper('improvedaddress')->__('Preço Fixo'),
            'class' => '',
            'required' => false,
            'name' => 'preco_fixo',
                )
        );

        // $fieldset->addField('status', 'select', array(
        // 'label'     => Mage::helper('improvedaddress')->__('Status'),
        // 'name'      => 'status',
        // 'values'    => array(
        // array(
        // 'value'     => 1,
        // 'label'     => Mage::helper('improvedaddress')->__('Enabled'),
        // ),
        // array(
        // 'value'     => 2,
        // 'label'     => Mage::helper('improvedaddress')->__('Disabled'),
        // ),
        // ),
        // ));	

        if (Mage::getSingleton('adminhtml/session')->getCityData()) {
            $form->setValues(Mage::getSingleton('adminhtml/session')->getCityData());
            Mage::getSingleton('adminhtml/session')->setCityData(null);
        } elseif (Mage::registry('city_data')) {
            $form->setValues(Mage::registry('city_data')->getData());
        }
        return parent::_prepareForm();
    }

    public function getThaiRegions() {
        if ($this->_regionOptions == null) {
            $this->_regionOptions[] = array('value' => '', 'label' => '');
            $regions = Mage::getSingleton('improvedaddress/region')->getCollection()
                    ->addFieldToFilter('country_id', 'TH');
            foreach ($regions as $region) {
                $this->_regionOptions[] = array(
                    'value' => $region->getRegionId(),
                    'label' => $region->getDefaultName()
                );
            }
        }
        return $this->_regionOptions;
    }

    public function getRegions($country_id) {
        if ($this->_regionOptions == null) {
            $this->_regionOptions[] = array('value' => '', 'label' => '');
            $regions = Mage::getSingleton('improvedaddress/region')->getCollection()
                    ->addFieldToFilter('country_id', $country_id);
            foreach ($regions as $region) {
                $this->_regionOptions[] = array(
                    'value' => $region->getRegionId(),
                    'label' => $region->getDefaultName()
                );
            }
        }
        return $this->_regionOptions;
    }

}
