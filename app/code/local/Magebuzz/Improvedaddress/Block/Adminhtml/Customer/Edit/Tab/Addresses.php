<?php
/*
* Copyright (c) 2015 www.magebuzz.com 
*/
class Magebuzz_Improvedaddress_Block_Adminhtml_Customer_Edit_Tab_Addresses extends Mage_Adminhtml_Block_Customer_Edit_Tab_Addresses {
	public function __construct() {
		parent::__construct();
		$this->setTemplate('improvedaddress/customer/tab/addresses.phtml');
	}
		
  public function getCitiesUrl() {
    return $this->getUrl('adminhtml/improvedaddress_json/city');
  }

  public function initForm() {
    /* @var $customer Mage_Customer_Model_Customer */
    $customer = Mage::registry('current_customer');

    $form = new Varien_Data_Form();
    $fieldset = $form->addFieldset('address_fieldset', array(
        'legend'    => Mage::helper('customer')->__("Edit Customer's Address"))
    );

    $addressModel = Mage::getModel('customer/address');
    $addressModel->setCountryId(Mage::helper('core')->getDefaultCountry($customer->getStore()));
    /** @var $addressForm Mage_Customer_Model_Form */
    $addressForm = Mage::getModel('customer/form');
    $addressForm->setFormCode('adminhtml_customer_address')
      ->setEntity($addressModel)
      ->initDefaultValues();
    $attributes = $addressForm->getAttributes();
    if(isset($attributes['street'])) {
      Mage::helper('adminhtml/addresses')
        ->processStreetAttribute($attributes['street']);
    }
    foreach ($attributes as $attribute) {
      /* @var $attribute Mage_Eav_Model_Entity_Attribute */
      $attribute->setFrontendLabel(Mage::helper('customer')->__($attribute->getFrontend()->getLabel()));
      $attribute->unsIsVisible();
    }
    $this->_setFieldset($attributes, $fieldset);

    $regionElement = $form->getElement('region');
    $regionElement->setRequired(true);
    if ($regionElement) {
      $regionElement->setRenderer(Mage::getModel('adminhtml/customer_renderer_region'));
    }

    $regionElement = $form->getElement('region_id');
    if ($regionElement) {
      $regionElement->setNoDisplay(true);
    }

    $cityElement = $form->getElement('city');
    $cityElement->setRequired(true);

    if ($cityElement) {
      $cityElement->addClass('cities');
      $cityElement->setRenderer(Mage::getModel('improvedaddress/customer_renderer_city'));
    }

    $cityElement = $form->getElement('city_id');
    if ($cityElement) {
      $cityElement->setNoDisplay(true);
    }

    $country = $form->getElement('country_id');
    if ($country) {
      $country->addClass('countries');
    }

    if ($this->isReadonly()) {
      foreach ($addressModel->getAttributes() as $attribute) {
        $element = $form->getElement($attribute->getAttributeCode());
        if ($element) {
          $element->setReadonly(true, true);
        }
      }
    }

    $customerStoreId = null;
    if ($customer->getId()) {
      $customerStoreId = Mage::app()->getWebsite($customer->getWebsiteId())->getDefaultStore()->getId();
    }

    $prefixElement = $form->getElement('prefix');
    if ($prefixElement) {
      $prefixOptions = $this->helper('customer')->getNamePrefixOptions($customerStoreId);
      if (!empty($prefixOptions)) {
        $fieldset->removeField($prefixElement->getId());
        $prefixField = $fieldset->addField($prefixElement->getId(),
          'select',
          $prefixElement->getData(),
          '^'
        );
        $prefixField->setValues($prefixOptions);
      }
    }

    $suffixElement = $form->getElement('suffix');
    if ($suffixElement) {
      $suffixOptions = $this->helper('customer')->getNameSuffixOptions($customerStoreId);
      if (!empty($suffixOptions)) {
        $fieldset->removeField($suffixElement->getId());
        $suffixField = $fieldset->addField($suffixElement->getId(),
          'select',
          $suffixElement->getData(),
          $form->getElement('lastname')->getId()
        );
        $suffixField->setValues($suffixOptions);
      }
    }

    $addressCollection = $customer->getAddresses();
    $this->assign('customer', $customer);
    $this->assign('addressCollection', $addressCollection);
    $form->setValues($addressModel->getData());
    $this->setForm($form);

    return $this;
  }
}