<?php

class Qualityunit_Liveagent_Block_Base extends Mage_Core_Block_Template {

	protected $variables = array();

	protected function getTextArea($name, $value, $rows = 1, $cols = 10, $additionalClass = '') {
		return '<textarea rows="'.$rows.'" cols="'.$cols.'" id="'.$name.'" name="'.$name.'" class="'.$additionalClass.'" />'.$value.'</textarea>';
	}

	protected function _toHtml() {
		$html = $this->getTemplateString();
		foreach ($this->variables as $name => $value) {
			$html = str_replace('{'.$name.'}', $value, $html);
		}
		return $html;
	}

	protected function _prepareLayout() {
		parent::_prepareLayout();
	}

	protected function assignVariable($name, $value) {
		$this->variables[$name] = $value;
	}

	protected function getTemplateString() {
		return '';
	}
}