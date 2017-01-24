<?php
class Magebuzz_Improvedaddress_Block_Adminhtml_Sales_Order_Create_Shipping_Address extends Mage_Adminhtml_Block_Sales_Order_Create_Shipping_Address {
	protected function _prepareForm() {
		parent::_prepareForm();
		
		$cityElement = $this->_form->getElement('city');
    $cityElement->setRequired(true);

    if ($cityElement) {
      $cityElement->addClass('select');
      $cityElement->setRenderer($this->getLayout()->createBlock('improvedaddress/adminhtml_customer_edit_renderer_city'));
    }
		
		$cityElement = $this->_form->getElement('city_id');
    if ($cityElement) {
      $cityElement->setNoDisplay(true);
    }
		
		return $this;
	}
}