<?php 
class Magebuzz_Improvedaddress_Block_Adminhtml_Region_Renderer_Name extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Action {
	public function render(Varien_Object $row) {
		$html = '';
		$locales = Mage::helper('improvedaddress')->getLocales();

		$resource = Mage::getSingleton('core/resource');
		$read = $resource->getConnection('core_read');
		$regionName = $resource->getTableName('directory/country_region_name');

		$select = $read->select()->from(array('region' => $regionName))->where('region.region_id=?', $row->getRegionId());
		$data = $read->fetchAll($select);
		foreach ($data as $row) {
				$arr[$row['locale']] = $row['name'];
		}
		foreach ($locales as $locale) {
				$name = $arr[$locale];
				if (!$name) {
						$name = 'EMPTY';
				}
				$html[] = '<span>' . $locale . '</span> => <span class="' . $locale . '_name">' . $name . '</span>';
		}
		$html = implode('<br />', $html);

		if ($html == '') {
				$html = '&nbsp;';
		}

		return $html;
	}	

}