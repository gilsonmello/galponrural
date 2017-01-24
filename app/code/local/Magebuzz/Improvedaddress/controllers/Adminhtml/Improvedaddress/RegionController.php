<?php
/*
* Copyright (c) 2015 www.magebuzz.com 
*/
class Magebuzz_Improvedaddress_Adminhtml_Improvedaddress_RegionController extends Mage_Adminhtml_Controller_Action {
	protected function _initAction() {
		$this->loadLayout()
			->_setActiveMenu('improvedaddress/region')
			->_addBreadcrumb(Mage::helper('adminhtml')->__('Items Manager'), Mage::helper('adminhtml')->__('Item Manager'));
		
		return $this;
	}   
 
	public function indexAction() {
		$this->_initAction()
			->renderLayout();
	}

	public function editAction() {
		$id = $this->getRequest()->getParam('id');
		$model = Mage::getModel('improvedaddress/region')->load($id);

		if ($model->getId() || $id == 0) {
			$data = Mage::getSingleton('adminhtml/session')->getFormData(true);
			if (!empty($data)) {
				$model->setData($data);
			}

			Mage::register('region_data', $model);

			$this->loadLayout();
			$this->_setActiveMenu('improvedaddress/region');

			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item Manager'), Mage::helper('adminhtml')->__('Item Manager'));
			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item News'), Mage::helper('adminhtml')->__('Item News'));

			$this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

			$this->_addContent($this->getLayout()->createBlock('improvedaddress/adminhtml_region_edit'))
				->_addLeft($this->getLayout()->createBlock('improvedaddress/adminhtml_region_edit_tabs'));

			$this->renderLayout();
		} else {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('improvedaddress')->__('Item does not exist'));
			$this->_redirect('*/*/');
		}
	}
 
	public function newAction() {
		$this->_forward('edit');
	}
 
	public function saveAction() {
		$request = $this->getRequest();
		if ($this->getRequest()->getPost()) {
			$id = $request->getParam('id');
			$code = $request->getParam('code');
			$name = $request->getParam('default_name');
			$countryId = $request->getParam('country_id');
			if (!$name || !$code) {
				Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please fill the required fields'));
				$this->_redirect('*/*/');
				return;
			}
			$state = Mage::getModel('improvedaddress/region')->getCollection()
				->addFieldToFilter('code', $code)
				->addFieldToFilter('country_id', $countryId)
				->getAllIds();
			if (count($state) > 0 && !in_array($id, $state)) {
				Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('State/Country combination must be unique'));
				$this->_redirect('*/*/edit', array('id' => $id));
				return;
			}

			try {
				$state = Mage::getModel('improvedaddress/region');
				$state->setRegionId($id)
					->setCode($code)
					->setCountryId($countryId)
					->setDefaultName($name)
					->save();
				if ($state->getRegionId()) {
					$locales = Mage::helper('improvedaddress')->getLocales();
					$resource = Mage::getSingleton('core/resource');
					$write = $resource->getConnection('core_write');
					$regionName = $resource->getTableName('directory/country_region_name');
					$write->delete($regionName, array('region_id =' . $state->getRegionId()));
					foreach ($locales as $locale) {
						$localeName = $request->getParam('name_' . $locale);
						if ($localeName == '' && $locale == 'en_US') {
							$localeName = $name;
						}
						if ($localeName) {
							$write->insert($regionName, array('region_id' => $state->getRegionId(), 'locale' => $locale, 'name' => trim($localeName)));
						}
					}
				}
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Item was successfully saved'));
				Mage::getSingleton('adminhtml/session')->setRegionData(false);
				$this->_redirect('*/*/');
				return;
			} catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
				Mage::getSingleton('adminhtml/session')->setRegionData($this->getRequest()->getPost());
				$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
				return;
			}
		}
		$this->_redirect('*/*/');
	}
 
	public function deleteAction() {
		if( $this->getRequest()->getParam('id') > 0 ) {
			try {
				$model = Mage::getModel('improvedaddress/region');
				 
				$model->setId($this->getRequest()->getParam('id'))
					->delete();
					 
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Item was successfully deleted'));
				$this->_redirect('*/*/');
			} catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
				$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
			}
		}
		$this->_redirect('*/*/');
	}

	public function massDeleteAction() {
		$regionIds = $this->getRequest()->getParam('region');
		if(!is_array($regionIds)) {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select item(s)'));
		} else {
			try {
				foreach ($regionIds as $regionId) {
					$region = Mage::getModel('improvedaddress/region')->load($regionId);
					$region->delete();
				}
				Mage::getSingleton('adminhtml/session')->addSuccess(
					Mage::helper('adminhtml')->__(
						'Total of %d record(s) were successfully deleted', count($regionIds)
					)
				);
			} catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
			}
		}
		$this->_redirect('*/*/index');
	}		
	
	protected function _isAllowed()	{
		return Mage::getSingleton('admin/session')->isAllowed('improvedaddress/region');
	}
}