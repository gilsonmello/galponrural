<?php
/*
* Copyright (c) 2016 www.magebuzz.com 
*/

$installer = $this;

$installer->startSetup();
		
//Update attribute sort order
$attribute_region = Mage::getModel('eav/entity_attribute')->loadByCode('2', 'region');
$attribute_city = Mage::getModel('eav/entity_attribute')->loadByCode('2', 'city');
$table_name = Mage::getSingleton('core/resource')->getTableName('customer_eav_attribute');

$installer->run("
	UPDATE {$table_name} SET `sort_order`= 103 WHERE `attribute_id` = {$attribute_region->getId()};
	UPDATE {$table_name} SET `sort_order`= 104 WHERE `attribute_id` = {$attribute_city->getId()};
");

$installer->endSetup(); 