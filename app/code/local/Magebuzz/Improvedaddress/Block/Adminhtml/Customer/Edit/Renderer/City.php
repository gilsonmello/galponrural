<?php
/*
* Copyright (c) 2015 www.magebuzz.com 
*/
class Magebuzz_Improvedaddress_Block_Adminhtml_Customer_Edit_Renderer_City
    extends Mage_Adminhtml_Block_Abstract
    implements Varien_Data_Form_Element_Renderer_Interface
{
    /**
     * Factory instance
     *
     * @var Mage_Core_Model_Abstract
     */
    protected $_factory;

    /**
     * Constructor for Mage_Adminhtml_Block_Customer_Edit_Renderer_Region class
     *
     * @param array $args
     */
    public function __construct(array $args = array())
    {
        $this->_factory = !empty($args['factory']) ? $args['factory'] : Mage::getSingleton('core/factory');
    }

    /**
     * Output the city element and javasctipt that makes it dependent from region element
     *
     * @param Varien_Data_Form_Element_Abstract $element
     * @return string
     */
    public function render(Varien_Data_Form_Element_Abstract $element)
    {
        $country = $element->getForm()->getElement('country_id');
				$region = $element->getForm()->getElement('region_id');
        if (!is_null($region)) {
            $regionId = $region->getValue();
        } else {
            return $element->getDefaultHtml();
        }

        $cityId = $element->getForm()->getElement('city_id')->getValue();
        $quoteStoreId = $element->getEntityAttribute()->getStoreId();

        $html = '<tr>';
        $element->setClass('input-text');
        $element->setRequired(true);
        $html .= '<td class="label">' . $element->getLabelHtml() . '</td><td class="value">';
        $html .= $element->getElementHtml();

        $selectName = str_replace('city', 'city_id', $element->getName());
        $selectId = $element->getHtmlId() . '_id';
        $html .= '<select id="' . $selectId . '" name="' . $selectName
            . '" class="select required-entry" style="display:none">';
        $html .= '<option value="">' . $this->_factory->getHelper('customer')->__('Please select') . '</option>';
        $html .= '</select>';

        $html .= '<script type="text/javascript">' . "\n";
        $html .= '$("' . $selectId . '").setAttribute("defaultValue", "' . $cityId.'");' . "\n";
        $html .= 'new CityUpdater("' . $country->getHtmlId() . '", "' . $region->getHtmlId() . '", "' . $element->getHtmlId() . '", "' .
            $selectId . '", ' . Mage::helper('improvedaddress')->getCityJson().');' . "\n";
        $html .= '</script>' . "\n";

        $html .= '</td></tr>' . "\n";

        return $html;
    }
}
