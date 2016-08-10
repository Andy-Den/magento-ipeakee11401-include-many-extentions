<?php

// Adds in new attribute for related

/* @var $installer Balance_Accessory_Model_Resource_Setup */
$installer = $this;
$installer->startSetup();

$entityTypeId = $installer->getEntityTypeId('catalog_product');

$setName   = 'Accessories';
$setCode   = 'accessories';
$groupName = 'Suits Models';

//get set id and save group
$setId = $installer->getAttributeSetId($entityTypeId, $setName);
$installer->addAttributeGroup($entityTypeId, $setId, $groupName, 20);


// add the brand attribute
try {
    $installer->addAttribute('catalog_product', Balance_Accessory_Helper_Data::ATTR_CODE_SUITS_MODELS, array(
        'attribute_set'              => $setCode,
        'group'                      => $groupName,
        'label'                      => 'Suits Models',
        'type'                       => 'text',
        'input'                      => 'textarea',
        'backend'                    => '',
        'frontend'                   => '',
        'source'                     => '',
        'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
        'visible'                    => true,
        'required'                   => false,
        'is_user_defined'            => true,
        'searchable'                 => true,
        'filterable'                 => false,
        'comparable'                 => false,
        'option'                     => array(),
        'visible_on_front'           => false,
        'visible_in_advanced_search' => false,
        'unique'                     => false,
    ));
}
catch (Exception $ex) {
    // already exists
}


$installer->endSetup();
