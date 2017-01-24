<?php
/*
* Copyright (c) 2016 www.magebuzz.com 
*/

$installer = $this;

$installer->startSetup();

$installer->run("

DROP TABLE IF EXISTS {$this->getTable('directory_region_city')};
CREATE TABLE {$this->getTable('directory_region_city')} (
  `city_id` int(11) unsigned NOT NULL auto_increment,
  `code` varchar(32) NOT NULL,
	`default_name` varchar(255) NOT NULL,  
  `region_id` int(11) unsigned NULL,	
  `distance` int(11) unsigned NULL,	
  `dias_entrega` int(11) unsigned NULL,	
  `dias_entrega_motoboy` int(11) unsigned NULL,
  `observacoes` text unsigned NULL,
  `preco_fixo` float(11) unsigned NULL,
  PRIMARY KEY (`city_id`),
	CONSTRAINT `FK_DIRECTORY_REGION_CITY_DIRECTORY_COUNTRY_REGION` FOREIGN KEY (`region_id`) REFERENCES `{$this->getTable('directory_country_region')}` (`region_id`) ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE {$this->getTable('sales_flat_quote_address')} ADD COLUMN `city_id` VARCHAR(255) CHARACTER SET utf8 DEFAULT NULL AFTER `fax`;
ALTER TABLE {$this->getTable('sales_flat_order_address')} ADD COLUMN `city_id` VARCHAR(255) CHARACTER SET utf8 DEFAULT NULL AFTER `fax`;

    ");
		
$setup = new Mage_Eav_Model_Entity_Setup('core_setup');
$entityTypeId     = $setup->getEntityTypeId('customer_address');
$attributeSetId   = $setup->getDefaultAttributeSetId($entityTypeId);
$attributeGroupId = $setup->getDefaultAttributeGroupId($entityTypeId, $attributeSetId);

$setup->addAttribute('customer_address', 'city_id', array(
    'input'         => 'hidden',
    'type'          => 'int',
    'label'         => 'City ID',
    'visible'       => 1,
    'required'      => 0, 
    'user_defined' => 0,
));

$oAttribute = Mage::getSingleton('eav/config')->getAttribute('customer_address', 'city_id');
$oAttribute->setData('used_in_forms', array('customer_register_address','customer_address_edit','adminhtml_customer_address'));
$oAttribute->save();

$installer->endSetup(); 