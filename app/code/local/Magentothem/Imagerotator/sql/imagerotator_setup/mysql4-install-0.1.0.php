<?php

$installer = $this;
$setup = new Mage_Eav_Model_Entity_Setup('core_setup');
$installer->startSetup();
$setup->addAttribute(
    'catalog_product',
    'rotator_image',
    array (
        'group'             => 'Images',
        'type'              => 'varchar',
        'frontend'          => 'catalog/product_attribute_frontend_image',
        'label'             => 'Rotator Image',
        'input'             => 'media_image',
        'class'             => '',
        'source'            => '',
        'global'            => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
        'visible'           => true,
        'required'          => false,
        'user_defined'      => false,
        'default'           => '',
        'searchable'        => false,
        'filterable'        => false,
        'comparable'        => false,
        'visible_on_front'  => false,
        'unique'            => false,
    )
);
$installer->endSetup();