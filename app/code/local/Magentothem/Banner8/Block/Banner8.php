<?php
/*------------------------------------------------------------------------
# Websites: http://www.magentothem.com/
-------------------------------------------------------------------------*/ 
class Magentothem_Banner8_Block_Banner8 extends Mage_Core_Block_Template
{
	public function _prepareLayout()
    {
		return parent::_prepareLayout();
    }
    
     public function getBanner8()     
     { 
        if (!$this->hasData('banner8')) {
            $this->setData('banner8', Mage::registry('banner8'));
        }
        return $this->getData('banner8');
        
    }
	public function getDataBanner8()
    {
    	$resource = Mage::getSingleton('core/resource');
		$read= $resource->getConnection('core_read');
		$slideTable = $resource->getTableName('banner8');
		if (Mage::app()->isSingleStoreMode()){
			$select = $read->select()
			   ->from($slideTable,array('banner8_id','title','link','description','image','order', 'store_id','status'))
			   ->where('status=?',1);
		}else{
			$select = $read->select()
			   ->from($slideTable,array('banner8_id','title','link','description','image','order', 'store_id','status'))
			   ->where('find_in_set(0, store_id) OR find_in_set(?, store_id)', (int)(Mage::app()->getStore()->getId()))
			   ->where('status=?',1);
		}
		
		$slide = $read->fetchAll($select);	
		if ( $this->getConfig('animation') == 'animation1' ) {
			$array2 = $this->sorting_array($slide,'giam');
		} else {
			$array2 = $this->sorting_array($slide,'tang');
		}
		return 	$array2;		
    }
	function sorting_array ($array, $mode='tang'){ 
        if($mode=='tang'){ 
            $length = count($array); 
            for ($i = 0; $i < $length-1; $i++){ 
                $k = $i; 
                for ($j = $i+1; $j < $length; $j++) 
                    if ($array[$j]['order'] < $array[$k]['order'])  
                        $k = $j; 
                $t = $array[$k]; 
                $array[$k] = $array[$i]; 
                $array[$i] = $t; 
            } 
        } 
        if($mode=='giam'){ 
            $length = count($array); 
            for ($i = 0; $i < $length-1; $i++){ 
                $k = $i; 
                for ($j = $i+1; $j < $length; $j++) 
                    if ($array[$j]['order'] > $array[$k]['order'])  
                        $k = $j; 
                $t = $array[$k]; 
                $array[$k] = $array[$i]; 
                $array[$i] = $t; 
            } 
        } 
        return $array; 
    }
	public function getConfig($att) 
	{
		$config = Mage::getStoreConfig('banner8');
		if (isset($config['banner8_config']) ) {
			$value = $config['banner8_config'][$att];
			return $value;
		} else {
			throw new Exception($att.' value not set');
		}
	}
}