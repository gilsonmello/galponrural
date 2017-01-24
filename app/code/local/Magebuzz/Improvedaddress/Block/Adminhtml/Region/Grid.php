<?php
/*
* Copyright (c) 2015 www.magebuzz.com 
*/
class Magebuzz_Improvedaddress_Block_Adminhtml_Region_Grid extends Mage_Adminhtml_Block_Widget_Grid {
  public function __construct() {
		parent::__construct();
		$this->setId('regionGrid');
		$this->setDefaultSort('region_id');
		$this->setDefaultDir('ASC');
		$this->setSaveParametersInSession(true);
  }

  protected function _prepareCollection() {
		$collection = Mage::getModel('improvedaddress/region')->getCollection();
    $this->setCollection($collection);

		$this->setLocales(Mage::helper('improvedaddress')->getLocales());

		return parent::_prepareCollection();
  }

  protected function _prepareColumns() {
		$this->addColumn('region_id', array(
			'header'    => Mage::helper('improvedaddress')->__('ID'),
			'align'     =>'right',
			'width'     => '50px',
			'index'     => 'region_id',
		));
		
		$this->addColumn(
			'country_id', array(
				'header' => Mage::helper('improvedaddress')->__('Country Code'),
				'align'  => 'left',
				'width'  => '110px',
				'index'  => 'country_id',
				'type'   => 'country',
			)
		);
		
		$this->addColumn(
			'code', array(
				'header'           => Mage::helper('improvedaddress')->__('Region Code'),
				'align'            => 'left',
				'width'            => '110px',
				'index'            => 'code',
				//'editable' =>true,
				'column_css_class' => 'code_td'
			)
		);
		
		$this->addColumn(
			'default_name', array(
				'header'           => Mage::helper('improvedaddress')->__('Default Name'),
				'align'            => 'left',
				'width'            => '110px',
				'index'            => 'default_name',
				//'editable' =>true,
				'column_css_class' => 'default_name'
			)
		);
		$this->addColumn(
			'name_locale', array(
				'header'           => Mage::helper('improvedaddress')->__('Name in Locale'),
				'align'            => 'left',
				'width'            => '110px',
				'index'            => 'region_id',
				'sortable'         => false,
				'filter'           => false,
				'renderer'  =>     'Magebuzz_Improvedaddress_Block_Adminhtml_Region_Renderer_Name',
				//'editable' =>true,
				'column_css_class' => 'name_locale'
			)
		);
				
	  
		$this->addColumn('action',
			array(
				'header'    =>  Mage::helper('improvedaddress')->__('Action'),
				'width'     => '100',
				'type'      => 'action',
				'getter'    => 'getId',
				'actions'   => array(
					array(
						'caption'   => Mage::helper('improvedaddress')->__('Edit'),
						'url'       => array('base'=> '*/*/edit'),
						'field'     => 'region_id'
					)
				),
				'filter'    => false,
				'sortable'  => false,
				'index'     => 'stores',
				'is_system' => true,
		));
	  
    return parent::_prepareColumns();
  }

	protected function _prepareMassaction() {
		$this->setMassactionIdField('region_id');
		$this->getMassactionBlock()->setFormFieldName('region');
		$this->getMassactionBlock()->setUseSelectAll(false);
		$this->getMassactionBlock()->addItem(
			'delete', 
			array(
				'label' => Mage::helper('improvedaddress')->__('Delete'),
				'url'   => $this->getUrl('*/*/massDelete', array('_current' => true)),
			)
		);
		
		return $this;
	}

  public function getRowUrl($row) {
    return $this->getUrl('*/*/edit', array('id' => $row->getRegionId()));
  }
}