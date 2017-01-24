<?php
class Magentothem_Producttabs_Block_Producttabs extends Mage_Core_Block_Template
{

    public function getProducttabsCfg($cfg)
    {
        return Mage::helper('producttabs')->getProducttabsCfg($cfg);
    }

    public function getProductCfg($cfg)
    {
        return Mage::helper('producttabs')->getProductCfg($cfg);
    }

    public function getTypeDefault()
    {
        $cfg   = $this->getProductCfg('product_type');
        $cfg   = explode(',', $cfg);
        return $cfg[0];
    }

    public function sortTabs()
    {
        return $this->getProducttabsCfg('sort_name');
    }

	public function getTabs()
	{
        $types = Mage::getSingleton("producttabs/system_config_type")->toOptionArray();
        $cfg = $this->getProductCfg('product_type');
        $cfg = explode(',', $cfg);
        $tabs = array();
        foreach ($types as $type) {
            if(in_array($type['value'], $cfg)){
                $tabs[$type['value']] = $type['label'];
            }
        }

        return $tabs;
	}
	
	public function getBestsellers(){
        $collection = Mage::getModel('catalog/product')->getCollection();
        $collection->addAttributeToSelect('*')->addStoreFilter();
        $orderItems = Mage::getSingleton('core/resource')->getTableName('sales/order_item');
        $orderMain =  Mage::getSingleton('core/resource')->getTableName('sales/order');
        $collection->getSelect()
            ->join(array('items' => $orderItems), "items.product_id = e.entity_id", array('count' => 'SUM(items.qty_ordered)'))
            ->join(array('trus' => $orderMain), "items.order_id = trus.entity_id", array())
            ->group('e.entity_id')
            ->order('count DESC');
		Mage::getSingleton('catalog/product_status')->addVisibleFilterToCollection($collection);
        Mage::getSingleton('catalog/product_visibility')->addVisibleInCatalogFilterToCollection($collection);
        $collection->setPageSize(Mage::helper('producttabs')->getProductCfg('product_number'));

        if(Mage::getStoreConfig('catalog/frontend/flat_catalog_product'))
        {
            // fix error mat image vs name while Enable useFlatCatalogProduct
            foreach ($collection as $product) 
            {
                $productId = $product->_data['entity_id'];
                $_product = Mage::getModel('catalog/product')->load($productId); //Product ID
                $product->_data['name']        = $_product->getName();
                $product->_data['thumbnail']   = $_product->getThumbnail();
                $product->_data['small_image'] = $_product->getSmallImage();
            }            
        }
        return $collection;
    }
	
	public function getFeatured(){
		$collection = Mage::getResourceModel('catalog/product_collection')
			->addAttributeToSelect('*')
			->addMinimalPrice()
			->addUrlRewrite()
			->addTaxPercents()			
			->addStoreFilter()
			//->addAttributeToFilter("featured", 1);		
			->addFieldToFilter(array(
			array('attribute'=>'featured','eq'=>'1'),
			));

        Mage::getSingleton('catalog/product_status')->addVisibleFilterToCollection($collection);
        Mage::getSingleton('catalog/product_visibility')->addVisibleInCatalogFilterToCollection($collection);
        $collection->setPageSize(Mage::helper('producttabs')->getProductCfg('product_number'));
        return $collection; 
    }
    
    public function getPromotion(){
		$collection = Mage::getResourceModel('catalog/product_collection')
			->addAttributeToSelect('*')
			->addMinimalPrice()
			->addUrlRewrite()
			->addTaxPercents()			
			->addStoreFilter()
			//->addAttributeToFilter("featured", 1);		
			->addFieldToFilter(array(
			array('attribute'=>'promotion','eq'=>'1'),
			));

        Mage::getSingleton('catalog/product_status')->addVisibleFilterToCollection($collection);
        Mage::getSingleton('catalog/product_visibility')->addVisibleInCatalogFilterToCollection($collection);
        $collection->setPageSize(Mage::helper('producttabs')->getProductCfg('product_number'));
        return $collection; 
    }
	
	public function getMostviewed(){
		$storeId    = Mage::app()->getStore()->getId();
		$collection = Mage::getResourceModel('reports/product_collection')
            ->addAttributeToSelect('*')
			->addMinimalPrice()
			->addUrlRewrite()
			->addTaxPercents()			
            ->addAttributeToSelect(array('name', 'price', 'small_image')) //edit to suit tastes
            ->setStoreId($storeId)
            ->addStoreFilter($storeId)
            ->addViewsCount()
            ;			
        Mage::getSingleton('catalog/product_status')->addVisibleFilterToCollection($collection);
        Mage::getSingleton('catalog/product_visibility')->addVisibleInCatalogFilterToCollection($collection);
        $this->setProductCollection($collection);
		$collection->setPageSize(Mage::helper('producttabs')->getProductCfg('product_number'));

		if(Mage::getStoreConfig('catalog/frontend/flat_catalog_product'))
        {
            // fix error mat image vs name while Enable useFlatCatalogProduct
            foreach ($collection as $product) 
            {
                $productId = $product->_data['entity_id'];
                $_product = Mage::getModel('catalog/product')->load($productId); //Product ID
                $product->_data['name']        = $_product->getName();
                $product->_data['thumbnail']   = $_product->getThumbnail();
                $product->_data['small_image'] = $_product->getSmallImage();
            }            
        }
        
        return $collection;
    }
	
	public function getNew() {
		$todayDate  = Mage::app()->getLocale()->date()->toString(Varien_Date::DATETIME_INTERNAL_FORMAT);
		$collection = Mage::getResourceModel('catalog/product_collection')
			->addAttributeToSelect('*')
			->addMinimalPrice()
			->addUrlRewrite()
			->addTaxPercents()
			->addStoreFilter()
			->addAttributeToFilter('news_from_date', array('date'=>true, 'to'=> $todayDate))
			->addAttributeToFilter(array(array('attribute'=>'news_to_date', 'date'=>true, 'from'=>$todayDate), array('attribute'=>'news_to_date', 'is' => new Zend_Db_Expr('null'))),'','left')
			->addAttributeToSort('news_from_date','desc');		
        Mage::getSingleton('catalog/product_status')->addVisibleFilterToCollection($collection);
        Mage::getSingleton('catalog/product_visibility')->addVisibleInCatalogFilterToCollection($collection);
        $collection->setPageSize(Mage::helper('producttabs')->getProductCfg('product_number'));
        return $collection;
    }
	
	public function getRandom() {
		$collection = Mage::getResourceModel('catalog/product_collection')
			->addAttributeToSelect('*');
		Mage::getModel('catalog/layer')->prepareProductCollection($collection);
		$collection->getSelect()->order('rand()');
		$collection->addAttributeToSelect('*')
			->addMinimalPrice()
			->addUrlRewrite()
			->addTaxPercents()
			->addStoreFilter();	
        Mage::getSingleton('catalog/product_status')->addVisibleFilterToCollection($collection);
        Mage::getSingleton('catalog/product_visibility')->addVisibleInCatalogFilterToCollection($collection);
        $collection->setPageSize(Mage::helper('producttabs')->getProductCfg('product_number'));
        return $collection;
    }
	
	public function getSale(){
		$todayDate  = Mage::app()->getLocale()->date()->toString(Varien_Date::DATETIME_INTERNAL_FORMAT);
		$collection = Mage::getResourceModel('catalog/product_collection')
			->addAttributeToSelect('*')
			->addMinimalPrice()
			->addUrlRewrite()
			->addTaxPercents()			
			->addStoreFilter()
			->addAttributeToFilter('special_from_date', array('date'=>true, 'to'=> $todayDate))
			->addAttributeToFilter(array(array('attribute'=>'special_to_date', 'date'=>true, 'from'=>$todayDate), array('attribute'=>'special_to_date', 'is' => new Zend_Db_Expr('null'))),'','left');
			
        Mage::getSingleton('catalog/product_status')->addVisibleFilterToCollection($collection);
        Mage::getSingleton('catalog/product_visibility')->addVisibleInCatalogFilterToCollection($collection);
		$collection->setPageSize(Mage::helper('producttabs')->getProductCfg('product_number'));
        // get Sale off
        foreach ($collection as $key => $product) {
            if($product->getSpecialPrice() == '') $collection->removeItemByKey($key); // remove product not set SpecialPrice
            if($product->getSpecialPrice() && $product->getSpecialPrice() >= $product->getPrice())
            {
               $collection->removeItemByKey($key); // remove product price increase
            }
        }
        return $collection;
    }

}