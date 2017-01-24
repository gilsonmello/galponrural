<?php
/*
* Copyright (c) 2015 www.magebuzz.com 
*/
class Magebuzz_Improvedaddress_Block_Adminhtml_Region_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form {
  protected function _prepareForm() {
		$form = new Varien_Data_Form();
		$this->setForm($form);
		$countries = Mage::getSingleton('directory/country')->getCollection()
			->loadData()->toOptionArray(false);
		$id = $this->getRequest()->getParam('region_id');

		$fieldSet = $form->addFieldset('region_form', array('legend' => Mage::helper('improvedaddress')->__('State information')));
		
		$fieldSet->addField('country_id', 'select', 
			array(
				'label'    => Mage::helper('improvedaddress')->__('Country'),
				'name'     => 'country_id',
				'required' => true,
				'values'   => $countries
			)
		);				

		$fieldSet->addField('code', 'text', 
			array(
				'label'    => Mage::helper('improvedaddress')->__('Code'),
				'class'    => 'required-entry',
				'required' => true,
				'name'     => 'code',
			)
		);
		$fieldSet->addField('default_name', 'text', 
			array(
				'label'    => Mage::helper('improvedaddress')->__('Default Name'),
				'class'    => 'required-entry',
				'required' => true,
				'name'     => 'default_name',
			)
		);
		
		$locales = Mage::helper('improvedaddress')->getLocales();
		foreach ($locales as $locale) {
			$fieldSet{$locale} = $form->addFieldset('improvedaddress_form_' . $locale, array('legend' => Mage::helper('improvedaddress')->__('Locale ' . $locale)));
			$fieldSet{$locale}->addField(
				'name_'.$locale, 'text', 
				array(
					'label' => Mage::helper('improvedaddress')->__('Name'),
					'name'  => 'name_'.$locale,
				)
			);
		}
		
		if (Mage::getSingleton('adminhtml/session')->getRegionData()) {
			$data = Mage::getSingleton('adminhtml/session')->getRegionData(); 
			$form->setValues($data);
			Mage::getSingleton('adminhtml/session')->setRegionData(null);
		} elseif (Mage::registry('region_data')) {
			$data = Mage::registry('region_data')->getData(); 
			$form->setValues($data);
		}
		
		if ($id) {
			$resource = Mage::getSingleton('core/resource');
			$read = $resource->getConnection('core_read');
			$regionName = $resource->getTableName('directory/country_region_name');
			$select = $read->select()->from(array('region'=>$regionName))->where('region.region_id=?', $id);
			$data =$read->fetchAll($select);
			foreach($data as $row) {
				$form->addValues(array('name_'.$row['locale']=> $row['name']));
			}
		}
		return parent::_prepareForm();

	}
}