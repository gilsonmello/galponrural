<?php
/*
* Copyright (c) 2015 www.magebuzz.com 
*/
class Magebuzz_Improvedaddress_Adminhtml_Improvedaddress_CityController extends Mage_Adminhtml_Controller_Action {
	protected function _initAction() {
		$this->loadLayout()
			->_setActiveMenu('improvedaddress/city')
			->_addBreadcrumb(Mage::helper('adminhtml')->__('Items Manager'), Mage::helper('adminhtml')->__('Item Manager'));
		
		return $this;
	}   
 
	public function indexAction() {
		$this->_initAction()
			->renderLayout();
	}

	public function editAction() {
		$id = $this->getRequest()->getParam('id');
		$model = Mage::getModel('improvedaddress/city')->load($id);

		if ($model->getId() || $id == 0) {
			$data = Mage::getSingleton('adminhtml/session')->getFormData(true);
			if (!empty($data)) {
				$model->setData($data);
			}

			Mage::register('city_data', $model);

			$this->loadLayout();
			$this->_setActiveMenu('improvedaddress/city');

			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item Manager'), Mage::helper('adminhtml')->__('Item Manager'));
			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item News'), Mage::helper('adminhtml')->__('Item News'));

			$this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

			$this->_addContent($this->getLayout()->createBlock('improvedaddress/adminhtml_city_edit'))
				->_addLeft($this->getLayout()->createBlock('improvedaddress/adminhtml_city_edit_tabs'));

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
			$regionId = $request->getParam('region_id');
                        $distance = $request->getParam('distance');
                        $countryId = $request->getParam('country_id');
                        $dias_entrega = $request->getParam('dias_entrega');
                        $dias_entrega_motoboy = $request->getParam('dias_entrega_motoboy');
                        $observacoes = $request->getParam('observacoes');
                        $preco_fixo = $request->getParam('preco_fixo');
                        
			if (!$name || !$code) {
				Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please fill the required fields'));
				$this->_redirect('*/*/');
				return;
			}
			$cities = Mage::getModel('improvedaddress/city')->getCollection()
				->addFieldToFilter('code', $code)
				->addFieldToFilter('region_id', $regionId)
				->getAllIds();
			if (count($cities) > 0 && !in_array($id, $cities)) {
				Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('City/Region combination must be unique'));
				$this->_redirect('*/*/edit', array('id' => $id));
				return;
			}

			try {
				$city = Mage::getModel('improvedaddress/city');
				$city->setCityId($id)
					->setCode($code)
					->setRegionId($regionId)
					->setDefaultName($name)
                                        ->setDistance($distance)
                                        ->setCountryId($countryId)
                                        ->setDiasEntrega($dias_entrega)
                                        ->setDiasEntregaMotoboy($dias_entrega_motoboy)
                                        ->setObservacoes($observacoes)
                                        ->setPrecoFixo($preco_fixo)
					->save();

				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('City was successfully saved'));
				Mage::getSingleton('adminhtml/session')->setCityData(false);
				$this->_redirect('*/*/');
				return;
			} catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
				Mage::getSingleton('adminhtml/session')->setStateData($this->getRequest()->getPost());
				$this->_redirect('*/*/edit', array('region_id' => $this->getRequest()->getParam('id')));
				return;
			}
		}
		$this->_redirect('*/*/');
	}
 
	public function deleteAction() {
		if( $this->getRequest()->getParam('id') > 0 ) {
			try {
				$model = Mage::getModel('improvedaddress/city');
				 
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
		$cityIds = $this->getRequest()->getParam('city');
		if(!is_array($cityIds)) {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select item(s)'));
		} else {
			try {
				foreach ($cityIds as $cityId) {
					$city = Mage::getModel('improvedaddress/city')->load($cityId);
					$city->delete();
				}
				Mage::getSingleton('adminhtml/session')->addSuccess(
					Mage::helper('adminhtml')->__(
						'Total of %d record(s) were successfully deleted', count($cityIds)
					)
				);
			} catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
			}
		}
		$this->_redirect('*/*/index');
	}		
	
	protected function _isAllowed()	{
		return Mage::getSingleton('admin/session')->isAllowed('improvedaddress/city');
	}
	
	public function reloadRegionAction() {
		$countryId = $this->getRequest()->getParam('country_id', false);
		$options = '';
		$result = array('success' => false);
		if ($countryId) {			
			$collection = Mage::getModel('improvedaddress/region')->getCollection()
				->addFieldToFilter('country_id', $countryId);
			if (count($collection)) {
				$options .= '<option value="">'.Mage::helper('adminhtml')->__("Please Select").'</option>';
				foreach ($collection as $region) {
					$code = $region->getRegionId();
					$regionName = $region->getDefaultName();
					$options .= "<option value='$code'>$regionName</option>";
				}
				$result['success'] = true;
				$result['options'] = $options;
			}
		}		
		
		$this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
	}
}