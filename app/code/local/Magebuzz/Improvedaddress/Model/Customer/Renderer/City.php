<?php
class Magebuzz_Improvedaddress_Model_Customer_Renderer_City implements Varien_Data_Form_Element_Renderer_Interface
{
  static protected $_cityCollections;

  public function render(Varien_Data_Form_Element_Abstract $element)
  {
    $html = '<tr>'."\n";

    $regionId = false;
    if ($region = $element->getForm()->getElement('region_id')) {
      $regionId = $region->getValue();
    }
    $cityCollection = false;
    if ($regionId) {
      if (!isset(self::$_cityCollections[$regionId])) {
        self::$_cityCollections[$regionId] = Mage::getModel('improvedaddress/city')
					->getCollection()
          ->addFieldToFilter('region_id', $regionId)
          ->load()
          ->toOptionArray();
      }
      $cityCollection = self::$_cityCollections[$regionId];
    }

    $cityId = intval($element->getForm()->getElement('city_id')->getValue());

    if(empty($cityId) || $cityId < 1){
      $cityId = "";
    }

    $htmlAttributes = $element->getHtmlAttributes();
    foreach ($htmlAttributes as $key => $attribute) {
      if ('type' === $attribute) {
        unset($htmlAttributes[$key]);
        break;
      }
    }

    $cityHtmlName = $element->getName();
    $cityIdHtmlName = str_replace('city', 'city_id', $cityHtmlName);
    $cityHtmlId = $element->getHtmlId();
    $cityIdHtmlId = str_replace('city', 'city_id', $cityHtmlId);

    if ($cityCollection && count($cityCollection) > 0) {
      $elementClass = $element->getClass();
      $html.= '<td class="label">'.$element->getLabelHtml().'</td>';
      $html.= '<td class="value">';

      $html .= '<select id="' . $cityIdHtmlId . '" name="' . $cityIdHtmlName . '" '
        . 'class="cities" >' . "\n";
      //. $element->serialize($htmlAttributes) .'>' . "\n";

      $selectedtext = '';
      foreach ($cityCollection as $city) {
        //$selected = ($cityId==$city['value']) ? ' selected="selected"' : '';
        $selected = '';
        $value = $city['value'];
        $value = empty($value) ? '' : ' value="'.$city['value'].'" ';
        if($cityId == $city['value']) {
          $selected = ' selected="selected" ';
          $selectedtext = $city['label'];
        }
        $html.= '<option '.$value.$selected.'>'.$city['label'].'</option>';
      }
      $html.= '</select>' . "\n";

      $html .= '<input type="hidden" name="' . $cityHtmlName . '" id="' . $cityHtmlId . '" value="'.$selectedtext .'"/>';

      $html.= '</td>';
      $element->setClass($elementClass);
    } else {
      $element->setClass('input-text');
      $html.= '<td class="label"><label for="'.$element->getHtmlId().'">'
        . $element->getLabel()
        . ' <span class="required" style="display:none">*</span></label></td>';

      $element->setRequired(false);
      $html.= '<td class="value">';
      $html .= '<input id="' . $cityHtmlId . '" name="' . $cityHtmlName
        . '" value="' . $element->getEscapedValue() . '" ' . $element->serialize($htmlAttributes) . "/>" . "\n";
      $html .= '<input type="hidden" name="' . $cityIdHtmlName . '" id="' . $cityIdHtmlId . '" value=""/>';
      $html .= '</td>'."\n";
    }
    $html.= '</tr>'."\n";
    return $html;
  }
}
