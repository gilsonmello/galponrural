<?php

require_once('app/Mage.php');
Mage::app()->setCurrentStore(Mage::getModel('core/store')->load(Mage_Core_Model_App::ADMIN_STORE_ID));
$installer = new Mage_Sales_Model_Mysql4_Setup;
$attribute1 = array(
    'type' => 'varchar',
    'backend_type' => 'varchar',
    'frontend_input' => 'varchar',
    'is_user_defined' => false,
    'label' => 'Bancard Status',
    'visible' => false,
    'required' => false,
    'user_defined' => false,
    'searchable' => false,
    'filterable' => false,
    'comparable' => false,
    'default' => ''
);
$attribute2 = array(
    'type' => 'varchar',
    'backend_type' => 'varchar',
    'frontend_input' => 'varchar',
    'is_user_defined' => false,
    'label' => 'Bancard Message',
    'visible' => false,
    'required' => false,
    'user_defined' => false,
    'searchable' => false,
    'filterable' => false,
    'comparable' => false,
    'default' => ''
);
$attribute3 = array(
    'type' => 'varchar',
    'backend_type' => 'varchar',
    'frontend_input' => 'varchar',
    'is_user_defined' => false,
    'label' => 'Bancard Status Code',
    'visible' => false,
    'required' => false,
    'user_defined' => false,
    'searchable' => false,
    'filterable' => false,
    'comparable' => false,
    'default' => ''
);
$attribute4 = array(
    'type' => 'text',
    'backend_type' => 'text',
    'frontend_input' => 'text',
    'is_user_defined' => false,
    'label' => 'Bancard Response',
    'visible' => false,
    'required' => false,
    'user_defined' => false,
    'searchable' => false,
    'filterable' => false,
    'comparable' => false,
    'default' => ''
);
$attribute5 = array(
    'type' => 'varchar',
    'backend_type' => 'varchar',
    'frontend_input' => 'varchar',
    'is_user_defined' => false,
    'label' => 'Bancard Extended Message',
    'visible' => false,
    'required' => false,
    'user_defined' => false,
    'searchable' => false,
    'filterable' => false,
    'comparable' => false,
    'default' => ''
);
$installer->addAttribute('order', 'bancard_status', $attribute1);
$installer->addAttribute('quote', 'bancard_status', $attribute1);
$installer->addAttribute('order', 'bancard_message', $attribute2);
$installer->addAttribute('quote', 'bancard_message', $attribute2);
$installer->addAttribute('order', 'bancard_status_code', $attribute3);
$installer->addAttribute('quote', 'bancard_status_code', $attribute3);
$installer->addAttribute('order', 'bancard_response', $attribute4);
$installer->addAttribute('quote', 'bancard_response', $attribute4);
$installer->addAttribute('order', 'bancard_extended_message', $attribute5);
$installer->addAttribute('quote', 'bancard_extended_message', $attribute5);
$installer->endSetup();

echo 'OK';