<?php
class Magentothem_Relatedslider_Block_Catalog_Product_List_Related extends Mage_Catalog_Block_Product_List_Related 
{
    const XML_PATH_ITEM_LIMIT   = 'relatedslider/relatedslider_config/qty';
    
    protected $_columnCount = 4;
    
    public function getItemLimit($type = '')
    {
        return Mage::getStoreConfig(self::XML_PATH_ITEM_LIMIT);
    }
    /**
     * Get relevant path to template
     *
     * @return string
     */
    public function getTemplate()
    {
        return 'magentothem/relatedslider/related.phtml';
    }
	
	public function getConfig($att) 
	{
		$config = Mage::getStoreConfig('relatedslider');
		if (isset($config['relatedslider_config']) ) {
			$value = $config['relatedslider_config'][$att];
			return $value;
		} else {
			throw new Exception($att.' value not set');
		}
	}

 

    public function getRowCount()
    {
        return ceil(count($this->getItems())/$this->getColumnCount());
    }

    public function setColumnCount($columns)
    {
        if (intval($columns) > 0) {
            $this->_columnCount = intval($columns);
        }
        return $this;
    }

    public function getColumnCount()
    {
        return $this->_columnCount;
    }
}